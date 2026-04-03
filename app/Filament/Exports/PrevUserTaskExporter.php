<?php

namespace App\Filament\Exports;

use App\Models\PrevUserTask;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PrevUserTaskExporter extends Exporter
{
    protected static ?string $model = PrevUserTask::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label(__('ID')),
            ExportColumn::make('managerUser.id'),
            ExportColumn::make('related_to_user_id'),
            ExportColumn::make('task_name'),
            ExportColumn::make('due_date_time'),
            ExportColumn::make('read_status'),
            ExportColumn::make('full_details'),
            ExportColumn::make('related_breeding_process_id'),
            ExportColumn::make('status'),
            ExportColumn::make('pedigree_color'),
            ExportColumn::make('is_editable'),
            ExportColumn::make('done_date_time'),
            ExportColumn::make('review_place'),
            ExportColumn::make('review_date'),
            ExportColumn::make('male_owner_agree'),
            ExportColumn::make('task_type'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
            ExportColumn::make('Req_final_mark'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your prev user task export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
