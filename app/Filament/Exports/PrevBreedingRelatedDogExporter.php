<?php

namespace App\Filament\Exports;

use App\Models\PrevBreedingRelatedDog;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PrevBreedingRelatedDogExporter extends Exporter
{
    protected static ?string $model = PrevBreedingRelatedDog::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label(__('ID')),
            ExportColumn::make('temparory_name'),
            ExportColumn::make('chip_number'),
            ExportColumn::make('sagir_id'),
            ExportColumn::make('color'),
            ExportColumn::make('other_color'),
            ExportColumn::make('gender'),
            ExportColumn::make('approval_status'),
            ExportColumn::make('is_dead'),
            ExportColumn::make('mother_sagir_id'),
            ExportColumn::make('breeding.id'),
            ExportColumn::make('note'),
            ExportColumn::make('supervisor_notes'),
            ExportColumn::make('image'),
            ExportColumn::make('document'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_by'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
            ExportColumn::make('hair'),
            ExportColumn::make('is_submit'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your prev breeding related dog export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
