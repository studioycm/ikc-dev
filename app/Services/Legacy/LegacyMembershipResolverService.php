<?php

namespace App\Services\Legacy;

use App\Models\PrevClub;
use App\Models\PrevClubUser;
use App\Models\PrevDog;
use App\Models\PrevUser;

class LegacyMembershipResolverService
{
    protected array $membershipCache = [];

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
            && ($membership->payment_status === null || (int)$membership->payment_status === 1);
    }

    public function resolveSummaryForDogAndUser(PrevDog $dog, ?int $prevUserId = null): array
    {
        $club = $this->resolveClubForDog($dog);
        $prevUserId ??= auth()->user()?->prev_user_id;

        if (!$club) {
            return [
                'club' => null,
                'membership' => null,
                'status_key' => 'check_needed',
                'status_label' => __('No breed club found'),
            ];
        }

        $membership = $this->resolveMembershipForUserAndClub($prevUserId, $club->id);

        $statusKey = match (true) {
            !$membership => 'absolute_no',
            $this->isActiveMembership($membership) => 'absolute_yes',
            default => 'check_needed',
        };

        return [
            'club' => $club,
            'membership' => $membership,
            'status_key' => $statusKey,
            'status_label' => match ($statusKey) {
                'absolute_yes' => __('Active membership'),
                'absolute_no' => __('No membership'),
                default => __('Expired / inactive / forbidden'),
            },
        ];
    }

    public function eligibleMembershipTypes(
        PrevDog $dog,
        ?int    $prevUserId = null,
        ?array  $strategies = null,
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
