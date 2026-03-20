<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevUserActivityResource\Pages;
use App\Models\PrevUser;
use App\Models\PrevUserActivity;
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
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrevUserActivityResource extends Resource
{
    protected static ?string $model = PrevUserActivity::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $recordTitleAttribute = 'Activity_Type';

    public static function getModelLabel(): string
    {
        return __('User Activity');
    }

    public static function getPluralModelLabel(): string
    {
        return __('User Activities');
    }

    public static function getNavigationGroup(): string
    {
        return __('Users Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('User Activities');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Activity details'))
                    ->schema([
                        Forms\Components\Select::make('UserID')
                            ->label(__('User'))
                            ->searchable()
                            ->getSearchResultsUsing(fn(string $search): array => PrevUser::selectOptions($search, 50))
                            ->getOptionLabelUsing(fn($value): ?string => PrevUser::query()->find($value)?->name),
                        Forms\Components\Select::make('CreatedBy')
                            ->label(__('Created By'))
                            ->searchable()
                            ->getSearchResultsUsing(fn(string $search): array => PrevUser::selectOptions($search, 50))
                            ->getOptionLabelUsing(fn($value): ?string => PrevUser::query()->find($value)?->name),
                        Forms\Components\TextInput::make('Activity_Type')
                            ->label(__('Activity Type'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('UserIP')
                            ->label(__('User IP'))
                            ->maxLength(255),
                        Forms\Components\DateTimePicker::make('CreationDateTime')
                            ->label(__('Created At'))
                            ->seconds(false),
                        Forms\Components\Toggle::make('Is_Payment')->label(__('Is Payment')),
                        Forms\Components\Toggle::make('Is_Show')->label(__('Is Show')),
                        Forms\Components\Toggle::make('Is_Study')->label(__('Is Study')),
                        Forms\Components\Textarea::make('Activity_Desc')->label(__('Activity Description'))->rows(3)->columnSpanFull(),
                        Forms\Components\Textarea::make('Activity_Log')->label(__('Activity Log'))->rows(6)->columnSpanFull(),
                    ])
                    ->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label(__('ID'))->numeric(decimalPlaces: 0, thousandsSeparator: '')->sortable(),
                TextColumn::make('user.name')
                    ->label(__('User'))
                    ->sortable(['last_name', 'first_name'])
                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('Activity_Type')->label(__('Activity Type'))->searchable()->sortable(),
                TextColumn::make('Activity_Desc')->label(__('Description'))->wrap()->limit(50)->toggleable(),
                TextColumn::make('CreationDateTime')->label(__('Created At'))->dateTime()->sortable(),
                TextColumn::make('createdBy.name')
                    ->label(__('Created By'))
                    ->sortable(['last_name', 'first_name'])
                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
                IconColumn::make('Is_Payment')->label(__('Payment'))->boolean(),
                IconColumn::make('Is_Show')->label(__('Show'))->boolean(),
                IconColumn::make('Is_Study')->label(__('Study'))->boolean(),
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
            ->defaultSort('CreationDateTime', 'desc')
            ->searchOnBlur()
            ->striped();
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make(__('Activity details'))
                    ->schema([
                        TextEntry::make('user.name')->label(__('User')),
                        TextEntry::make('createdBy.name')->label(__('Created By')),
                        TextEntry::make('Activity_Type')->label(__('Activity Type')),
                        TextEntry::make('UserIP')->label(__('User IP')),
                        TextEntry::make('CreationDateTime')->label(__('Created At'))->dateTime(),
                        IconEntry::make('Is_Payment')->label(__('Is Payment'))->boolean(),
                        IconEntry::make('Is_Show')->label(__('Is Show'))->boolean(),
                        IconEntry::make('Is_Study')->label(__('Is Study'))->boolean(),
                        TextEntry::make('Activity_Desc')->label(__('Activity Description'))->columnSpanFull(),
                        TextEntry::make('Activity_Log')->label(__('Activity Log'))->columnSpanFull(),
                        TextEntry::make('deleted_at')->label(__('Deleted At'))->since()->dateTimeTooltip()->placeholder('-'),
                    ])
                    ->columns(4),
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
            'index' => Pages\ListPrevUserActivities::route('/'),
            'create' => Pages\CreatePrevUserActivity::route('/create'),
            'view' => Pages\ViewPrevUserActivity::route('/{record}'),
            'edit' => Pages\EditPrevUserActivity::route('/{record}/edit'),
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
