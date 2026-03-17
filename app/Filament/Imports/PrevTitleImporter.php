<?php

namespace App\Filament\Imports;

use App\Models\PrevTitle;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class PrevTitleImporter extends Importer
{
    protected static ?string $model = PrevTitle::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('TitleCode')
                ->label('Title Code')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('DataID')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('ModificationDateTime')
                ->rules(['datetime']),
            ImportColumn::make('CreationDateTime')
                ->rules(['datetime']),
            ImportColumn::make('TitleName')
                ->rules(['max:200']),
            ImportColumn::make('Remark'),
            ImportColumn::make('TitleDesc')
                ->rules(['max:200']),
        ];
    }

    public function resolveRecord(): ?PrevTitle
    {
        return PrevTitle::firstOrNew([
            'TitleCode' => $this->data['TitleCode'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your prev title import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
