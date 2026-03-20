<?php

namespace App\Filament\Imports;

use App\Models\PrevDogImport;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class PrevDogImportImporter extends Importer
{
    protected static ?string $model = PrevDogImport::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('dog_name')
                ->rules(['max:255']),
            ImportColumn::make('dog_import_sagir')
                ->rules(['max:255']),
            ImportColumn::make('dog_birth_date')
                ->rules(['max:255']),
            ImportColumn::make('dog_breed')
                ->rules(['max:255']),
            ImportColumn::make('dog_hair_type')
                ->rules(['max:255']),
            ImportColumn::make('dog_hair_color')
                ->rules(['max:255']),
            ImportColumn::make('dog_gender')
                ->rules(['max:255']),
            ImportColumn::make('dog_sagir_prefix')
                ->rules(['max:255']),
            ImportColumn::make('dog_chip')
                ->rules(['max:255']),
            ImportColumn::make('dog_dna')
                ->rules(['max:255']),
            ImportColumn::make('dog_breeder_name')
                ->rules(['max:255']),
            ImportColumn::make('dog_owner_fname')
                ->rules(['max:255']),
            ImportColumn::make('dog_owner_lname')
                ->rules(['max:255']),
            ImportColumn::make('dog_country_id')
                ->rules(['max:255']),
            ImportColumn::make('dog_mobile_phone_code')
                ->rules(['max:255']),
            ImportColumn::make('dog_mobile_phone')
                ->rules(['max:255']),
            ImportColumn::make('dog_owner_phone')
                ->rules(['max:255']),
            ImportColumn::make('dog_tests')
                ->rules(['max:255']),
            ImportColumn::make('dog_titles')
                ->rules(['max:255']),
            ImportColumn::make('dog_notes')
                ->rules(['max:255']),
            ImportColumn::make('user')
                ->relationship(),
            ImportColumn::make('dog_type')
                ->rules(['max:255']),
            ImportColumn::make('dog_sagir_id')
                ->rules(['max:255']),
            ImportColumn::make('dog_owner_fname_2')
                ->rules(['max:255']),
            ImportColumn::make('dog_owner_lname_2')
                ->rules(['max:255']),
            ImportColumn::make('dog_country_id_2')
                ->rules(['max:255']),
            ImportColumn::make('dog_mobile_phone_code_2')
                ->rules(['max:255']),
            ImportColumn::make('dog_mobile_phone_2')
                ->rules(['max:255']),
            ImportColumn::make('dog_owner_phone_2')
                ->rules(['max:255']),
            ImportColumn::make('dog_owner_fname_3')
                ->rules(['max:255']),
            ImportColumn::make('dog_owner_lname_3')
                ->rules(['max:255']),
            ImportColumn::make('dog_country_id_3')
                ->rules(['max:255']),
            ImportColumn::make('dog_mobile_phone_code_3')
                ->rules(['max:255']),
            ImportColumn::make('dog_mobile_phone_3')
                ->rules(['max:255']),
            ImportColumn::make('dog_owner_phone_3')
                ->rules(['max:255']),
            ImportColumn::make('dog_owner_email')
                ->rules(['email', 'max:255']),
            ImportColumn::make('dog_owner_email_2')
                ->rules(['email', 'max:255']),
            ImportColumn::make('dog_owner_email_3')
                ->rules(['email', 'max:255']),
            ImportColumn::make('Foreign_Breeder_name')
                ->rules(['max:255']),
        ];
    }

    public function resolveRecord(): ?PrevDogImport
    {
        // return PrevDogImport::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new PrevDogImport;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your prev dog import import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
