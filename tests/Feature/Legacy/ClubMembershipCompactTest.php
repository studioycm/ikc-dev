<?php

use App\Livewire\Legacy\Breeding\ClubMembershipCompact;
use App\Models\PrevClub;
use App\Models\PrevClubUser;
use App\Models\PrevDog;
use App\Services\Legacy\LegacyMembershipResolverService;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

it('includes club pricing and membership details in the resolved summary', function () {
    Carbon::setTestNow('2026-03-14 02:10:00');

    $resolver = new class extends LegacyMembershipResolverService {
        public function resolveClubForDog(PrevDog $dog): ?PrevClub
        {
            return new PrevClub([
                'id' => 15,
                'Name' => 'Terrier Club',
                'Address' => '12 Main Street',
                'Email' => 'club@example.test',
                'GeneralReviewFee' => 120,
                'Breed_NonReg_Price' => 220,
                'RegistrationPrice' => 40,
                'DogReviewFee' => 35,
                'PerDog_NonReg_Price' => 25,
                'TestPrice' => 15,
            ]);
        }

        public function resolveMembershipForUserAndClub(?int $prevUserId, ?int $clubId): ?PrevClubUser
        {
            return new PrevClubUser([
                'id' => 88,
                'club_id' => $clubId,
                'user_id' => $prevUserId,
                'type' => 'Main',
                'status' => 'active',
                'payment_status' => 1,
                'forbidden' => false,
                'breeder_code' => 'BR-777',
                'expire_date' => now()->addMonth(),
            ]);
        }
    };

    $summary = $resolver->resolveSummaryForDogAndUser(new PrevDog, 10);

    expect($summary['status_key'])->toBe('active')
        ->and($summary['club_name'])->toBe('Terrier Club')
        ->and($summary['has_active_membership'])->toBeTrue()
        ->and(data_get($summary, 'club.fees.member_price.amount'))->toBe(120)
        ->and(data_get($summary, 'prices.non_member.amount'))->toBe(220)
        ->and(data_get($summary, 'prices.final.amount'))->toBe(120)
        ->and(data_get($summary, 'prices.discount.amount'))->toBe(100)
        ->and(data_get($summary, 'membership.breeder_code'))->toBe('BR-777')
        ->and(data_get($summary, 'membership.expire_date_display'))->toBe('14/04/2026');

    Carbon::setTestNow();
});

it('returns a no club summary when the dog breed has no club', function () {
    $resolver = new class extends LegacyMembershipResolverService {
        public function resolveClubForDog(PrevDog $dog): ?PrevClub
        {
            return null;
        }
    };

    $summary = $resolver->resolveSummaryForDogAndUser(new PrevDog, 10);

    expect($summary['status_key'])->toBe('no_club')
        ->and($summary['prices'])->toBeNull()
        ->and($summary['club'])->toBeNull();
});

it('renders the membership summary in the livewire compact component', function () {
    Livewire::test(ClubMembershipCompact::class, [
        'membershipState' => [
            'club_name' => 'Terrier Club',
            'status_key' => 'active',
            'status_label' => 'Active membership',
            'membership' => [
                'type_label' => 'Main Member',
                'expire_date_display' => '14/04/2026',
            ],
            'prices' => [
                'non_member' => [
                    'amount' => 220,
                    'formatted' => '₪220',
                ],
                'member' => [
                    'amount' => 120,
                    'formatted' => '₪120',
                ],
                'final' => [
                    'amount' => 120,
                    'formatted' => '₪120',
                ],
                'discount' => [
                    'amount' => 100,
                    'formatted' => '₪100',
                ],
                'final_label' => 'Member Price',
            ],
        ],
    ])
        ->assertSee('Terrier Club')
        ->assertSee('Active membership')
        ->assertSee('₪220')
        ->assertSee('₪120')
        ->assertSee(__('More details'));
});

it('renders an empty state when no membership summary is available', function () {
    Livewire::test(ClubMembershipCompact::class, [
        'membershipState' => null,
    ])
        ->assertSee(__('No dog selected.'))
        ->assertDontSee(__('More details'));
});
