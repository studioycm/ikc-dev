<?php

namespace App\Filament\User\Widgets;

use App\Models\PrevClub;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;

class ClubManagersWidget extends Widget
{
    protected static string $view = 'filament.user.widgets.club-managers-widget';

    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 3;

    protected int $gridColumns = 6;

    public function getClubs(): Collection
    {
        $prevUserId = auth()->user()?->prevUser?->id;

        if (!$prevUserId) {
            return new Collection;
        }

        // Get clubs from user's memberships
        $clubIds = auth()->user()?->prevUser?->clubs()
            ->pluck('clubs.id')
            ->unique()
            ->toArray() ?? [];

        if (empty($clubIds)) {
            return new Collection;
        }

        return PrevClub::query()
            ->whereIn('id', $clubIds)
            ->with([
                'managers.skills' => fn($query) => $query->whereIn('skills.id', PrevClub::CLUB_STAFF_SKILL_IDS),
                'breeds.promoters.skills' => fn($query) => $query->where('skills.id', PrevClub::PROMOTER_SKILL_ID),
            ])
            ->get();
    }

    public function getClubStaffData(): array
    {
        return $this->getClubs()
            ->mapWithKeys(function (PrevClub $club): array {
                return [
                    $club->getKey() => [
                        'name' => $club->Name,
                        'email' => $club->Email,
                        'address' => $club->full_address,
                        'breeds' => $club->breeds->pluck('BreedName')->filter()->values()->all(),
                        'staff' => $club->managersWithClubTitles()->map(function ($manager): array {
                            return [
                                'name' => $manager->name,
                                'titles' => $manager->getAttribute('club_titles') ?: [],
                                'email' => $manager->email,
                                'mobile_phone' => $manager->normalised_phone ?? $manager->mobile_phone,
                            ];
                        })->values()->all(),
                        'promoters' => $club->promoters()->map(function ($promoter): array {
                            return [
                                'name' => $promoter->name,
                                'titles' => $promoter->getAttribute('club_titles') ?: [],
                                'email' => $promoter->email,
                                'mobile_phone' => $promoter->normalised_phone ?? $promoter->mobile_phone,
                                'breeds' => $promoter->getAttribute('club_breeds') ?: [],
                            ];
                        })->values()->all(),
                    ],
                ];
            })
            ->all();
    }

    public function getGridColumns(): int
    {
        return $this->gridColumns;
    }
}
