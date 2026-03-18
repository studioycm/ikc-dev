<?php

namespace App\Filament\User\Widgets;

use App\Filament\User\Widgets\Sections\UserClubMembershipsTable;

class ClubMembershipsWidget extends UserClubMembershipsTable
{
    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 2;
}
