<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BreedingInquiryResource\Pages;
use App\Models\BreedingInquiry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BreedingInquiryResource extends Resource
{
    protected static ?string $model = BreedingInquiry::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return __('Breeding Inquiry');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Breeding Inquiries');
    }

    public static function getNavigationGroup(): string
    {
        return __('Breedings Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Breeding Inquiries');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\TextInput::make('prev_user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('female_sagir_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('male_sagir_id')
                    ->numeric(),
                Forms\Components\TextInput::make('litter_report_name')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('breeding_date'),
                Forms\Components\DatePicker::make('birthing_date'),
                Forms\Components\TextInput::make('puppies'),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255)
                    ->default('draft'),
                Forms\Components\DateTimePicker::make('submitted_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('prev_user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('female_sagir_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('male_sagir_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('litter_report_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('breeding_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('birthing_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('submitted_at')
                    ->dateTime()
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
            'index' => Pages\ListBreedingInquiries::route('/'),
            'create' => Pages\CreateBreedingInquiry::route('/create'),
            'view' => Pages\ViewBreedingInquiry::route('/{record}'),
            'edit' => Pages\EditBreedingInquiry::route('/{record}/edit'),
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
