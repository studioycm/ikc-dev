<?php

namespace App\Filament\Exports;

use App\Models\PrevBreeding;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PrevBreedingExporter extends Exporter
{
    protected static ?string $model = PrevBreeding::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label(__('ID')),
            ExportColumn::make('SagirId'),
            ExportColumn::make('BreddingDate'),
            ExportColumn::make('MaleSagirId'),
            ExportColumn::make('Rules_IsOwner'),
            ExportColumn::make('BreedMismatch'),
            ExportColumn::make('Male_More_Than_5'),
            ExportColumn::make('Male_More_Than_2'),
            ExportColumn::make('Male_DNA'),
            ExportColumn::make('Female_DNA'),
            ExportColumn::make('Male_Breeding_Not_Approved'),
            ExportColumn::make('Female_Breeding_Not_Approved'),
            ExportColumn::make('Foreign_Male_Records'),
            ExportColumn::make('female_rate'),
            ExportColumn::make('male_rebreed'),
            ExportColumn::make('male_rebreed_5'),
            ExportColumn::make('male_rebreed_2'),
            ExportColumn::make('generations_note'),
            ExportColumn::make('live_male_puppie'),
            ExportColumn::make('live_female_puppie'),
            ExportColumn::make('dead_male_puppie'),
            ExportColumn::make('dead_female_puppie'),
            ExportColumn::make('total_dead'),
            ExportColumn::make('review_type'),
            ExportColumn::make('publish_data'),
            ExportColumn::make('share_data'),
            ExportColumn::make('birthing_date'),
            ExportColumn::make('filled_step'),
            ExportColumn::make('payment_type'),
            ExportColumn::make('payment_status'),
            ExportColumn::make('price_per_dog'),
            ExportColumn::make('review_price'),
            ExportColumn::make('certificate_price'),
            ExportColumn::make('total_payment'),
            ExportColumn::make('total_refund'),
            ExportColumn::make('less_than_8_years'),
            ExportColumn::make('more_than_18_months'),
            ExportColumn::make('status'),
            ExportColumn::make('responsiable_owner'),
            ExportColumn::make('created_at'),
            ExportColumn::make('created_by'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
            ExportColumn::make('breedingHouse.id'),
            ExportColumn::make('Breeding_ManagerID'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your prev breeding export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
