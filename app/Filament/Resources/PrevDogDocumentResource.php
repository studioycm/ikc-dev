<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevDogDocumentResource\Pages;
use App\Models\PrevDogDocument;
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
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrevDogDocumentResource extends Resource
{
    protected static ?string $model = PrevDogDocument::class;

    protected static ?string $navigationIcon = 'fas-file-lines';

    protected static ?int $navigationSort = 15;

    public static function getNavigationGroup(): string
    {
        return __('dog/model/general.labels.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('Dog Documents');
    }

    public static function getModelLabel(): string
    {
        return __('Dog Document');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Dog Documents');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('type')->label(__('Type'))->maxLength(255),
                DatePicker::make('TestDate')->label(__('Test Date')),
                TextInput::make('TestFile')->label(__('Test File'))->maxLength(255),
                TextInput::make('Notes')->label(__('Notes'))->maxLength(1000),
                Checkbox::make('is_maag')->label(__('Maag?')),
                DatePicker::make('maag_date')->label(__('Maag Date')),
                TextInput::make('judge_name')->label(__('Judge'))->maxLength(255),
                Checkbox::make('result')->label(__('Result')),
                TextInput::make('grade')->label(__('Grade'))->maxLength(255),
                TextInput::make('location')->label(__('Location'))->maxLength(255),
                TextInput::make('SagirID')->label(__('Sagir'))->numeric(),
                Placeholder::make('created_at')->label(__('Created'))
                    ->content(fn(?PrevDogDocument $r) => $r?->created_at?->diffForHumans() ?? '-'),
                Placeholder::make('updated_at')->label(__('Updated'))
                    ->content(fn(?PrevDogDocument $r) => $r?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label(__('ID'))->numeric(decimalPlaces: 0, thousandsSeparator: ''),
                TextColumn::make('SagirID')->label(__('Sagir'))->numeric(decimalPlaces: 0, thousandsSeparator: ''),
                TextColumn::make('type')->label(__('Type'))->searchable(),
                TextColumn::make('TestDate')->label(__('Test Date'))->date(),
                TextColumn::make('judge_name')->label(__('Judge'))->toggleable(),
                TextColumn::make('grade')->label(__('Grade'))->toggleable(),
                IconColumn::make('result')->label(__('Result'))->boolean(),
                TextColumn::make('location')->label(__('Location'))->toggleable(),
                TextColumn::make('created_at')->label(__('Created'))->since()->toggleable(),
                TextColumn::make('updated_at')->label(__('Updated'))->since()->toggleable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                ViewAction::make(),
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
            'index' => Pages\ListPrevDogDocuments::route('/'),
            'create' => Pages\CreatePrevDogDocument::route('/create'),
            'view' => Pages\ViewPrevDogDocument::route('/{record}'),
            'edit' => Pages\EditPrevDogDocument::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
