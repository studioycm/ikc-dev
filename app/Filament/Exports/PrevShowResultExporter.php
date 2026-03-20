<?php

namespace App\Filament\Exports;

use App\Models\PrevShowResult;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PrevShowResultExporter extends Exporter
{
    protected static ?string $model = PrevShowResult::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('DataID'),
            ExportColumn::make('ModificationDateTime'),
            ExportColumn::make('CreationDateTime'),
            ExportColumn::make('RegDogID'),
            ExportColumn::make('SagirID'),
            ExportColumn::make('JudgeName'),
            ExportColumn::make('ShowOrderID'),
            ExportColumn::make('MainArenaID'),
            ExportColumn::make('SubArenaID'),
            ExportColumn::make('ClassID'),
            ExportColumn::make('ShowID'),
            ExportColumn::make('JCAC'),
            ExportColumn::make('GCAC'),
            ExportColumn::make('REJCAC'),
            ExportColumn::make('REGCAC'),
            ExportColumn::make('CW'),
            ExportColumn::make('BJ'),
            ExportColumn::make('BV'),
            ExportColumn::make('CAC'),
            ExportColumn::make('RECACIB'),
            ExportColumn::make('RECAC'),
            ExportColumn::make('BP'),
            ExportColumn::make('BB'),
            ExportColumn::make('BOB'),
            ExportColumn::make('Excellent'),
            ExportColumn::make('Cannotbejudged'),
            ExportColumn::make('VeryGood'),
            ExportColumn::make('VeryPromising'),
            ExportColumn::make('Good'),
            ExportColumn::make('Promising'),
            ExportColumn::make('Sufficient'),
            ExportColumn::make('Satisfactory'),
            ExportColumn::make('Disqualified'),
            ExportColumn::make('Remarks'),
            ExportColumn::make('Rank'),
            ExportColumn::make('CACIB'),
            ExportColumn::make('BD'),
            ExportColumn::make('BOS'),
            ExportColumn::make('BPIS'),
            ExportColumn::make('BPIS2'),
            ExportColumn::make('BPIS3'),
            ExportColumn::make('BJIS'),
            ExportColumn::make('BJIS2'),
            ExportColumn::make('BJIS3'),
            ExportColumn::make('BVIS'),
            ExportColumn::make('BVIS2'),
            ExportColumn::make('BVIS3'),
            ExportColumn::make('BIG'),
            ExportColumn::make('BIG2'),
            ExportColumn::make('BIG3'),
            ExportColumn::make('BIS'),
            ExportColumn::make('BIS2'),
            ExportColumn::make('BIS3'),
            ExportColumn::make('BreedID'),
            ExportColumn::make('NotPresent'),
            ExportColumn::make('GenderID'),
            ExportColumn::make('NoTitle'),
            ExportColumn::make('VCAC'),
            ExportColumn::make('RVCAC'),
            ExportColumn::make('BBaby'),
            ExportColumn::make('BBIS'),
            ExportColumn::make('BBIS2'),
            ExportColumn::make('BBIS3'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
            ExportColumn::make('BBaby2'),
            ExportColumn::make('BBaby3'),
            ExportColumn::make('VCACIB'),
            ExportColumn::make('JCACIB'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your prev show result export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
