<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevTitleResource\Pages;
//use App\Filament\Resources\PrevTitleResource\RelationManagers;
use App\Models\PrevTitle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Enums\FiltersLayout;
use App\Filament\Exports\PrevTitleExporter;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;

class PrevTitleResource extends Resource
{
    protected static ?string $model = PrevTitle::class;

    public static function getModelLabel(): string
    {
        return __('Title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Titles');
    }

    public static function getNavigationGroup(): string
    {
        return __('Shows Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Title Types');
    }

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

//    public static function getNavigationBadge(): ?string
//    {
//        return (string) static::$model::count();
//    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(6)->schema([
                    Forms\Components\TextInput::make('DataID')
                        ->required()
                        ->numeric(),
                    Forms\Components\TextInput::make('TitleCode')
                        ->numeric(),
                    Forms\Components\TextInput::make('TitleName')
                        ->maxLength(200),
                    Forms\Components\TextInput::make('TitleDesc')
                        ->maxLength(200),
                    Forms\Components\DateTimePicker::make('ModificationDateTime'),
                    Forms\Components\DateTimePicker::make('CreationDateTime'),
                    Forms\Components\Textarea::make('Remark')
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
             ->modifyQueryUsing(function (Builder $query) {
                 return $query
                 ->withCount('awarding')
                 ;
             })
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('DataID')
                    ->label(__('DataID'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('TitleCode')
                    ->label(__('Title Code'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('TitleName')
                    ->label(__('Title Name'))
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('TitleDesc')
                    ->label(__('Description'))
                    ->searchable(isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('Remark')
                    ->label(__('Remark'))
                    ->searchable(isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: false),
                 Tables\Columns\TextColumn::make('awarding_count')
                     ->label(__('Awarded'))
                     ->counts('awarding')
                     ->numeric()
                     ->sortable(['awarding_count'])
                     ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('CreationDateTime')
                    ->label(__('Create Date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ModificationDateTime')
                    ->label(__('Modify Date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label(__('Deleted at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ],
                layout: FiltersLayout::AboveContentCollapsible
            )
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
                    ->exporter(PrevTitleExporter::class)
                    ->chunkSize(50)
                    ->modifyQueryUsing(fn (Builder $query) => $query->withCount('dogs')),
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
                    ->exporter(PrevTitleExporter::class)
                    ->chunkSize(50)
                    ->modifyQueryUsing(fn (Builder $query) => $query->withCount('dogs')),
            ])
            ->paginated([10, 25, 50, 100, 200, 250, 'all'])
            ->defaultPaginationPageOption(10)
            ->defaultSort('TitleName', 'asc')
            ->searchOnBlur()
            ->striped()
            ->deferLoading()
            // ->recordUrl(fn (PrevTitle $record): string => route('filament.admin.resources.prev-titles.view', $record), shouldOpenInNewTab: false,);
            ->recordUrl(false)
            ->filtersFormColumns(3);
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
            'index' => Pages\ListPrevTitles::route('/'),
            'create' => Pages\CreatePrevTitle::route('/create'),
            'view' => Pages\ViewPrevTitle::route('/{record}'),
            'edit' => Pages\EditPrevTitle::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
                ->withoutGlobalScopes([SoftDeletingScope::class,])
            ;
    }
}
