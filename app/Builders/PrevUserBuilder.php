<?php

declare(strict_types=1);

namespace App\Builders;

use App\Services\Legacy\PrevUserService;
use Illuminate\Database\Eloquent\Builder;

class PrevUserBuilder extends Builder
{
    public const string RESOLUTION_PRIORITY_SQL = "CASE record_type WHEN 'Native' THEN 0 WHEN 'Owners' THEN 1 WHEN 'Members' THEN 2 ELSE 3 END";

    public function searchName(?string $fullTerm): self
    {
        if ($fullTerm === null || $fullTerm === '') {
            return $this;
        }

        $tokens = preg_split('/[\s,]+/u', $fullTerm, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($tokens as $token) {
            $tokenLike = '%' . $token . '%';

            $this->where(function (Builder $query) use ($tokenLike): void {
                $query->whereRaw("CONCAT_WS(' ', first_name, last_name) LIKE ?", [$tokenLike])
                    ->orWhereRaw("CONCAT_WS(' ', first_name_en, last_name_en) LIKE ?", [$tokenLike]);
            });
        }

        return $this;
    }

    public function whereRecordType(?string $recordType): self
    {
        if ($recordType === null || $recordType === '' || $recordType === 'all') {
            return $this;
        }

        return match ($recordType) {
            'Native' => $this->whereNative(),
            'Members' => $this->whereMembers(),
            'Owners' => $this->whereOwners(),
            'without' => $this->whereWithoutRecordType(),
            default => $this->where('record_type', $recordType),
        };
    }

    public function whereNative(): self
    {
        return $this->where('record_type', 'Native');
    }

    public function whereMembers(): self
    {
        return $this->where('record_type', 'Members');
    }

    public function whereOwners(): self
    {
        return $this->where('record_type', 'Owners');
    }

    public function whereWithoutRecordType(): self
    {
        return $this->whereNull('record_type');
    }

    public function whereOwnerCode(int|string|null $ownerCode): self
    {
        if (blank($ownerCode)) {
            return $this->whereRaw('1 = 0');
        }

        return $this->where('owner_code', $ownerCode);
    }

    public function whereEmail(?string $email): self
    {
        $normalisedEmail = PrevUserService::normaliseEmail($email);

        if ($normalisedEmail === null) {
            return $this->whereRaw('1 = 0');
        }

        return $this->whereRaw('LOWER(TRIM(email)) = ?', [$normalisedEmail]);
    }

    public function whereNormalisedPhone(string|int|null $phone): self
    {
        $normalisedPhone = PrevUserService::normalisePhone($phone);

        if ($normalisedPhone === null) {
            return $this->whereRaw('1 = 0');
        }

        return $this->where(function (Builder $query) use ($normalisedPhone): void {
            $query->whereRaw(PrevUserService::normalisedIsraeliMobileSql('mobile_phone') . ' = ?', [$normalisedPhone])
                ->orWhereRaw(PrevUserService::normalisedIsraeliMobileSql('phone') . ' = ?', [$normalisedPhone]);
        });
    }

    public function whereNormalisedMobilePhone(string|int|null $mobilePhone): self
    {
        $normalisedPhone = PrevUserService::normalisePhone($mobilePhone);

        if ($normalisedPhone === null) {
            return $this->whereRaw('1 = 0');
        }

        return $this->whereRaw(PrevUserService::normalisedIsraeliMobileSql('mobile_phone') . ' = ?', [$normalisedPhone]);
    }

    public function whereNormalisedPhoneColumn(string $column, string|int|null $phone): self
    {
        $normalisedPhone = PrevUserService::normalisePhone($phone);

        if ($normalisedPhone === null) {
            return $this->whereRaw('1 = 0');
        }

        return $this->whereRaw(PrevUserService::normalisedIsraeliMobileSql($column) . ' = ?', [$normalisedPhone]);
    }

    public function whereResolvesIdentity(
        string|int|null $mobilePhone = null,
        string|int|null $phone = null,
        ?string         $email = null,
        int|string|null $ownerCode = null,
    ): self
    {
        [$resolutionSql, $bindings] = $this->identityResolutionCase(
            mobilePhone: $mobilePhone,
            phone: $phone,
            email: $email,
            ownerCode: $ownerCode,
        );

        if ($bindings === []) {
            return $this->whereRaw('1 = 0');
        }

        return $this->whereRaw("{$resolutionSql} < 999", $bindings);
    }

    public function orderByIdentityResolution(
        string|int|null $mobilePhone = null,
        string|int|null $phone = null,
        ?string         $email = null,
        int|string|null $ownerCode = null,
    ): self
    {
        [$resolutionSql, $bindings] = $this->identityResolutionCase(
            mobilePhone: $mobilePhone,
            phone: $phone,
            email: $email,
            ownerCode: $ownerCode,
        );

        if ($bindings === []) {
            return $this;
        }

        return $this->orderByRaw($resolutionSql, $bindings);
    }

    public function orderByResolutionPriority(): self
    {
        return $this->orderByRaw(self::RESOLUTION_PRIORITY_SQL)
            ->orderBy('id');
    }

    /**
     * @return array{0: string, 1: array<int, mixed>}
     */
    protected function identityResolutionCase(
        string|int|null $mobilePhone = null,
        string|int|null $phone = null,
        ?string         $email = null,
        int|string|null $ownerCode = null,
    ): array
    {
        $normalisedMobilePhone = PrevUserService::normalisePhone($mobilePhone);
        $normalisedPhone = PrevUserService::normalisePhone($phone);
        $normalisedEmail = PrevUserService::normaliseEmail($email);
        $hasOwnerCode = filled($ownerCode);
        $bindings = [];
        $cases = [];
        $priority = 0;

        if ($hasOwnerCode) {
            $cases[] = "WHEN owner_code = ? THEN {$priority}";
            $bindings[] = $ownerCode;
            $priority++;
        }

        if ($normalisedMobilePhone !== null) {
            $cases[] = 'WHEN ' . PrevUserService::normalisedIsraeliMobileSql('mobile_phone') . " = ? THEN {$priority}";
            $bindings[] = $normalisedMobilePhone;
            $priority++;

            $cases[] = 'WHEN ' . PrevUserService::normalisedIsraeliMobileSql('phone') . " = ? THEN {$priority}";
            $bindings[] = $normalisedMobilePhone;
            $priority++;
        }

        if ($normalisedPhone !== null) {
            $cases[] = 'WHEN ' . PrevUserService::normalisedIsraeliMobileSql('mobile_phone') . " = ? THEN {$priority}";
            $bindings[] = $normalisedPhone;
            $priority++;

            $cases[] = 'WHEN ' . PrevUserService::normalisedIsraeliMobileSql('phone') . " = ? THEN {$priority}";
            $bindings[] = $normalisedPhone;
            $priority++;
        }

        if ($normalisedEmail !== null) {
            $cases[] = "WHEN LOWER(TRIM(email)) = ? THEN {$priority}";
            $bindings[] = $normalisedEmail;
        }

        if ($cases === []) {
            return ['CASE WHEN 1 = 1 THEN 999 END', []];
        }

        return ['CASE ' . implode(' ', $cases) . ' ELSE 999 END', $bindings];
    }
}
