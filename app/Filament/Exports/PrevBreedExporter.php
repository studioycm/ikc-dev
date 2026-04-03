<?php

namespace App\Filament\Exports;

use App\Models\PrevBreed;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PrevBreedExporter extends Exporter
{
    protected static ?string $model = PrevBreed::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label(__('ID')),
            ExportColumn::make('DataID'),
            ExportColumn::make('ModificationDateTime'),
            ExportColumn::make('CreationDateTime'),
            ExportColumn::make('BreedName'),
            ExportColumn::make('BreedCode'),
            ExportColumn::make('Desc'),
            ExportColumn::make('BreedNameEN'),
            ExportColumn::make('GroupID'),
            ExportColumn::make('FCICODE'),
            ExportColumn::make('UserManagerID'),
            ExportColumn::make('ClubManagerID'),
            ExportColumn::make('fci_group'),
            ExportColumn::make('status'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your prev breed export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
