<?php

namespace App\Filament\Resources\PrevDogResource\RelationManagers;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PrevDogDocumentRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $recordTitleAttribute = 'type';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Documents');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label(__('ID'))->numeric(decimalPlaces: 0, thousandsSeparator: ''),
                TextColumn::make('type')->label(__('Type'))->searchable(),
                TextColumn::make('TestDate')->label(__('Test Date'))->date(),
                TextColumn::make('judge_name')->label(__('Judge'))->toggleable(),
                TextColumn::make('grade')->label(__('Grade'))->toggleable(),
                IconColumn::make('result')
                    ->label(__('Result'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
                TextColumn::make('location')->label(__('Location'))->toggleable(),
                TextColumn::make('created_at')->label(__('Created'))->since()->toggleable(),
                TextColumn::make('updated_at')->label(__('Updated'))->since()->toggleable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('Add Document'))
                    ->form([
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
                    ])
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['SagirID'] = $this->getOwnerRecord()->SagirID;

                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('Edit'))
                    ->form([
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
                    ]),
                Tables\Actions\DeleteAction::make()->label(__('Delete')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
