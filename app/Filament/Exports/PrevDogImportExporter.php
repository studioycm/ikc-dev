<?php

namespace App\Filament\Exports;

use App\Models\PrevDogImport;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PrevDogImportExporter extends Exporter
{
    protected static ?string $model = PrevDogImport::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('dog_name'),
            ExportColumn::make('dog_import_sagir'),
            ExportColumn::make('dog_birth_date'),
            ExportColumn::make('dog_breed'),
            ExportColumn::make('dog_hair_type'),
            ExportColumn::make('dog_hair_color'),
            ExportColumn::make('dog_gender'),
            ExportColumn::make('dog_sagir_prefix'),
            ExportColumn::make('dog_chip'),
            ExportColumn::make('dog_dna'),
            ExportColumn::make('dog_breeder_name'),
            ExportColumn::make('dog_owner_fname'),
            ExportColumn::make('dog_owner_lname'),
            ExportColumn::make('dog_country_id'),
            ExportColumn::make('dog_mobile_phone_code'),
            ExportColumn::make('dog_mobile_phone'),
            ExportColumn::make('dog_owner_phone'),
            ExportColumn::make('dog_tests'),
            ExportColumn::make('dog_titles'),
            ExportColumn::make('dog_notes'),
            ExportColumn::make('user.id'),
            ExportColumn::make('dog_type'),
            ExportColumn::make('dog_sagir_id'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
            ExportColumn::make('dog_owner_fname_2'),
            ExportColumn::make('dog_owner_lname_2'),
            ExportColumn::make('dog_country_id_2'),
            ExportColumn::make('dog_mobile_phone_code_2'),
            ExportColumn::make('dog_mobile_phone_2'),
            ExportColumn::make('dog_owner_phone_2'),
            ExportColumn::make('dog_owner_fname_3'),
            ExportColumn::make('dog_owner_lname_3'),
            ExportColumn::make('dog_country_id_3'),
            ExportColumn::make('dog_mobile_phone_code_3'),
            ExportColumn::make('dog_mobile_phone_3'),
            ExportColumn::make('dog_owner_phone_3'),
            ExportColumn::make('dog_owner_email'),
            ExportColumn::make('dog_owner_email_2'),
            ExportColumn::make('dog_owner_email_3'),
            ExportColumn::make('Foreign_Breeder_name'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your prev dog import export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
