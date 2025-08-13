<?php

namespace App\Filament\Resources\PrevJudgeResource\Pages;

    use App\Filament\Resources\PrevJudgeResource;
    use Filament\Actions\DeleteAction;
    use Filament\Resources\Pages\EditRecord;

    class EditPrevJudge extends EditRecord {
        protected static string $resource = PrevJudgeResource::class;

        protected function getHeaderActions(): array {
        return [
        DeleteAction::make(),
        ];
        }
    }
