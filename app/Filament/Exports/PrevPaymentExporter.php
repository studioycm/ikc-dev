<?php

namespace App\Filament\Exports;

use App\Models\PrevPayment;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PrevPaymentExporter extends Exporter
{
    protected static ?string $model = PrevPayment::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label(__('ID')),
            ExportColumn::make('desc'),
            ExportColumn::make('amount'),
            ExportColumn::make('approval_number'),
            ExportColumn::make('payment_topic'),
            ExportColumn::make('first_name'),
            ExportColumn::make('last_name'),
            ExportColumn::make('email'),
            ExportColumn::make('payment_date_time'),
            ExportColumn::make('club.id'),
            ExportColumn::make('breed.id'),
            ExportColumn::make('sagir_id'),
            ExportColumn::make('last4_digits'),
            ExportColumn::make('user_ip'),
            ExportColumn::make('created_at'),
            ExportColumn::make('created_by'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('updated_by'),
            ExportColumn::make('deleted_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your prev payment export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
