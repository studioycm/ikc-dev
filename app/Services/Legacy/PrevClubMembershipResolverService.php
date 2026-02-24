<?php

namespace App\Services\Legacy;

use App\Models\PrevClub;
use App\Models\PrevClubUser;
use App\Models\PrevDog;
use Illuminate\Support\Collection;

class PrevClubMembershipResolverService
{
    public function resolveForFemaleDog(
        PrevDog $femaleDog,
        ?int    $prevUserId = null,
    ): array
    {

        $prevUserId ??= auth()->user()?->prev_user_id;

        // Load required relations once
        $femaleDog->loadMissing('breed.clubs');

        $breed = $femaleDog->breed;
        $clubs = $breed?->clubs ?? collect();

        if ($clubs->isEmpty()) {
            return $this->noClubResponse();
        }

        // In reality should be one club per breed
        $club = $clubs->first();

        if (!$prevUserId) {
            return $this->noUserResponse($club);
        }

        $membership = PrevClubUser::query()
            ->where('user_id', $prevUserId)
            ->where('club_id', $club->id)
            ->whereNull('deleted_at')
            ->orderByDesc('expire_date')
            ->first();

        return $this->buildResponse(
            club: $club,
            membership: $membership
        );
    }

    protected function buildResponse(PrevClub $club, PrevClubUser $membership): array
    {
        if (!$membership) {
            return $this->statusResponse(
                club: $club,
                status: 'not_member',
                membership: null,
            );
        }

        $isActive =
            $membership->expire_date >= now()
            && is_null($membership->deleted_at)
            && (is_null($membership->payment_status) || $membership->payment_status === 1);

        if ($isActive) {
            return $this->statusResponse(
                club: $club,
                status: 'active',
                membership: $membership,
            );
        }

        if ($membership->expire_date < now()) {
            return $this->statusResponse(
                club: $club,
                status: 'expired',
                membership: $membership,
            );
        }

        return $this->statusResponse(
            club: $club,
            status: 'inactive',
            membership: $membership,
        );
    }

    protected function statusResponse(PrevClub $club, string $status, ?PrevClubUser $membership): array
    {
        return [
            'club_id' => $club->id,
            'club_name' => $club->Name ?? $club->EngName,
            'status_key' => $status,
            'status_label' => $this->translateStatus($status),
            'discount_valid' => $status === 'active',
            'prices' => $status === 'active'
                ? $this->calculateDiscount($club)
                : null,
            'membership' => $membership,
        ];
    }

    protected function calculateDiscount(PrevClub $club): ?Collection
    {
//        if (! $club->GeneralReviewFee || ! $club->Breed_NonReg_Price) {
//            return null;
//        }

        $nonMember = $club->Breed_NonReg_Price;
        $member = $club->GeneralReviewFee;

//        if ($nonMember <= 0 || $member >= $nonMember) {
//            return null;
//        }
        $prices = [
            'non_member' => $nonMember,
            'member' => $member
        ];
        return collect($prices);
    }

    protected function translateStatus(string $status): string
    {
        return match ($status) {
            'active' => __('Active Member'),
            'expired' => __('Expired'),
            'inactive' => __('Inactive'),
            'not_member' => __('Not a Member'),
            default => __('No Club'),
        };
    }

    protected function noClubResponse(): array
    {
        return [
            'club_id' => null,
            'club_name' => null,
            'status_key' => 'no_club',
            'status_label' => __('No Club'),
            'discount_valid' => false,
            'discount_percent' => null,
            'membership' => null,
        ];
    }

    protected function noUserResponse(PrevClub $club): array
    {
        return [
            'club_id' => $club->id,
            'club_name' => $club->Name ?? $club->EngName,
            'status_key' => 'no_user',
            'status_label' => __('No Related User'),
            'discount_valid' => false,
            'discount_percent' => null,
            'membership' => null,
        ];
    }
}
