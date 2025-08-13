<?php

namespace App\Filament\Resources\PrevJudgeResource\Pages;

use App\Filament\Resources\PrevJudgeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrevJudges extends ListRecords
{
    protected static string $resource = PrevJudgeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
