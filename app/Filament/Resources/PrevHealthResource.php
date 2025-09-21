<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevHealthResource\Pages;
use App\Models\PrevHealth;
use Filament\Forms\Components\Checkbox;
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

class PrevHealthResource extends Resource
{
    protected static ?string $model = PrevHealth::class;

    protected static ?string $navigationIcon = 'fas-stethoscope';

    protected static ?int $navigationSort = 10;

    public static function getNavigationGroup(): string
    {
        return __('dog/model/general.labels.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('Health Records');
    }

    public static function getModelLabel(): string
    {
        return __('Health Record');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Health Records');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('DataID')
                    ->required()
                    ->integer(),

                TextInput::make('type'),

                DatePicker::make('ModificationDateTime'),

                DatePicker::make('CreationDateTime'),

                TextInput::make('SagirID')
                    ->numeric(),

                DatePicker::make('TestDate'),

                TextInput::make('TestFile'),

                TextInput::make('Notes'),

                TextInput::make('ImageResultID'),

                Checkbox::make('show_in_paper'),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?PrevHealth $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?PrevHealth $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('DataID'),

                TextColumn::make('type'),

                TextColumn::make('ModificationDateTime')
                    ->date(),

                TextColumn::make('CreationDateTime')
                    ->date(),

                TextColumn::make('SagirID'),

                TextColumn::make('TestDate')
                    ->date(),

                TextColumn::make('TestFile'),

                TextColumn::make('Notes'),

                TextColumn::make('ImageResultID'),

                TextColumn::make('show_in_paper'),
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
            'index' => Pages\ListPrevHealths::route('/'),
            'create' => Pages\CreatePrevHealth::route('/create'),
            'edit' => Pages\EditPrevHealth::route('/{record}/edit'),
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
