<?php

namespace App\Filament\Resources;

use App\Enums\Legacy\LegacySagirPrefix;
use App\Enums\Legacy\LegacyUserRequestChampionType;
use App\Enums\Legacy\LegacyUserRequestPaperType;
use App\Enums\Legacy\LegacyUserRequestTopic;
use App\Filament\Exports\PrevUserRequestExporter;
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
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;

class PrevUserRequestResource extends Resource
{
    protected static ?string $model = PrevUserRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $recordTitleAttribute = 'topic';

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        return $record->topic->getLabel();
    }

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
                        Forms\Components\Select::make('topic')
                            ->label(__('Topic'))
                            ->options(LegacyUserRequestTopic::class),
                        Forms\Components\TextInput::make('status')->label(__('Status'))->maxLength(255),
                        Forms\Components\TextInput::make('payment_by')->label(__('Payment By'))->maxLength(255),
                        Forms\Components\TextInput::make('payment_incerments')->label(__('Payment Increments'))->numeric()->minValue(0),
                        Forms\Components\TextInput::make('total_amount')
                            ->label(__('Cost'))
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\TextInput::make('payment_approval_id')->label(__('Payment Approval Number'))->maxLength(255),
                        Forms\Components\TextInput::make('last_4_digits')->label(__('Last 4 Digits'))->maxLength(10),
                        Forms\Components\DateTimePicker::make('record_date_time')->label(__('Recorded at'))->seconds(false),
                        Forms\Components\DateTimePicker::make('payment_date_time')->label(__('Payment Date'))->seconds(false),
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
                        Forms\Components\ToggleButtons::make('paper_request_type')
                            ->label(__('Paper Request Type'))
                            ->options(LegacyUserRequestPaperType::class)
                            ->grouped(),
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
            ->modifyQueryUsing(fn($query) => $query->with(
                ['userByPhone', 'owner', 'dog', 'doneBy', 'club', 'vetAuth']
            ))
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('topic')
                    ->label(__('Topic'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable()
                    ->badge()
                    ->description(function ($state, PrevUserRequest $record): ?string {
                        if ($state === null) {
                            return __('Topic') . ' ' . __('Missing');
                        }

                        return match ($state->value) {
                            'pedigree_paper_request' => $record->paper_request_type
                                ? $record->paper_request_type->getLabel()
                                : __('Pedigree Type') . ' ' . __('Missing'),

                            'champion_diploma_request' => $record->champion_certificate_type
                                ? $record->champion_certificate_type->getLabel()
                                : __('Champion Certificate') . ' ' . __('Missing'),

                            'Payment of pelvic / elbow photo decoding' => $record->total_amount
                                ? Number::currency($record->total_amount, in: 'ILS', locale: 'he_IL', precision: 0)
                                : __('Price') . ' ' . __('Missing'),
                            'agra_form' => $record->vetAuth
                                ? $record->vetAuth->name . ' (' . $record->vetAuth->vet_email . ')'
                                : __('Veterinarian Authority') . ' ' . __('Missing'),
                            'young_rider_registration' => $record->kids_name
                                ? $record->kids_name . ($record->class ? ' | ' . $record->class : '') . ($record->birth_date ? ' (' . $record->birth_date->format('Y-m-d') . ')' : '')
                                : __("Kid's Name") . ' ' . __('Missing'),
                            default => '',
                        };
                    })
                    ->toggleable(),
                TextColumn::make('paper_request_type')
                    ->label(__('Pedigree Type'))
                    ->badge()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('champion_certificate_type')
                    ->label(__('Champion Certificate'))
                    ->badge()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('vetAuth.name')
                    ->label(__('Veterinarian Authority'))
                    ->sortable()
                    ->searchable(['agra_cities.name', 'agra_cities.vet_email'], isIndividual: true, isGlobal: false)
                    ->description(fn(PrevUserRequest $record): ?string => $record->vetAuth?->vet_email ?? __('Missing') . ' ' . __('Email'))
                    ->toggleable(),
                TextColumn::make('club.Name')
                    ->label(__('Club'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('requester_name')
                    ->label(__('User Name'))
                    ->description(fn(PrevUserRequest $record): string => $record->mobile_phone ?? $record->email ?? __('Missing') . ' ' . __('Phone'))
                    ->searchable(['public_registration.first_name', 'public_registration.last_name'], isIndividual: true, isGlobal: false)
                    ->sortable(['public_registration.first_name', 'public_registration.last_name'])
                    ->toggleable(),
                TextColumn::make('userByPhone.name')
                    ->label(__('User by Phone'))
                    ->description(fn(PrevUserRequest $record) => $record->userByPhone?->mobile_phone)
                    ->sortable(['first_name', 'last_name'])
                    ->searchable(['users.first_name', 'users.last_name', 'users.first_name_en', 'users.last_name_en', 'users.mobile_phone'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('dog.SagirID')
                    ->label(__('dog/model/general.labels.singular'))
                    ->description(fn(PrevUserRequest $record): ?string => $record->dog?->full_name)
                    ->sortable()
                    ->searchable(['DogsDB.SagirID', 'DogsDB.eng_name', 'DogsDB.heb_name'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('owner_name')
                    ->label(__('Owner Name'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('owner.name')
                    ->label(__('Owner'))
                    ->sortable(['users.last_name', 'users.first_name'])
                    ->searchable(['users.first_name', 'users.last_name', 'users.first_name_en', 'users.last_name_en', 'users.mobile_phone'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('total_amount')
                    ->label(__('Cost'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: ',')
                    ->money(currency: 'ILS')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('payment_date_time')
                    ->label(__('Payment Date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('payment_approval_id')
                    ->label(__('Payment Approval Number'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending payment' => 'warning',
                        'payment done' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending payment' => __('Pending Payment'),
                        'payment done' => __('Payment Done'),
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('record_date_time')
                    ->label(__('Recorded at'))
                    ->date()
                    ->sortable()
                    ->toggleable(),
                IconColumn::make('IsDone')
                    ->label(__('Done'))
                    ->boolean()
                    ->toggleable(),
                TextColumn::make('doneBy.name')
                    ->label(__('Done By'))
                    ->sortable(['users.first_name', 'users.last_name'])
                    ->searchable(['users.first_name', 'users.last_name', 'users.first_name_en', 'users.last_name_en', 'users.mobile_phone'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('DoneDate')
                    ->label(__('Done Date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('Created at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label(__('Updated at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('deleted_at')
                    ->label(__('Deleted at'))
                    ->since()
                    ->dateTimeTooltip()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'pending payment' => __('Pending Payment'),
                        'payment done' => __('Payment Done'),
                    ]),
                Tables\Filters\SelectFilter::make('club')
                    ->label(__('Club'))
                    ->relationship('club', 'Name')
                    ->searchable(['Name', 'EngName'])
                    ->multiple()
                    ->preload(),
                Tables\Filters\SelectFilter::make('topic')
                    ->label(__('Topic'))
                    ->options(LegacyUserRequestTopic::class)
                    ->multiple(),
                Tables\Filters\SelectFilter::make('paper_request_type')
                    ->label(__('Pedigree Type'))
                    ->options(LegacyUserRequestPaperType::class),
                Tables\Filters\SelectFilter::make('champion_certificate_type')
                    ->label(__('Champion Certificate'))
                    ->options(LegacyUserRequestChampionType::class)
                    ->multiple(),
                Tables\Filters\SelectFilter::make('dog.sagir_prefix')
                    ->label(__('Sagir Prefix'))
                    ->options(LegacySagirPrefix::class),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make()
                    ->label(__('Export Selected'))
                    ->icon('fas-file-export')
                    ->color('primary')
                    ->iconPosition('after')
                    ->exporter(PrevUserRequestExporter::class),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label(__('Export All'))
                    ->icon('fas-file-export')
                    ->color('primary')
                    ->iconPosition('after')
                    ->exporter(PrevUserRequestExporter::class),
            ])
            ->defaultSort('record_date_time', 'desc')
            ->searchOnBlur()
            ->striped()
            ->actionsPosition(Tables\Enums\ActionsPosition::BeforeColumns);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(4)
            ->schema([
                Section::make(__('Request details'))
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('topic')
                            ->label(__('Topic'))
                            ->badge()
                            ->inlineLabel(),
                        TextEntry::make('champion_certificate_type')
                            ->label(__('Certificate Type'))
                            ->badge()
                            ->hidden(fn(PrevUserRequest $record) => $record->champion_certificate_type === null)
                            ->inlineLabel(),
                        TextEntry::make('paper_request_type')
                            ->label(__('Paper Request Type'))
                            ->badge()
                            ->hidden(fn(PrevUserRequest $record) => $record->paper_request_type === null)
                            ->inlineLabel(),
                        TextEntry::make('shipping')->label(__('Shipping'))
                            ->visible(fn(PrevUserRequest $record) => filled($record->shipping))
                            ->inlineLabel(),
                        TextEntry::make('shipping_type_id')->label(__('Shipping Type'))
                            ->visible(fn(PrevUserRequest $record) => filled($record->shipping_type_id))
                            ->inlineLabel(),
                        TextEntry::make('club.Name')->label(__('Club'))
                            ->hidden(fn(PrevUserRequest $record) => $record->club === null)
                            ->inlineLabel(),
                        TextEntry::make('vetAuth.name')->label(__('Veterinarian Authority'))
                            ->tooltip(fn(PrevUserRequest $record) => $record->vetAuth?->vet_email)
                            ->copyable()
                            ->copyableState(fn(PrevUserRequest $record) => $record->vetAuth?->vet_email)
                            ->copyMessage(__('filament::components/copyable.messages.copied'))
                            ->copyMessageDuration(1500)
                            ->hidden(fn(PrevUserRequest $record) => $record->vetAuth === null)
                            ->inlineLabel(),
                        TextEntry::make('kids_name')
                            ->label(__("Kid's Name"))
                            ->formatStateUsing(fn($state, PrevUserRequest $record) => $state . ($record->class ? ' | ' . $record->class : '') . ($record->birth_date ? ' (' . $record->birth_date->format('Y-m-d') . ')' : ''))
                            ->visible(fn(PrevUserRequest $record) => filled($record->kids_name))
                            ->inlineLabel(),
                        TextEntry::make('total_amount')
                            ->label(__('Cost'))
                            ->numeric(decimalPlaces: 0, thousandsSeparator: ',')
                            ->money('ILS', 0, 'he_IL')
                            ->inlineLabel(),
                        TextEntry::make('status')->label(__('Status'))->badge()
                            ->inlineLabel(),
                        TextEntry::make('payment_by')->label(__('Payment Type'))
                            ->visible(fn(PrevUserRequest $record) => filled($record->payment_by))
                            ->inlineLabel(),
                        TextEntry::make('payment_approval_id')->label(__('Payment Approval Number'))
                            ->inlineLabel(),
                    ])
                    ->columns(1),
                Section::make(__('Requester details'))
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('requesterName')->label(__('Name'))
                            ->inlineLabel(),
                        TextEntry::make('normalized_mobile')->label(__('Mobile Phone'))
                            ->copyable()
                            ->copyableState(fn(PrevUserRequest $record) => $record->normalizedMobile)
                            ->copyMessage(__('filament::components/copyable.messages.copied'))
                            ->copyMessageDuration(1500)
                            ->inlineLabel(),
                        TextEntry::make('email')->label(__('Email'))
                            ->inlineLabel(),
                        TextEntry::make('full_address')->label(__('Full Address'))
                            ->inlineLabel()
                            ->columnSpanFull(),
                        TextEntry::make('other_address')
                            ->label(fn() => __('City') . ', ' . __('Street') . ', ' . __('Number'))
                            ->state(fn(PrevUserRequest $record): string => ($record->city ?? '-') . ', ' . ($record->street ?? '-') . ', ' . ($record->number ?? '-'))
                            ->inlineLabel()
                            ->columnSpanFull(),
                        TextEntry::make('dog.full_name')->label(__('dog/model/general.labels.singular'))
                            ->tooltip(fn(PrevUserRequest $record) => __('Copy Sagir ') . $record->sagirID)
                            ->formatStateUsing(fn(PrevUserRequest $record) => $record->dog->full_name . ' / ' . $record->sagirID)
                            ->copyable()
                            ->copyableState(fn(PrevUserRequest $record) => $record->sagirID)
                            ->copyMessage(__('filament::components/copyable.messages.copied'))
                            ->copyMessageDuration(1500)
                            ->inlineLabel(),
                        TextEntry::make('dog_name')->label(__('Dog name'))
                            ->inlineLabel(),
                        TextEntry::make('owner_name')->label(__('Owner Name'))
                            ->inlineLabel(),
                        TextEntry::make('owner.name')->label(__('Associated Owner User'))
                            ->inlineLabel(),
                        TextEntry::make('owner.mobile_phone')->label(__('Owner Phone'))
                            ->copyable()
                            ->copyableState(fn(PrevUserRequest $record) => $record->owner?->mobile_phone)
                            ->copyMessage(__('filament::components/copyable.messages.copied'))
                            ->copyMessageDuration(1500)
                            ->inlineLabel(),
                    ])
                    ->columns(1),
                Section::make(__('User by Phone'))
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('userByPhone.full_name')->label(__('Name'))
                            ->inlineLabel(),
                        TextEntry::make('normalized_mobile')->label(__('Mobile Phone'))
                            ->copyable()
                            ->copyableState(fn(PrevUserRequest $record) => $record->normalizedMobile)
                            ->copyMessage(__('filament::components/copyable.messages.copied'))
                            ->copyMessageDuration(1500)
                            ->inlineLabel(),
                        TextEntry::make('userByPhone.email')->label(__('Email'))
                            ->copyable(fn(PrevUserRequest $record) => $record->userByPhone?->email ? true : false)
                            ->copyableState(fn(PrevUserRequest $record) => $record->userByPhone?->email)
                            ->copyMessage(__('filament::components/copyable.messages.copied'))
                            ->copyMessageDuration(1500)
                            ->inlineLabel(),
                        TextEntry::make('userByPhone.address')->label(__('Full Address'))
                            ->inlineLabel()
                            ->columnSpanFull(),
                    ])
                    ->hidden(fn(PrevUserRequest $record) => $record->mobile_phone === null || $record->mobile_phone === '')
                    ->columns(1),
                Section::make(__('Dates'))
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('payment_date_time')->label(__('Payment Date'))->dateTime()
                            ->inlineLabel(),
                        TextEntry::make('record_date_time')->label(__('Recorded at'))->dateTime()
                            ->inlineLabel(),
                        TextEntry::make('created_at')->label(__('Created at'))
                            ->dateTime()
                            ->inlineLabel(),
                        TextEntry::make('updated_at')->label(__('Updated at'))
                            ->dateTime()
                            ->inlineLabel(),
                        TextEntry::make('deleted_at')->label(__('Deleted at'))
                            ->dateTime()
                            ->hidden(fn(PrevUserRequest $record) => $record->deleted_at === null)
                            ->inlineLabel(),
                        IconEntry::make('IsDone')->label(__('Is Done'))->boolean()
                            ->inlineLabel(),
                        TextEntry::make('doneBy.name')->label(__('Done By'))
                            ->inlineLabel(),
                        TextEntry::make('DoneDate')->label(__('Done Date'))->dateTime()->placeholder('-')
                            ->inlineLabel(),
                    ])
                    ->columns(1),
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
