<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevClubUser extends Pivot
{
    use SoftDeletes;

    protected $connection = 'mysql_prev';

    protected $table = 'club2user';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    protected $guarded = [];

    /**
     * Casts for database fields
     *
     * Note: In the DB, all these are varchar columns:
     * - type: 'Main' or 'Sub'
     * - status: 'active' (always, currently not used for logic)
     * - payment_status: '0', '1', or NULL (as strings)
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'club_id' => 'integer',
        'forbidden' => 'boolean',
        'expire_date' => 'datetime',
    ];

    protected $appends = ['is_active', 'computed_status', 'type_label', 'payment_status_code', 'expiration_human'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'user_id', 'id');
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(PrevClub::class, 'club_id', 'id');
    }

    /**
     * Check if membership is truly active based on multiple conditions
     * Note: DB 'status' column is always 'active' string, so we check other conditions
     */
    public function isActive(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes): bool {
                // Check if expire_date is in the future
                $expireDate = $attributes['expire_date'] ?? null;
                $notExpired = $expireDate && $expireDate >= now()->format('Y-m-d H:i:s');

                // Check if payment is ok ('1' as string or null)
                $paymentStatus = $attributes['payment_status'] ?? null;
                $paymentOk = $paymentStatus === null || $paymentStatus === '1' || $paymentStatus == '1';

                // Check if not forbidden
                $notForbidden = empty($attributes['forbidden']);

                return $notExpired && $paymentOk && $notForbidden;
            }
        );
    }

    /**
     * Computed status based on actual conditions
     * Returns: 1 (Active), 0 (Inactive/Forbidden), 2 (Pending payment), 3 (Expired)
     */
    public function computedStatus(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes): int {
                $expireDate = $attributes['expire_date'] ?? null;
                $paymentStatus = $attributes['payment_status'] ?? null;
                $forbidden = $attributes['forbidden'] ?? false;

                // Check if forbidden first
                if ($forbidden) {
                    return 0; // Inactive/Forbidden
                }

                // Check if expired
                if ($expireDate && $expireDate < now()->format('Y-m-d H:i:s')) {
                    return 3; // Expired
                }

                // Check payment status (string '0' or '1' or null)
                if ($paymentStatus === '0' || $paymentStatus == '0') {
                    return 2; // Pending payment
                }

                // All good
                return 1; // Active
            }
        );
    }

    /**
     * Get membership type label
     * DB has: 'Main' or 'Sub' as varchar
     */
    public function typeLabel(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes): string {
                $type = $attributes['type'] ?? '';
                return match (strtolower($type)) {
                    'main' => __('Main'),
                    'sub' => __('Sub'),
                    default => __('Unknown'),
                };
            }
        );
    }

    /**
     * Get payment status as integer code for consistency
     * DB has: '0', '1', or NULL as varchar
     * Returns: 0 (Pending), 1 (Paid), null (N/A)
     */
    public function paymentStatusCode(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes): ?int {
                $paymentStatus = $attributes['payment_status'] ?? null;

                if ($paymentStatus === null) {
                    return null; // N/A
                }

                return ($paymentStatus == '1') ? 1 : 0;
            }
        );
    }

    /**
     * Get days until expiration
     * Returns positive number for future dates, negative for past dates
     */
    public function daysUntilExpiration(): ?int
    {
        if (!$this->expire_date) {
            return null;
        }

        return now()->diffInDays($this->expire_date, false);
    }

    /**
     * Get absolute days until/since expiration
     */
    public function daysUntilExpirationAbs(): ?int
    {
        if (!$this->expire_date) {
            return null;
        }

        return now()->diffInDays($this->expire_date, true);
    }

    /**
     * Check if membership is expiring soon
     */
    public function isExpiringSoon(int $days = 30): bool
    {
        $daysUntil = $this->daysUntilExpiration();

        if ($daysUntil === null) {
            return false;
        }

        return $daysUntil > 0 && $daysUntil <= $days;
    }

    /**
     * Get human-readable expiration status
     */
    public function expirationHuman(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                if (!$this->expire_date) {
                    return __('No expiration date');
                }

                $days = $this->daysUntilExpiration();

                if ($days === null) {
                    return __('No expiration date');
                }

                if ($days > 0) {
                    // Future date - will expire
                    return $this->expire_date->diffForHumans(['parts' => 1, 'short' => false]);
                } elseif ($days === 0) {
                    return __('Expires today');
                } else {
                    // Past date - expired
                    return __('Expired') . ' ' . $this->expire_date->diffForHumans(['parts' => 1, 'short' => false]);
                }
            }
        );
    }

    /**
     * Get expiration status color
     * Returns: 'success', 'warning', 'danger', 'gray'
     */
    public function getExpirationColor(): string
    {
        $days = $this->daysUntilExpiration();

        if ($days === null) {
            return 'gray';
        }

        // Expired
        if ($days <= 0) {
            return 'danger';
        }

        // Expiring soon (within 30 days)
        if ($days <= 30) {
            return 'warning';
        }

        // All good
        return 'success';
    }
}
