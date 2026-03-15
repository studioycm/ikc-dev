<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevUserRequestResource\Pages;
use App\Models\PrevUser;
use App\Models\PrevUserRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrevUserRequestResource extends Resource
{
    protected static ?string $model = PrevUserRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $recordTitleAttribute = 'topic';

    public static function getModelLabel(): string
    {
        return __('User Request');
    }

    public static function getPluralModelLabel(): string
    {
        return __('User Requests');
    }

    public static function getNavigationGroup(): string
    {
        return __('Users Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('User Requests');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Requester details'))
                    ->schema([
                        Forms\Components\TextInput::make('first_name')->label(__('First Name'))->maxLength(255),
                        Forms\Components\TextInput::make('last_name')->label(__('Last Name'))->maxLength(255),
                        Forms\Components\TextInput::make('email')->label(__('Email'))->email()->maxLength(255),
                        Forms\Components\TextInput::make('mobile_phone')->label(__('Mobile Phone'))->tel()->maxLength(255),
                        Forms\Components\TextInput::make('mobile_prefix')->label(__('Mobile Prefix'))->maxLength(50),
                        Forms\Components\Textarea::make('full_address')->label(__('Full Address'))->rows(2)->columnSpanFull(),
                        Forms\Components\TextInput::make('street')->label(__('Street'))->maxLength(255),
                        Forms\Components\TextInput::make('number')->label(__('Number'))->maxLength(255),
                        Forms\Components\TextInput::make('city')->label(__('City'))->maxLength(255),
                    ])
                    ->columns(3),
                Forms\Components\Section::make(__('Request details'))
                    ->schema([
                        Forms\Components\Select::make('club_id')
                            ->label(__('Club'))
                            ->relationship('club', 'Name')
                            ->searchable(['Name', 'EngName'])
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn(Model $record): string => $record->Name ?? $record->EngName ?? (string)$record->id),
                        Forms\Components\Select::make('owner_id')
                            ->label(__('Owner'))
                            ->searchable()
                            ->getSearchResultsUsing(fn(string $search): array => PrevUser::selectOptions($search, 50))
                            ->getOptionLabelUsing(fn($value): ?string => PrevUser::query()->find($value)?->name),
                        Forms\Components\Select::make('DoneByUserID')
                            ->label(__('Done By'))
                            ->searchable()
                            ->getSearchResultsUsing(fn(string $search): array => PrevUser::selectOptions($search, 50))
                            ->getOptionLabelUsing(fn($value): ?string => PrevUser::query()->find($value)?->name),
                        Forms\Components\TextInput::make('topic')->label(__('Topic'))->maxLength(255),
                        Forms\Components\TextInput::make('status')->label(__('Status'))->maxLength(255),
                        Forms\Components\TextInput::make('payment_by')->label(__('Payment By'))->maxLength(255),
                        Forms\Components\TextInput::make('payment_incerments')->label(__('Payment Increments'))->numeric()->minValue(0),
                        Forms\Components\TextInput::make('total_amount')->label(__('Total Amount'))->numeric()->minValue(0),
                        Forms\Components\TextInput::make('payment_approval_id')->label(__('Payment Approval ID'))->maxLength(255),
                        Forms\Components\TextInput::make('last_4_digits')->label(__('Last 4 Digits'))->maxLength(10),
                        Forms\Components\DateTimePicker::make('record_date_time')->label(__('Recorded At'))->seconds(false),
                        Forms\Components\DateTimePicker::make('payment_date_time')->label(__('Payment Date Time'))->seconds(false),
                        Forms\Components\Toggle::make('approve_1')->label(__('Approve 1')),
                        Forms\Components\Toggle::make('approve_2')->label(__('Approve 2')),
                        Forms\Components\Toggle::make('approve_3')->label(__('Approve 3')),
                        Forms\Components\Toggle::make('IsDone')->label(__('Is Done')),
                        Forms\Components\DateTimePicker::make('DoneDate')->label(__('Done Date'))->seconds(false),
                    ])
                    ->columns(4),
                Forms\Components\Section::make(__('Dog and request metadata'))
                    ->schema([
                        Forms\Components\TextInput::make('owner_name')->label(__('Owner Name'))->maxLength(255),
                        Forms\Components\TextInput::make('dog_name')->label(__('Dog Name'))->maxLength(255),
                        Forms\Components\Select::make('sagirID')
                            ->label(__('Dog Record'))
                            ->relationship('dog', 'SagirID')
                            ->searchable(['SagirID', 'Heb_Name', 'Eng_Name'])
                            ->getOptionLabelFromRecordUsing(fn(Model $record): string => $record->full_name . ' #' . $record->SagirID),
                        Forms\Components\TextInput::make('certificate_type')->label(__('Certificate Type'))->maxLength(255),
                        Forms\Components\TextInput::make('shipping')->label(__('Shipping'))->maxLength(255),
                        Forms\Components\TextInput::make('shipping_type_id')->label(__('Shipping Type'))->numeric(),
                        Forms\Components\TextInput::make('paper_request_type')->label(__('Paper Request Type'))->maxLength(255),
                        Forms\Components\TextInput::make('champion_certificate_type')->label(__('Champion Certificate Type'))->maxLength(255),
                        Forms\Components\TextInput::make('agra_city')->label(__('Agra City'))->maxLength(255),
                        Forms\Components\TextInput::make('dog1_chip_number')->label(__('Dog 1 Chip Number'))->maxLength(255),
                        Forms\Components\TextInput::make('dog2_chip_number')->label(__('Dog 2 Chip Number'))->maxLength(255),
                        Forms\Components\TextInput::make('dog3_chip_number')->label(__('Dog 3 Chip Number'))->maxLength(255),
                        Forms\Components\DatePicker::make('dog1_vaccine_date')->label(__('Dog 1 Vaccine Date')),
                        Forms\Components\DatePicker::make('dog2_vaccine_date')->label(__('Dog 2 Vaccine Date')),
                        Forms\Components\DatePicker::make('dog3_vaccine_date')->label(__('Dog 3 Vaccine Date')),
                        Forms\Components\Textarea::make('breeding_abroad_file')->label(__('Breeding Abroad File'))->rows(2)->columnSpanFull(),
                    ])
                    ->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label(__('ID'))->numeric(decimalPlaces: 0, thousandsSeparator: '')->sortable(),
                TextColumn::make('topic')->label(__('Topic'))->searchable()->wrap()->sortable(),
                TextColumn::make('owner.name')
                    ->label(__('Owner'))
                    ->description(fn(PrevUserRequest $record): ?string => $record->owner_name)
                    ->sortable(['last_name', 'first_name'])
                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('dog.SagirID')
                    ->label(__('Dog'))
                    ->description(fn(PrevUserRequest $record): ?string => $record->dog?->full_name ?? $record->dog_name)
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('club.Name')->label(__('Club'))->toggleable(),
                TextColumn::make('total_amount')->label(__('Total Amount'))->numeric(decimalPlaces: 0, thousandsSeparator: '')->sortable(),
                TextColumn::make('status')->label(__('Status'))->badge()->sortable(),
                IconColumn::make('IsDone')->label(__('Done'))->boolean(),
                TextColumn::make('record_date_time')->label(__('Recorded At'))->dateTime()->sortable(),
                TextColumn::make('payment_date_time')->label(__('Payment Date Time'))->dateTime()->sortable()->toggleable(),
                TextColumn::make('deleted_at')->label(__('Deleted At'))->since()->dateTimeTooltip()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('record_date_time', 'desc')
            ->searchOnBlur()
            ->striped();
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make(__('Requester details'))
                    ->schema([
                        TextEntry::make('first_name')->label(__('First Name')),
                        TextEntry::make('last_name')->label(__('Last Name')),
                        TextEntry::make('email')->label(__('Email')),
                        TextEntry::make('mobile_phone')->label(__('Mobile Phone')),
                        TextEntry::make('mobile_prefix')->label(__('Mobile Prefix')),
                        TextEntry::make('full_address')->label(__('Full Address')),
                        TextEntry::make('street')->label(__('Street')),
                        TextEntry::make('number')->label(__('Number')),
                        TextEntry::make('city')->label(__('City')),
                    ])
                    ->columns(3),
                Section::make(__('Request details'))
                    ->schema([
                        TextEntry::make('topic')->label(__('Topic')),
                        TextEntry::make('status')->label(__('Status'))->badge(),
                        TextEntry::make('club.Name')->label(__('Club')),
                        TextEntry::make('owner.name')->label(__('Owner')),
                        TextEntry::make('doneBy.name')->label(__('Done By')),
                        TextEntry::make('total_amount')->label(__('Total Amount'))->numeric(decimalPlaces: 0, thousandsSeparator: ''),
                        TextEntry::make('payment_approval_id')->label(__('Payment Approval ID')),
                        TextEntry::make('last_4_digits')->label(__('Last 4 Digits')),
                        TextEntry::make('record_date_time')->label(__('Recorded At'))->dateTime(),
                        TextEntry::make('payment_date_time')->label(__('Payment Date Time'))->dateTime(),
                        IconEntry::make('approve_1')->label(__('Approve 1'))->boolean(),
                        IconEntry::make('approve_2')->label(__('Approve 2'))->boolean(),
                        IconEntry::make('approve_3')->label(__('Approve 3'))->boolean(),
                        IconEntry::make('IsDone')->label(__('Is Done'))->boolean(),
                        TextEntry::make('DoneDate')->label(__('Done Date'))->dateTime()->placeholder('-'),
                    ])
                    ->columns(4),
                Section::make(__('Dog and metadata'))
                    ->schema([
                        TextEntry::make('dog.full_name')->label(__('Dog')),
                        TextEntry::make('owner_name')->label(__('Owner Name')),
                        TextEntry::make('dog_name')->label(__('Dog Name')),
                        TextEntry::make('certificate_type')->label(__('Certificate Type')),
                        TextEntry::make('paper_request_type')->label(__('Paper Request Type')),
                        TextEntry::make('shipping')->label(__('Shipping')),
                        TextEntry::make('shipping_type_id')->label(__('Shipping Type')),
                        TextEntry::make('agra_city')->label(__('Agra City')),
                        TextEntry::make('deleted_at')->label(__('Deleted At'))->since()->dateTimeTooltip()->placeholder('-'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrevUserRequests::route('/'),
            'create' => Pages\CreatePrevUserRequest::route('/create'),
            'view' => Pages\ViewPrevUserRequest::route('/{record}'),
            'edit' => Pages\EditPrevUserRequest::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
