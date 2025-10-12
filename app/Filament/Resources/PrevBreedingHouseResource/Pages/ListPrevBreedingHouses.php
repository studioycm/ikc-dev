<?php

namespace App\Filament\Resources\PrevBreedingHouseResource\Pages;

use App\Filament\Resources\PrevBreedingHouseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrevBreedingHouses extends ListRecords
{
    protected static string $resource = PrevBreedingHouseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

//    public function getTabs(): array
//    {
//        return [
//            'all' => Tab::make(__('All')),
//            'active' => Tab::make(__('Active'))
//                ->badge(fn() => PrevBreedingHouse::query()->where('status', '=', true)->count())
//                ->modifyQueryUsing(fn(Builder $q) => $q->where('status', '=', true)),
//            'recommended' => Tab::make(__('Recommended'))
//                ->badge(fn() => PrevBreedingHouse::query()->where('recommended', '=', true)->count())
//                ->modifyQueryUsing(fn(Builder $q) => $q->where('recommended', '=', true)),
//            'perfect' => Tab::make(__('Perfect'))
//                ->badge(fn() => PrevBreedingHouse::query()->where('perfect', '=', true)->count())
//                ->modifyQueryUsing(fn(Builder $q) => $q->where('perfect', '=', true)),
//        ];
//    }
}
