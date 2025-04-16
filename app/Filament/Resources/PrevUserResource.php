<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevUserResource\Pages;
use App\Filament\Resources\PrevUserResource\RelationManagers;
use App\Models\PrevUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrevUserResource extends Resource
{
    protected static ?string $model = PrevUser::class;

    protected static ?string $label = 'Owner';
    protected static ?string $pluralLabel = 'Owners';

    protected static ?string $navigationGroup = 'Owners Management';

    protected static ?string $navigationLabel = 'Owners';
    protected static ?string $slug = 'prev-users';
    protected static ?string $navigationIcon = 'fas-user';
    protected static ?int $navigationSort = 3;
    protected static ?string $recordTitleAttribute = 'full_name';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
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
                Tables\Columns\TextColumn::make('role_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('first_name_en')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name_en')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('otp')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mobile_phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('birth_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address_city')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address_city_en')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address_street')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address_street_en')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address_street_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('house_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address_zip')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('country_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fax')
                    ->searchable(),
                Tables\Columns\TextColumn::make('social_id_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('passport_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('profile_photo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_active_date_time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('is_superadmin')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('language_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('record_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('migration_status'),
                Tables\Columns\TextColumn::make('data_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('owner_code')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('info_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('owner_email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sagir_owner_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('is_current_owner')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('new_sid')
                    ->searchable(),
                Tables\Columns\TextColumn::make('new_org_data_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('new_fill_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('new_filler_ip')
                    ->searchable(),
                Tables\Columns\TextColumn::make('club_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('owner_payment_sum')
                    ->searchable(),
                Tables\Columns\TextColumn::make('owner_payment_last4')
                    ->searchable(),
                Tables\Columns\TextColumn::make('member_status')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('special_key')
                    ->searchable(),
                Tables\Columns\TextColumn::make('expire_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('owner_total_payment')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('record_source')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('is_judge')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('private_phone_1')
                    ->searchable(),
                Tables\Columns\TextColumn::make('private_phone_2')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('invoice_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('breed_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_key')
                    ->searchable(),
                Tables\Columns\TextColumn::make('is_breed_manager')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('beit_gidul_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('approved_terms')
                    ->searchable(),
                Tables\Columns\TextColumn::make('approved_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ClubManagerID')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('logout')
                    ->boolean(),
                Tables\Columns\TextColumn::make('breeding_otp')
                    ->numeric()
                    ->sortable(),
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrevUsers::route('/'),
            'create' => Pages\CreatePrevUser::route('/create'),
            'edit' => Pages\EditPrevUser::route('/{record}/edit'),
        ];
    }
}
