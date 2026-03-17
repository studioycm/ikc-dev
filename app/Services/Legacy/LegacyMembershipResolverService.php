<?php

namespace App\Services\Legacy;

use App\Models\PrevClub;
use App\Models\PrevClubUser;
use App\Models\PrevDog;
use App\Models\PrevUser;

class LegacyMembershipResolverService
{
    protected array $membershipCache = [];

    protected string $currencySymbol = '₪';

    public function resolveClubForDog(PrevDog $dog): ?PrevClub
    {
        return $dog->breedClub();
    }

    public function resolveMembershipForUserAndClub(?int $prevUserId, ?int $clubId): ?PrevClubUser
    {
        if (!$prevUserId || !$clubId) {
            return null;
        }

        $cacheKey = "{$prevUserId}:{$clubId}";

        if (array_key_exists($cacheKey, $this->membershipCache)) {
            return $this->membershipCache[$cacheKey];
        }

        return $this->membershipCache[$cacheKey] = PrevClubUser::query()
            ->where('user_id', $prevUserId)
            ->where('club_id', $clubId)
            ->whereNull('deleted_at')
            ->orderByDesc('expire_date')
            ->first();
    }

    public function isActiveMembership(?PrevClubUser $membership): bool
    {
        if (!$membership) {
            return false;
        }

        return !$membership->forbidden
            && ($membership->status === 'active')
            && ($membership->expire_date?->gte(now()))
            && ($membership->payment_status === 1);
    }

    public function resolveSummaryForDogAndUser(PrevDog $dog, ?int $prevUserId = null): array
    {
        $club = $this->resolveClubForDog($dog);
        $prevUserId ??= auth()->user()?->prev_user_id;

        if (!$club) {
            return [
                'club' => null,
                'club_name' => null,
                'membership' => null,
                'has_membership' => false,
                'has_active_membership' => false,
                'prices' => null,
                'status_key' => 'no_club',
                'status_label' => __('No breed club found'),
            ];
        }

        if (!$prevUserId) {
            return [
                'club' => $this->summarizeClub($club),
                'club_name' => $this->resolveClubName($club),
                'membership' => null,
                'has_membership' => false,
                'has_active_membership' => false,
                'prices' => $this->resolvePrices($club, 'no_user'),
                'status_key' => 'no_user',
                'status_label' => __('No related user'),
            ];
        }

        $membership = $this->resolveMembershipForUserAndClub($prevUserId, $club->id);
        $statusKey = $this->resolveMembershipStatus($membership);

        return [
            'club' => $this->summarizeClub($club),
            'club_name' => $this->resolveClubName($club),
            'membership' => $this->summarizeMembership($membership),
            'has_membership' => $membership !== null,
            'has_active_membership' => $statusKey === 'active',
            'prices' => $this->resolvePrices($club, $statusKey),
            'status_key' => $statusKey,
            'status_label' => $this->resolveStatusLabel($statusKey),
        ];
    }

    protected function resolveMembershipStatus(?PrevClubUser $membership): string
    {
        if (!$membership) {
            return 'not_member';
        }

        if ($this->isActiveMembership($membership)) {
            return 'active';
        }

        if ($membership->forbidden) {
            return 'forbidden';
        }

        if ($membership->expire_date?->lt(now())) {
            return 'expired';
        }

        if ((int)$membership->payment_status !== 1) {
            return 'payment_pending';
        }

        return 'inactive';
    }

    protected function resolveStatusLabel(string $statusKey): string
    {
        return match ($statusKey) {
            'active' => __('Active membership'),
            'expired' => __('Expired membership'),
            'inactive' => __('Inactive membership'),
            'forbidden' => __('Forbidden membership'),
            'payment_pending' => __('Membership payment pending'),
            'not_member' => __('No membership'),
            'no_user' => __('No related user'),
            default => __('No breed club found'),
        };
    }

    protected function summarizeClub(PrevClub $club): array
    {
        return [
            'id' => $club->id,
            'code' => $club->ClubCode,
            'name' => $this->resolveClubName($club),
            'name_secondary' => $club->EngName,
            'address' => $club->full_address,
            'email' => $club->Email,
            'manager_name' => $club->ManagerName,
            'manager_email' => $club->ManagerEmail,
            'manager_mobile' => $club->ManagerMobile,
            'fees' => [
                'registration_price' => $this->moneyMeta($club->RegistrationPrice),
                'member_price' => $this->moneyMeta($club->GeneralReviewFee),
                'non_member_price' => $this->moneyMeta($club->Breed_NonReg_Price),
                'dog_review_fee' => $this->moneyMeta($club->DogReviewFee),
                'per_dog_non_member_price' => $this->moneyMeta($club->PerDog_NonReg_Price),
                'test_price' => $this->moneyMeta($club->TestPrice),
            ],
        ];
    }

    protected function summarizeMembership(?PrevClubUser $membership): ?array
    {
        if (!$membership) {
            return null;
        }

        return [
            'id' => $membership->id,
            'type' => $membership->type,
            'type_label' => $membership->type_label,
            'status' => $membership->status,
            'computed_status' => $membership->computed_status,
            'payment_status' => $membership->payment_status,
            'payment_status_code' => $membership->payment_status_code,
            'payment_status_label' => $this->resolvePaymentStatusLabel($membership->payment_status_code),
            'expire_date' => $membership->expire_date?->toDateString(),
            'expire_date_display' => $membership->expire_date?->format('d/m/Y'),
            'expiration_human' => $membership->expiration_human,
            'forbidden' => (bool)$membership->forbidden,
            'breeder_code' => $membership->breeder_code,
        ];
    }

    protected function resolvePrices(PrevClub $club, string $statusKey): array
    {
        $memberPrice = $this->moneyMeta($club->GeneralReviewFee);
        $nonMemberPrice = $this->moneyMeta($club->Breed_NonReg_Price);
        $finalKey = $statusKey === 'active' ? 'member' : 'non_member';
        $finalPrice = $finalKey === 'member' ? $memberPrice : $nonMemberPrice;

        $discountAmount = null;

        if ($club->GeneralReviewFee !== null && $club->Breed_NonReg_Price !== null) {
            $discountAmount = max(0, (int)$club->Breed_NonReg_Price - (int)$club->GeneralReviewFee);
        }

        return [
            'member' => $memberPrice,
            'non_member' => $nonMemberPrice,
            'final' => $finalPrice,
            'final_key' => $finalKey,
            'final_label' => $finalKey === 'member'
                ? __('Member Price')
                : __('Standard Price'),
            'discount' => $this->moneyMeta($discountAmount),
        ];
    }

    protected function resolveClubName(PrevClub $club): ?string
    {
        return $club->Name ?: $club->EngName;
    }

    protected function resolvePaymentStatusLabel(?int $paymentStatusCode): string
    {
        return match ($paymentStatusCode) {
            1 => __('Paid'),
            0 => __('Pending'),
            default => __('Unknown'),
        };
    }

    protected function moneyMeta(?int $amount): array
    {
        return [
            'amount' => $amount,
            'formatted' => $amount === null
                ? null
                : $this->currencySymbol . number_format($amount),
        ];
    }

    public function eligibleMembershipTypes(
        PrevDog $dog,
        ?int $prevUserId = null,
        ?array $strategies = null,
    ): array
    {
        $prevUserId ??= auth()->user()?->prev_user_id;
        $strategies ??= array_keys(config('breeding_checks.membership_types', []));

        $dog->loadMissing('owners', 'breed.clubs');

        $club = $this->resolveClubForDog($dog);

        if (!$club) {
            return [];
        }

        $allTypes = [];

        foreach ($strategies as $strategy) {
            $eligible = match ($strategy) {
                'owner_breed_club' => $this->ownerHasBreedClubMembership($prevUserId, $club->id),
                'owner_any_club' => $this->ownerHasAnyClubMembership($prevUserId),
                'at_least_one_co_owner_breed_club' => $this->atLeastOneCoOwnerHasBreedClubMembership($dog, $prevUserId, $club->id),
                'all_owners_breed_club' => $this->allOwnersHaveBreedClubMembership($dog, $club->id),
                default => false,
            };

            if (!$eligible) {
                continue;
            }

            $config = config("breeding_checks.membership_types.{$strategy}", []);

            $allTypes[] = [
                'key' => $strategy,
                'label' => __($config['label'] ?? $strategy),
                'color' => $config['color'] ?? 'gray',
                'icon' => $config['icon'] ?? 'heroicon-m-tag',
            ];
        }

        return $allTypes;
    }

    public function ownerHasBreedClubMembership(?int $prevUserId, int $clubId): bool
    {
        return $this->isActiveMembership(
            $this->resolveMembershipForUserAndClub($prevUserId, $clubId)
        );
    }

    public function ownerHasAnyClubMembership(?int $prevUserId): bool
    {
        if (!$prevUserId) {
            return false;
        }

        return PrevClubUser::query()
            ->where('user_id', $prevUserId)
            ->whereNull('deleted_at')
            ->get()
            ->contains(fn(PrevClubUser $membership) => $this->isActiveMembership($membership));
    }

    public function atLeastOneCoOwnerHasBreedClubMembership(PrevDog $dog, ?int $excludePrevUserId, int $clubId): bool
    {
        return $dog->currentOwnersExcluding($excludePrevUserId)
            ->contains(fn(PrevUser $owner) => $this->ownerHasBreedClubMembership((int)$owner->id, $clubId));
    }

    public function allOwnersHaveBreedClubMembership(PrevDog $dog, int $clubId): bool
    {
        $dog->loadMissing('owners');

        if ($dog->owners->isEmpty()) {
            return false;
        }

        return $dog->owners->every(
            fn(PrevUser $owner) => $this->ownerHasBreedClubMembership((int)$owner->id, $clubId)
        );
    }
}
