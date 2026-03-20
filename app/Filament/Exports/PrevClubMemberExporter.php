<?php

namespace App\Filament\Exports;

use App\Models\PrevUser;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PrevClubMemberExporter extends Exporter
{
    protected static ?string $model = PrevUser::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name')
                ->label('Member'),
            ExportColumn::make('email')
                ->label('Email'),
            ExportColumn::make('mobile_phone')
                ->label('Phone'),
            ExportColumn::make('membership.type')
                ->label('Type'),
            ExportColumn::make('membership.status')
                ->label('Status'),
            ExportColumn::make('membership.payment_status')
                ->label('Payment Status'),
            ExportColumn::make('membership.forbidden')
                ->label('Forbidden'),
            ExportColumn::make('membership.expire_date')
                ->label('Expires At'),
            ExportColumn::make('membership.created_at')
                ->label('Attached At'),
            ExportColumn::make('membership.updated_at')
                ->label('Updated At'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your club members export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
