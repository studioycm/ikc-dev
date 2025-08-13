<?php

namespace App\Filament\Resources\PrevJudgeResource\Pages;

    use App\Filament\Resources\PrevJudgeResource;
    use Filament\Resources\Pages\CreateRecord;

    class CreatePrevJudge extends CreateRecord {
        protected static string $resource = PrevJudgeResource::class;

        protected function getHeaderActions(): array {
        return [

        ];
        }
    }
