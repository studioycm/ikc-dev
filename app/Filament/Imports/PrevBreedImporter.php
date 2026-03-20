<?php

namespace App\Filament\Imports;

use App\Models\PrevBreed;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class PrevBreedImporter extends Importer
{
    protected static ?string $model = PrevBreed::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('DataID')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('ModificationDateTime')
                ->rules(['datetime']),
            ImportColumn::make('CreationDateTime')
                ->rules(['datetime']),
            ImportColumn::make('BreedName')
                ->rules(['max:200']),
            ImportColumn::make('BreedCode')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('Desc'),
            ImportColumn::make('BreedNameEN')
                ->rules(['max:200']),
            ImportColumn::make('GroupID')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('FCICODE')
                ->rules(['max:200']),
            ImportColumn::make('UserManagerID')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('ClubManagerID')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('fci_group')
                ->rules(['max:50']),
            ImportColumn::make('status')
                ->rules(['max:50']),
        ];
    }

    public function resolveRecord(): ?PrevBreed
    {
        // return PrevBreed::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new PrevBreed;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your prev breed import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
