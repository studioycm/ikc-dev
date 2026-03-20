<?php

namespace App\Filament\Exports;

use App\Models\PrevShowPayment;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PrevShowPaymentExporter extends Exporter
{
    protected static ?string $model = PrevShowPayment::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('DataID'),
            ExportColumn::make('ModificationDateTime'),
            ExportColumn::make('CreationDateTime'),
            ExportColumn::make('SagirID'),
            ExportColumn::make('RegistrationID'),
            ExportColumn::make('DogID'),
            ExportColumn::make('PaymentAmount'),
            ExportColumn::make('Last4Digits'),
            ExportColumn::make('OwnerSocialID'),
            ExportColumn::make('NameOnCard'),
            ExportColumn::make('BuyerIP'),
            ExportColumn::make('PaymentSubject'),
            ExportColumn::make('CartKey'),
            ExportColumn::make('PaymentStatus'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your prev show payment export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
