<?php

namespace App\Filament\Imports;

use App\Models\PrevBreedingRelatedDog;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class PrevBreedingRelatedDogImporter extends Importer
{
    protected static ?string $model = PrevBreedingRelatedDog::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('temparory_name'),
            ImportColumn::make('chip_number'),
            ImportColumn::make('sagir_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('color')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('other_color')
                ->rules(['max:255']),
            ImportColumn::make('gender')
                ->rules(['max:20']),
            ImportColumn::make('approval_status')
                ->rules(['max:20']),
            ImportColumn::make('is_dead')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('mother_sagir_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('note'),
            ImportColumn::make('supervisor_notes'),
            ImportColumn::make('image'),
            ImportColumn::make('document')
                ->rules(['max:100']),
            ImportColumn::make('updated_by')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('hair')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('is_submit')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
        ];
    }

    public function resolveRecord(): ?PrevBreedingRelatedDog
    {
        $record = new PrevBreedingRelatedDog;

        if ($breedingId = ($this->options['breedingId'] ?? null)) {
            $record->breeding_id = $breedingId;
        }

        return $record;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your prev breeding related dog import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
