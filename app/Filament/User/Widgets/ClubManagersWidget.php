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

    protected int $grid_columns = 4;

    public function getClubsWithManagers(): Collection
    {
        $prevUserId = auth()->user()?->prevUser?->id;

        if (!$prevUserId) {
            return new Collection();
        }

        // Get clubs from user's memberships
        $clubIds = auth()->user()?->prevUser?->clubs()
            ->pluck('clubs.id')
            ->unique()
            ->toArray() ?? [];

        if (empty($clubIds)) {
            return new Collection();
        }

        // Get clubs with their managers
        // Note: managers relationship returns PrevUser models directly
        return PrevClub::query()
            ->whereIn('id', $clubIds)
            ->with([
                'managers' => function ($query) {
                    $query->select('users.id', 'users.first_name', 'users.last_name',
                        'users.first_name_en', 'users.last_name_en',
                        'users.mobile_phone', 'users.email');
                },
            ])
            ->get();
    }

    public function getManagersByRole(): array
    {
        // This is a simplified version. In a real implementation,
        // you would have a role field in the user_club_manager pivot table
        $clubs = $this->getClubsWithManagers();
        $managersByClub = [];

        foreach ($clubs as $club) {
            $managers = $club->managers;

            // Group managers by role (placeholder logic)
            $managersByClub[$club->id] = [
                "managers" => [
                    'chairman' => $managers->take(1),
                    'secretary' => $managers->slice(1, 1),
                    'accountant' => $managers->slice(2, 1),
                    'promoters' => $managers->slice(3),
                ],
                "name" => $club->Name,
                "breeds" => $club->breeds->pluck('BreedName')->toArray(),
            ];
        }

        return $managersByClub;
    }
}
