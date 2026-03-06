<?php

namespace App\Filament\Resources\PrevDogResource\Pages;

use App\Enums\Legacy\LegacySagirPrefix;
use App\Filament\Resources\PrevDogResource;
use App\Models\PrevDog;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListPrevDogs extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = PrevDogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
//            Actions\Action::make('pedigree')
//                ->label(__('Manage Pedigree'))
//                ->icon('heroicon-m-share')
//                ->url(PrevDogResource::getUrl('pedigree')),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PrevDogResource\Widgets\DogStats::class,
        ];
    }

    public function setPage($page, $pageName = 'page'): void
    {
        parent::setPage($page, $pageName);

        $this->dispatch('scroll-to-top');
    }

    public function getTabs(): array
    {

        return [
            'israeli' => Tab::make()
                ->label(__('ISR Studbook'))
                ->icon(LegacySagirPrefix::ISR->getIcon())
                ->badgeColor(LegacySagirPrefix::ISR->getColor())
                // Add badge to the tab
                ->badge(PrevDog::where('sagir_prefix', LegacySagirPrefix::ISR->value)->count())
                // Modify the query only to show completed tasks
                ->modifyQueryUsing(function ($query) {
                    return $query->where('sagir_prefix', LegacySagirPrefix::ISR->value);
                }),

            'import' => Tab::make()
                ->label(__('IMP Studbook'))
                ->icon(LegacySagirPrefix::IMP->getIcon())
                ->badgeColor(LegacySagirPrefix::IMP->getColor())
                // Add badge to the tab
                ->badge(PrevDog::where('sagir_prefix', LegacySagirPrefix::IMP->value)->count())
                // Modify the query only to show completed tasks
                ->modifyQueryUsing(function ($query) {
                    return $query->where('sagir_prefix', LegacySagirPrefix::IMP->value);
                }),

            'external' => Tab::make()
                ->label(__('EXT Studbook'))
                ->icon(LegacySagirPrefix::EXT->getIcon())
                ->badgeColor(LegacySagirPrefix::EXT->getColor())
                // Add badge to the tab
                ->badge(PrevDog::where('sagir_prefix', LegacySagirPrefix::EXT->value)->count())
                // Modify the query only to show completed tasks
                ->modifyQueryUsing(function ($query) {
                    return $query->where('sagir_prefix', LegacySagirPrefix::EXT->value);
                }),

            'appendix' => Tab::make()
                ->label(__('APX Studbook'))
                ->icon(LegacySagirPrefix::APX->getIcon())
                ->badgeColor(LegacySagirPrefix::APX->getColor())
                // Add badge to the tab
                ->badge(PrevDog::where('sagir_prefix', LegacySagirPrefix::APX->value)->count())
                // Modify the query only to show completed tasks
                ->modifyQueryUsing(function ($query) {
                    return $query->where('sagir_prefix', LegacySagirPrefix::APX->value);
                }),
            'all' => Tab::make()
                ->label(__('Show All')),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'all';
    }
}
