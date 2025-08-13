<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevShowPaymentResource\Pages;
use App\Models\PrevShowPayment;
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

class PrevShowPaymentResource extends Resource
{
    protected static ?string $model = PrevShowPayment::class;

    protected static ?string $slug = 'prev-show-payments';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('DataID')
                    ->required()
                    ->integer(),

                DatePicker::make('ModificationDateTime'),

                DatePicker::make('CreationDateTime'),

                TextInput::make('SagirID')
                    ->numeric(),

                TextInput::make('RegistrationID')
                    ->numeric(),

                TextInput::make('DogID')
                    ->numeric(),

                TextInput::make('PaymentAmount')
                    ->numeric(),

                TextInput::make('Last4Digits'),

                TextInput::make('OwnerSocialID'),

                TextInput::make('NameOnCard'),

                TextInput::make('BuyerIP'),

                TextInput::make('PaymentSubject'),

                TextInput::make('CartKey'),

                TextInput::make('PaymentStatus'),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?PrevShowPayment $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?PrevShowPayment $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
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

                TextColumn::make('SagirID'),

                TextColumn::make('RegistrationID'),

                TextColumn::make('DogID'),

                TextColumn::make('PaymentAmount'),

                TextColumn::make('Last4Digits'),

                TextColumn::make('OwnerSocialID'),

                TextColumn::make('NameOnCard'),

                TextColumn::make('BuyerIP'),

                TextColumn::make('PaymentSubject'),

                TextColumn::make('CartKey'),

                TextColumn::make('PaymentStatus'),
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
            'index' => Pages\ListPrevShowPayments::route('/'),
            'create' => Pages\CreatePrevShowPayment::route('/create'),
            'edit' => Pages\EditPrevShowPayment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
