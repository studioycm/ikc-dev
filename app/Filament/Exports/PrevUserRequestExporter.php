<?php

namespace App\Filament\Exports;

use App\Models\PrevUserRequest;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PrevUserRequestExporter extends Exporter
{
    protected static ?string $model = PrevUserRequest::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('first_name'),
            ExportColumn::make('last_name'),
            ExportColumn::make('full_address'),
            ExportColumn::make('email'),
            ExportColumn::make('mobile_phone'),
            ExportColumn::make('mobile_prefix'),
            ExportColumn::make('club.id'),
            ExportColumn::make('approve_1'),
            ExportColumn::make('approve_2'),
            ExportColumn::make('approve_3'),
            ExportColumn::make('year'),
            ExportColumn::make('kids_name'),
            ExportColumn::make('birth_date'),
            ExportColumn::make('class'),
            ExportColumn::make('dog1_breed'),
            ExportColumn::make('dog2_breed'),
            ExportColumn::make('dog3_breed'),
            ExportColumn::make('dog1_chip_number'),
            ExportColumn::make('dog2_chip_number'),
            ExportColumn::make('dog3_chip_number'),
            ExportColumn::make('dog1_vaccine_date'),
            ExportColumn::make('dog2_vaccine_date'),
            ExportColumn::make('dog3_vaccine_date'),
            ExportColumn::make('payment_incerments'),
            ExportColumn::make('payment_by'),
            ExportColumn::make('total_amount'),
            ExportColumn::make('status'),
            ExportColumn::make('record_date_time'),
            ExportColumn::make('payment_approval_id'),
            ExportColumn::make('last_4_digits'),
            ExportColumn::make('payment_date_time'),
            ExportColumn::make('topic'),
            ExportColumn::make('owner_name'),
            ExportColumn::make('dog_name'),
            ExportColumn::make('sagirID'),
            ExportColumn::make('shipping'),
            ExportColumn::make('street'),
            ExportColumn::make('number'),
            ExportColumn::make('city'),
            ExportColumn::make('certificate_type'),
            ExportColumn::make('shipping_type_id'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
            ExportColumn::make('IsDone'),
            ExportColumn::make('DoneByUserID'),
            ExportColumn::make('DoneDate'),
            ExportColumn::make('owner.id'),
            ExportColumn::make('paper_request_type'),
            ExportColumn::make('agra_city'),
            ExportColumn::make('champion_certificate_type'),
            ExportColumn::make('breeding_abroad_file'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your prev user request export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
