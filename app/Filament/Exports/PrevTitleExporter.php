<?php

namespace App\Filament\Exports;

use App\Models\PrevTitle;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PrevTitleExporter extends Exporter
{
    protected static ?string $model = PrevTitle::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('DataID'),
            ExportColumn::make('TitleCode'),
            ExportColumn::make('TitleName'),
            ExportColumn::make('TitleDesc'),
            ExportColumn::make('Remark'),
            ExportColumn::make('dogs_count')
                ->label('Dogs Count')
                ->counts('dogs'),
            ExportColumn::make('ModificationDateTime'),
            ExportColumn::make('CreationDateTime'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your "Previous Title-Types" export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
