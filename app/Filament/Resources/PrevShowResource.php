<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevShowResource\Pages;
use App\Filament\Resources\PrevShowResource\RelationManagers;
use App\Models\PrevShow;
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
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrevShowResource extends Resource
{
    protected static ?string $model = PrevShow::class;

    protected static ?string $slug = 'prev-shows';

    protected static ?string $navigationIcon = 'fas-trophy';

    protected static ?int $navigationSort = 60;

    public static function getModelLabel(): string
    {
        return __('Show');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Shows');
    }

    public static function getNavigationGroup(): string
    {
        return __('Shows Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Shows');
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

                TextInput::make('TitleName'),

                DatePicker::make('StartDate'),

                TextInput::make('ShortDesc'),

                TextInput::make('LongDesc'),

                TextInput::make('TopImage'),

                TextInput::make('MaxRegisters')
                    ->numeric(),

                TextInput::make('ShowType')
                    ->numeric(),

                TextInput::make('ClubID')
                    ->numeric(),

                DatePicker::make('EndRegistrationDate'),

                TextInput::make('ShowStatus')
                    ->numeric(),

                DatePicker::make('EndDate'),

                TextInput::make('ShowPrice')
                    ->numeric(),

                TextInput::make('Dog2Price1')
                    ->required()
                    ->numeric(),

                TextInput::make('Dog2Price2')
                    ->required()
                    ->numeric(),

                TextInput::make('Dog2Price3')
                    ->required()
                    ->numeric(),

                TextInput::make('Dog2Price4')
                    ->required()
                    ->numeric(),

                TextInput::make('Dog2Price5')
                    ->required()
                    ->numeric(),

                TextInput::make('Dog2Price6')
                    ->required()
                    ->numeric(),

                TextInput::make('Dog2Price7')
                    ->required()
                    ->numeric(),

                TextInput::make('Dog2Price8')
                    ->required()
                    ->numeric(),

                TextInput::make('Dog2Price9')
                    ->required()
                    ->numeric(),

                TextInput::make('Dog2Price10')
                    ->required()
                    ->numeric(),

                TextInput::make('CouplesPrice')
                    ->numeric(),

                TextInput::make('BGidulPrice')
                    ->numeric(),

                TextInput::make('ZezaimPrice')
                    ->numeric(),

                TextInput::make('YoungPrice')
                    ->numeric(),

                TextInput::make('MoreDogsPrice')
                    ->numeric(),

                TextInput::make('MoreDogsPrice2')
                    ->numeric(),

                TextInput::make('TicketCost')
                    ->numeric(),

                TextInput::make('IsExtraTickets'),

                TextInput::make('IsParking'),

                TextInput::make('MoreTicketsSelect'),

                TextInput::make('ParkingSelect'),

                TextInput::make('PeototCost')
                    ->numeric(),

                TextInput::make('FreeTextDesc'),

                TextInput::make('start_from_index'),

                TextInput::make('location'),

                TextInput::make('Check_all_members')
                    ->required()
                    ->integer(),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn (?PrevShow $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn (?PrevShow $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with(['club'])->withCount(['showArenas', 'showClasses', 'registrations', 'showDogs', 'results']);
            })
            ->columns([
                TextColumn::make('TitleName')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('club.Name')
                    ->label('Club')
                    ->toggleable(),
                TextColumn::make('show_arenas_count')
                    ->label('Arenas')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('show_classes_count')
                    ->label('Classes')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('registrations_count')
                    ->label('Registrations')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('show_dogs_count')
                    ->label('Show Dogs')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('results_count')
                    ->label('Results')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('DataID'),

                TextColumn::make('ModificationDateTime')
                    ->date(),

                TextColumn::make('CreationDateTime')
                    ->date(),

                TextColumn::make('TitleName'),

                TextColumn::make('StartDate')
                    ->date(),

                TextColumn::make('ShortDesc'),

                TextColumn::make('LongDesc'),

                TextColumn::make('TopImage'),

                TextColumn::make('MaxRegisters'),

                TextColumn::make('ShowType'),

                TextColumn::make('ClubID'),

                TextColumn::make('EndRegistrationDate')
                    ->date(),

                TextColumn::make('ShowStatus'),

                TextColumn::make('EndDate')
                    ->date(),

                TextColumn::make('ShowPrice'),

                TextColumn::make('Dog2Price1'),

                TextColumn::make('Dog2Price2'),

                TextColumn::make('Dog2Price3'),

                TextColumn::make('Dog2Price4'),

                TextColumn::make('Dog2Price5'),

                TextColumn::make('Dog2Price6'),

                TextColumn::make('Dog2Price7'),

                TextColumn::make('Dog2Price8'),

                TextColumn::make('Dog2Price9'),

                TextColumn::make('Dog2Price10'),

                TextColumn::make('CouplesPrice'),

                TextColumn::make('BGidulPrice'),

                TextColumn::make('ZezaimPrice'),

                TextColumn::make('YoungPrice'),

                TextColumn::make('MoreDogsPrice'),

                TextColumn::make('MoreDogsPrice2'),

                TextColumn::make('TicketCost'),

                TextColumn::make('IsExtraTickets'),

                TextColumn::make('IsParking'),

                TextColumn::make('MoreTicketsSelect'),

                TextColumn::make('ParkingSelect'),

                TextColumn::make('PeototCost'),

                TextColumn::make('FreeTextDesc'),

                TextColumn::make('start_from_index'),

                TextColumn::make('location'),

                ImageColumn::make('banner_image'),

                TextColumn::make('Check_all_members'),
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
            'index' => Pages\ListPrevShows::route('/'),
            'create' => Pages\CreatePrevShow::route('/create'),
            'edit' => Pages\EditPrevShow::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ArenasRelationManager::class,
            RelationManagers\ClassesRelationManager::class,
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
