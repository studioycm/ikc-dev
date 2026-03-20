<?php

namespace App\Filament\Exports;

use App\Models\PrevShowDog;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PrevShowDogExporter extends Exporter
{
    protected static ?string $model = PrevShowDog::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('DataID'),
            ExportColumn::make('ModificationDateTime'),
            ExportColumn::make('CreationDateTime'),
            ExportColumn::make('ShowID'),
            ExportColumn::make('SagirID'),
            ExportColumn::make('GlobalSagirID'),
            ExportColumn::make('OrderID'),
            ExportColumn::make('OwnerID'),
            ExportColumn::make('BirthDate'),
            ExportColumn::make('BreedID'),
            ExportColumn::make('SizeID'),
            ExportColumn::make('GenderID'),
            ExportColumn::make('DogName'),
            ExportColumn::make('ShowRegistrationID'),
            ExportColumn::make('ClassID'),
            ExportColumn::make('OwnerName'),
            ExportColumn::make('OwnerMobile'),
            ExportColumn::make('BeitGidulName'),
            ExportColumn::make('HairID'),
            ExportColumn::make('ColorID'),
            ExportColumn::make('MainArenaID'),
            ExportColumn::make('ArenaID'),
            ExportColumn::make('ShowBreedID'),
            ExportColumn::make('MobileNumber'),
            ExportColumn::make('OwnerEmail'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
            ExportColumn::make('new_show_registration_id'),
            ExportColumn::make('present'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your prev show dog export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
