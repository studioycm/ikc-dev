<?php

namespace App\Filament\Resources;

use App\Filament\Exports\PrevPaymentExporter;
use App\Filament\Resources\PrevPaymentResource\Pages;
use App\Models\PrevPayment;
use App\Models\PrevUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrevPaymentResource extends Resource
{
    protected static ?string $model = PrevPayment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $recordTitleAttribute = 'payment_topic';

    public static function getModelLabel(): string
    {
        return __('Payment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Payments');
    }

    public static function getNavigationGroup(): string
    {
        return __('Finances Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Payments');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Payment details'))
                    ->schema([
                        Forms\Components\TextInput::make('desc')
                            ->label(__('Description'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('payment_topic')
                            ->label(__('Topic'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('amount')
                            ->label(__('Cost'))
                            ->numeric()
                            ->minValue(0)
                            ->required(),
                        Forms\Components\TextInput::make('approval_number')
                            ->label(__('Approval Number'))
                            ->maxLength(255),
                        Forms\Components\DateTimePicker::make('payment_date_time')
                            ->label(__('Payment Date Time'))
                            ->seconds(false),
                        Forms\Components\TextInput::make('last4_digits')
                            ->label(__('Last 4 Digits'))
                            ->maxLength(10),
                        Forms\Components\TextInput::make('user_ip')
                            ->label(__('User IP'))
                            ->maxLength(255),
                    ])
                    ->columns(3),
                Forms\Components\Section::make(__('Payer details'))
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->label(__('First Name'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->label(__('Last Name'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label(__('Email'))
                            ->email()
                            ->maxLength(255),
                    ])
                    ->columns(3),
                Forms\Components\Section::make(__('Relations'))
                    ->schema([
                        Forms\Components\Select::make('club_id')
                            ->label(__('Club'))
                            ->relationship('club', 'Name')
                            ->searchable(['Name', 'EngName'])
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn(Model $record): string => $record->Name ?? $record->EngName ?? (string)$record->id),
                        Forms\Components\Select::make('breed_id')
                            ->label(__('Breed'))
                            ->relationship('breed', 'BreedName')
                            ->searchable(['BreedName', 'BreedNameEN', 'BreedCode'])
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn(Model $record): string => $record->BreedName ?? $record->BreedNameEN ?? (string)$record->id),
                        Forms\Components\Select::make('sagir_id')
                            ->label(__('Dog'))
                            ->relationship('dog', 'SagirID')
                            ->searchable(['SagirID', 'Heb_Name', 'Eng_Name'])
                            ->getOptionLabelFromRecordUsing(fn(Model $record): string => $record->full_name . ' #' . $record->SagirID),
                        Forms\Components\Select::make('created_by')
                            ->label(__('Created By'))
                            ->searchable()
                            ->getSearchResultsUsing(fn(string $search): array => PrevUser::selectOptions($search, 50))
                            ->getOptionLabelUsing(fn($value): ?string => PrevUser::query()->find($value)?->name),
                        Forms\Components\Select::make('updated_by')
                            ->label(__('Updated By'))
                            ->searchable()
                            ->getSearchResultsUsing(fn(string $search): array => PrevUser::selectOptions($search, 50))
                            ->getOptionLabelUsing(fn($value): ?string => PrevUser::query()->find($value)?->name),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->with(['club', 'breed', 'dog', 'createdBy', 'updatedBy']);
            })
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('approval_number')
                    ->label(__('Approval Number'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('desc')
                    ->label(__('Description'))
                    ->wrap()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('amount')
                    ->label(__('Cost'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(),
                TextColumn::make('club.Name')
                    ->label(__('Club'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('breed.BreedName')
                    ->label(__('Breed'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('dog.SagirID')
                    ->label(__('dog/model/general.labels.singular'))
                    ->description(fn(PrevPayment $record): ?string => $record->dog?->full_name)
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('payment_date_time')
                    ->label(__('Paid at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label(__('Created By'))
                    ->sortable(['users.last_name', 'users.first_name'])
                    ->searchable(['users.first_name', 'users.last_name', 'users.first_name_en', 'users.last_name_en', 'users.mobile_phone'], isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updatedBy.name')
                    ->label(__('Updated By'))
                    ->sortable(['users.last_name', 'users.first_name'])
                    ->searchable(['users.first_name', 'users.last_name', 'users.first_name_en', 'users.last_name_en', 'users.mobile_phone'], isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label(__('Deleted at'))
                    ->since()
                    ->dateTimeTooltip()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([

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
                    ->exporter(PrevPaymentExporter::class),
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
                    ->exporter(PrevPaymentExporter::class),
            ])
            ->defaultSort('payment_date_time', 'desc')
            ->searchOnBlur()
            ->striped();
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make(__('Payment details'))
                    ->schema([
                        TextEntry::make('id')->label(__('ID')),
                        TextEntry::make('approval_number')->label(__('Approval Number')),
                        TextEntry::make('payment_topic')->label(__('Topic')),
                        TextEntry::make('desc')->label(__('Description')),
                        TextEntry::make('amount')
                            ->label(__('Cost'))
                            ->numeric(decimalPlaces: 0, thousandsSeparator: ''),
                        TextEntry::make('payment_date_time')->label(__('Payment Date Time'))->dateTime(),
                        TextEntry::make('last4_digits')->label(__('Last 4 Digits')),
                        TextEntry::make('user_ip')->label(__('User IP')),
                    ])
                    ->columns(3),
                Section::make(__('Payer and relations'))
                    ->schema([
                        TextEntry::make('first_name')->label(__('First Name')),
                        TextEntry::make('last_name')->label(__('Last Name')),
                        TextEntry::make('email')->label(__('Email')),
                        TextEntry::make('club.Name')->label(__('Club')),
                        TextEntry::make('breed.BreedName')->label(__('Breed')),
                        TextEntry::make('dog.full_name')->label(__('Dog')),
                        TextEntry::make('createdBy.name')->label(__('Created By')),
                        TextEntry::make('updatedBy.name')->label(__('Updated By')),
                        TextEntry::make('deleted_at')->label(__('Deleted at'))->since()->dateTimeTooltip()->placeholder('-'),
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
            'index' => Pages\ListPrevPayments::route('/'),
            'create' => Pages\CreatePrevPayment::route('/create'),
            'view' => Pages\ViewPrevPayment::route('/{record}'),
            'edit' => Pages\EditPrevPayment::route('/{record}/edit'),
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
