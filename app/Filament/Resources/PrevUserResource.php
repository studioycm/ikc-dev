<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\PrevUser;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Filters;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\PrevUserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
//use App\Filament\Resources\PrevUserResource\RelationManagers;

class PrevUserResource extends Resource
{
    protected static ?string $model = PrevUser::class;


    public static function getModelLabel(): string
    {
        return __('Owner');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Owners');
    }

    public static function getNavigationGroup(): string
    {
        return __('Owners Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Owners');
    }


    protected static ?string $slug = 'prev-users';
    protected static ?string $navigationIcon = 'fas-user';
    protected static ?int $navigationSort = 3;
    // protected static ?string $recordTitleAttribute = 'first_name';

    // public static function getGlobalSearchResultTitle(Model $record): Htmlable | string
    // {
    //     return $record->name;
    // }

    // public static function getGloballySearchableAttributes(): array
    // {
    //     return ['first_name', 'last_name', 'first_name_en', 'last_name_en'];
    // }

    // public static function getGlobalSearchResultDetails(Model $record): array
    // {
    //     return [
    //         'Email' => $record->Email,
    //         'Phone' => $record->mobile_phone,
    //     ];
    // }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::$model::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('role_id')
                    ->numeric(),
                Forms\Components\TextInput::make('first_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('first_name_en')
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name_en')
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('email_verified_at'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->maxLength(255),
                Forms\Components\TextInput::make('otp')
                    ->numeric(),
                Forms\Components\TextInput::make('mobile_phone')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('birth_date'),
                Forms\Components\TextInput::make('address_city')
                    ->maxLength(255),
                Forms\Components\TextInput::make('address_city_en')
                    ->maxLength(255),
                Forms\Components\TextInput::make('address_street')
                    ->maxLength(255),
                Forms\Components\TextInput::make('address_street_en')
                    ->maxLength(250),
                Forms\Components\TextInput::make('address_street_number')
                    ->maxLength(255),
                Forms\Components\TextInput::make('house_number')
                    ->maxLength(150),
                Forms\Components\TextInput::make('address_zip')
                    ->maxLength(255),
                Forms\Components\TextInput::make('country_id')
                    ->numeric(),
                Forms\Components\TextInput::make('country_code')
                    ->maxLength(255),
                Forms\Components\TextInput::make('fax')
                    ->maxLength(255),
                Forms\Components\TextInput::make('social_id_number')
                    ->maxLength(255),
                Forms\Components\TextInput::make('passport_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('profile_photo')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('last_active_date_time'),
                Forms\Components\TextInput::make('is_superadmin')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('language_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('status')
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('record_type')
                    ->maxLength(50),
                Forms\Components\TextInput::make('migration_status'),
                Forms\Components\TextInput::make('data_id')
                    ->numeric(),
                Forms\Components\TextInput::make('owner_code')
                    ->numeric(),
                Forms\Components\TextInput::make('info_id')
                    ->numeric(),
                Forms\Components\TextInput::make('owner_email')
                    ->email()
                    ->maxLength(100),
                Forms\Components\TextInput::make('sagir_owner_id')
                    ->numeric(),
                Forms\Components\TextInput::make('is_current_owner')
                    ->numeric(),
                Forms\Components\TextInput::make('order_id')
                    ->numeric(),
                Forms\Components\TextInput::make('new_sid')
                    ->maxLength(200),
                Forms\Components\TextInput::make('new_org_data_id')
                    ->numeric(),
                Forms\Components\DatePicker::make('new_fill_date'),
                Forms\Components\TextInput::make('new_filler_ip')
                    ->maxLength(200),
                Forms\Components\TextInput::make('club_id')
                    ->numeric(),
                Forms\Components\TextInput::make('owner_payment_sum')
                    ->maxLength(200),
                Forms\Components\TextInput::make('owner_payment_last4')
                    ->maxLength(200),
                Forms\Components\TextInput::make('member_status')
                    ->numeric(),
                Forms\Components\TextInput::make('special_key')
                    ->maxLength(4000),
                Forms\Components\DatePicker::make('expire_date'),
                Forms\Components\TextInput::make('owner_total_payment')
                    ->numeric(),
                Forms\Components\DatePicker::make('start_date'),
                Forms\Components\TextInput::make('record_source')
                    ->numeric(),
                Forms\Components\TextInput::make('is_judge')
                    ->maxLength(200),
                Forms\Components\TextInput::make('city_id')
                    ->numeric(),
                Forms\Components\TextInput::make('private_phone_1')
                    ->tel()
                    ->maxLength(200),
                Forms\Components\TextInput::make('private_phone_2')
                    ->tel()
                    ->maxLength(200),
                Forms\Components\Textarea::make('note')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('image')
                    ->image(),
                Forms\Components\TextInput::make('invoice_id')
                    ->maxLength(200),
                Forms\Components\TextInput::make('breed_id')
                    ->numeric(),
                Forms\Components\TextInput::make('user_key')
                    ->maxLength(4000),
                Forms\Components\TextInput::make('is_breed_manager')
                    ->maxLength(200),
                Forms\Components\TextInput::make('payment_status')
                    ->maxLength(200),
                Forms\Components\Textarea::make('created_from')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('grower_remarks')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('beit_gidul_id')
                    ->numeric(),
                Forms\Components\TextInput::make('approved_terms')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('approved_date'),
                Forms\Components\TextInput::make('ClubManagerID')
                    ->numeric(),
                Forms\Components\Toggle::make('logout')
                    ->required(),
                Forms\Components\TextInput::make('breeding_otp')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->sortable(['last_name', 'first_name'])
                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en'],isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Full Name')
                    ->sortable(['last_name', 'first_name'])
                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en'],isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('first_name')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('last_name')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('first_name_en')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('last_name_en')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(isGlobal: false, isIndividual: true)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('otp')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('mobile_phone')
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('role_id')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('birth_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('address_city')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('address_city_en')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('address_street')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('address_street_en')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('address_street_number')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('house_number')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('address_zip')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('country_id')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('country_code')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('fax')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('social_id_number')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('passport_id')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('profile_photo')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('last_active_date_time')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('language_id')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('is_superadmin')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('status')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('record_type')
                    ->label(__('User Type'))
                    ->badge()
                    ->color(fn (PrevUser $record): string => match ($record->record_type) {
                        'Native'  => 'success',
                        'Owners'  => 'warning',
                        'Members' => 'blue',
                        default   => 'gray',
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('migration_status')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('data_id')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('owner_code')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('info_id')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('owner_email')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('sagir_owner_id')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('is_current_owner')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('order_id')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('new_sid')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('new_org_data_id')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('new_fill_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('new_filler_ip')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('club_id')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('owner_payment_sum')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('owner_payment_last4')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('member_status')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('special_key')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('expire_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('owner_total_payment')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('record_source')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('is_judge')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('city_id')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('private_phone_1')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('private_phone_2')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ImageColumn::make('image')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('invoice_id')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('breed_id')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user_key')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('is_breed_manager')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('payment_status')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('beit_gidul_id')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('approved_terms')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('approved_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('ClubManagerID')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('logout')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('breeding_otp')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filters\Filter::make('trashed')
                    ->form([
                        Forms\Components\ToggleButtons::make('trashed')
                            ->label(__('Deleted Status'))
                            ->options([
                                'not_deleted' => __('Not Deleted'),
                                'deleted'     => __('Deleted'),
                                'all'         => __('All'),
                            ])
                            ->colors([
                                'not_deleted' => 'success',
                                'deleted'     => 'danger',
                                'all'         => 'gray',
                            ])
                            ->default('not_deleted')
                            ->grouped(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['trashed']) || $data['trashed'] === 'all') {
                            return $query;
                        }
                        return match ($data['trashed']) {
                            'deleted'     => $query->onlyTrashed(),
                            'not_deleted' => $query->withoutTrashed(),
                        };
                    }),
                Filters\Filter::make('record_type')
                    ->form([
                        Forms\Components\ToggleButtons::make('record_type')
                            ->label(__('User Type'))
                            ->options([
                                'all' => __('All'),
                                'Native'  => 'Native',
                                'Owners'  => __('Owners'),
                                'Members' => __('Members'),
                                'without' => __('-without-'),
                            ])
                            ->colors([
                                'all' => 'gray',
                                'Native'  => 'success',
                                'Owners'  => 'warning',
                                'Members' => 'danger',
                                'without' => 'gray',
                            ])
                            ->default('all')
                            ->grouped(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['record_type']) || $data['record_type'] === 'all') {
                            return $query;
                        }
                        return match ($data['record_type']) {
                            'all' => $query,
                            'Native'  => $query->where('record_type', 'Native'),
                            'Owners'  => $query->where('record_type', 'Owners'),
                            'Members' => $query->where('record_type', 'Members'),
                            'without' => $query->whereNull('record_type'),
                        };
                    }),
                    Filters\Filter::make('created_at')
                        ->form([
                            Forms\Components\Section::make(__('Created Dates'))
                                ->columns(2)
                                ->schema([
                                    Forms\Components\DatePicker::make('created_at_from')
                                        ->label(__('Created From'))
                                        ->native(true)
                                        ->format('d/m/Y')
                                        ->displayFormat('d/m/Y')
                                        ->locale('he')
                                        ->weekStartsOnSunday()
                                        ->closeOnDateSelection(),
                                    Forms\Components\DatePicker::make('created_at_to')
                                        ->label(__('Created To'))
                                        ->native(false)
                                        ->format('d/m/Y')
                                        ->displayFormat('d/m/Y')
                                        ->locale('he')
                                        ->weekStartsOnSunday()
                                        ->closeOnDateSelection(),
                                ]),
                        ])
                        ->query(function (Builder $query, array $data) {
                            if (!empty($data['created_at_from'])) {
                                $query->where('created_at', '>=', $data['created_at_from']);
                            }
                            if (!empty($data['created_at_to'])) {
                                $query->where('created_at', '<=', $data['created_at_to']);
                            }
                            return $query;
                        }),
                        Filters\Filter::make('updated_at')
                        ->form([
                            Forms\Components\Section::make(__('Updated Dates'))
                                ->columns(2)
                                ->schema([
                                    Forms\Components\DatePicker::make('updated_at_from')
                                        ->label(__('Updated From')),
                                    Forms\Components\DatePicker::make('updated_at_to')
                                        ->label(__('Updated To')),
                                ]),
                        ])
                        ->query(function (Builder $query, array $data) {
                            if (!empty($data['updated_at_from'])) {
                                $query->where('updated_at', '>=', $data['updated_at_from']);
                            }
                            if (!empty($data['updated_at_to'])) {
                                $query->where('updated_at', '<=', $data['updated_at_to']);
                            }
                            return $query;
                        }),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(4)
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->paginated([10, 25, 50, 100, 200, 250, 300])
            ->defaultPaginationPageOption(25)
            ->defaultSort('first_name', 'asc')
            ->searchOnBlur()
            ->striped()
            ->deferLoading()
            ->recordUrl(false);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrevUsers::route('/'),
            'create' => Pages\CreatePrevUser::route('/create'),
            'edit' => Pages\EditPrevUser::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            PrevUserResource\Widgets\UserStats::class,
        ];
    }
}
