<?php

namespace App\Filament\User\Pages;

use App\Filament\User\Widgets\Sections\UserDogsTable;
use Filament\Pages\Dashboard as BaseDashboard;

class DogsDashboard extends BaseDashboard
{
    protected static ?int $navigationSort = 20;

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static string $routePath = 'dogs';

    public static function getNavigationLabel(): string
    {
        return __('dog/model/general.labels.navigation_label');
    }

    public function getTitle(): string
    {
        return __('dog/model/general.labels.plural');
    }

    public function getColumns(): int|string|array
    {
        return 1;
    }

    public function getWidgets(): array
    {
        return [
            UserDogsTable::class,
        ];
    }
}
