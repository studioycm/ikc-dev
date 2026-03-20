<?php

namespace App\Filament\Exports;

use App\Models\PrevUser;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PrevUserExporter extends Exporter
{
    protected static ?string $model = PrevUser::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('role_id'),
            ExportColumn::make('first_name'),
            ExportColumn::make('last_name'),
            ExportColumn::make('first_name_en'),
            ExportColumn::make('last_name_en'),
            ExportColumn::make('email'),
            ExportColumn::make('email_verified_at'),
            ExportColumn::make('otp'),
            ExportColumn::make('mobile_phone'),
            ExportColumn::make('phone'),
            ExportColumn::make('birth_date'),
            ExportColumn::make('address_city'),
            ExportColumn::make('address_city_en'),
            ExportColumn::make('address_street'),
            ExportColumn::make('address_street_en'),
            ExportColumn::make('address_street_number'),
            ExportColumn::make('house_number'),
            ExportColumn::make('address_zip'),
            ExportColumn::make('country_id'),
            ExportColumn::make('country_code'),
            ExportColumn::make('fax'),
            ExportColumn::make('social_id_number'),
            ExportColumn::make('passport_id'),
            ExportColumn::make('profile_photo'),
            ExportColumn::make('last_active_date_time'),
            ExportColumn::make('is_superadmin'),
            ExportColumn::make('language_id'),
            ExportColumn::make('status'),
            ExportColumn::make('record_type'),
            ExportColumn::make('migration_status'),
            ExportColumn::make('data_id'),
            ExportColumn::make('owner_code'),
            ExportColumn::make('info_id'),
            ExportColumn::make('owner_email'),
            ExportColumn::make('sagir_owner_id'),
            ExportColumn::make('is_current_owner'),
            ExportColumn::make('order_id'),
            ExportColumn::make('new_sid'),
            ExportColumn::make('new_org_data_id'),
            ExportColumn::make('new_fill_date'),
            ExportColumn::make('new_filler_ip'),
            ExportColumn::make('club_id'),
            ExportColumn::make('owner_payment_sum'),
            ExportColumn::make('owner_payment_last4'),
            ExportColumn::make('member_status'),
            ExportColumn::make('special_key'),
            ExportColumn::make('expire_date'),
            ExportColumn::make('owner_total_payment'),
            ExportColumn::make('start_date'),
            ExportColumn::make('record_source'),
            ExportColumn::make('is_judge'),
            ExportColumn::make('city_id'),
            ExportColumn::make('private_phone_1'),
            ExportColumn::make('private_phone_2'),
            ExportColumn::make('note'),
            ExportColumn::make('image'),
            ExportColumn::make('invoice_id'),
            ExportColumn::make('breed_id'),
            ExportColumn::make('user_key'),
            ExportColumn::make('is_breed_manager'),
            ExportColumn::make('payment_status'),
            ExportColumn::make('created_from'),
            ExportColumn::make('grower_remarks'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
            ExportColumn::make('beit_gidul_id'),
            ExportColumn::make('approved_terms'),
            ExportColumn::make('approved_date'),
            ExportColumn::make('ClubManagerID'),
            ExportColumn::make('logout'),
            ExportColumn::make('breeding_otp'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your prev user export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
