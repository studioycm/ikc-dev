<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevShowArenaResource\Pages;
use App\Models\PrevShowArena;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrevShowArenaResource extends Resource
{
    protected static ?string $model = PrevShowArena::class;

    protected static ?string $slug = 'prev-show-arenas';

    protected static ?string $navigationIcon = 'fas-border-all';

    protected static ?int $navigationSort = 61;

    public static function getModelLabel(): string
    {
        return __('Show Arena');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Show Arenas');
    }

    public static function getNavigationGroup(): string
    {
        return __('Shows Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Arenas');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('DataID')
                    ->required()
                    ->integer(),

                DatePicker::make('ModificationDateTime'),

                DatePicker::make('CreationDateTime'),

                TextInput::make('ShowID')
                    ->integer(),

                TextInput::make('GroupName'),

                TextInput::make('GroupParentID')
                    ->integer(),

                TextInput::make('ClassID')
                    ->integer(),

                TextInput::make('OrderID')
                    ->integer(),

                TextInput::make('ArenaType')
                    ->integer(),

                TextInput::make('ManagerPass'),

                TextInput::make('JudgeID')
                    ->integer(),

                DatePicker::make('arena_date'),

                DatePicker::make('OrderTime'),

                TextInput::make('ShowsDB_id')
                    ->required()
                    ->integer(),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?PrevShowArena $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?PrevShowArena $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('DataID'),

                TextColumn::make('ModificationDateTime')
                    ->date(),

                TextColumn::make('CreationDateTime')
                    ->date(),

                TextColumn::make('ShowID'),

                TextColumn::make('GroupName'),

                TextColumn::make('GroupParentID'),

                TextColumn::make('ClassID'),

                TextColumn::make('OrderID'),

                TextColumn::make('ArenaType'),

                TextColumn::make('ManagerPass'),

                TextColumn::make('JudgeID'),

                TextColumn::make('arena_date')
                    ->date(),

                TextColumn::make('OrderTime')
                    ->date(),

                TextColumn::make('ShowsDB_id'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrevShowArenas::route('/'),
            'create' => Pages\CreatePrevShowArena::route('/create'),
            'edit' => Pages\EditPrevShowArena::route('/{record}/edit'),
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
