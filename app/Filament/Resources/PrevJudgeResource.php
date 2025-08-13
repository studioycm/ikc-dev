<?php

namespace App\Filament\Resources;

    use App\Filament\Resources\PrevJudgeResource\Pages;
    use App\Models\PrevJudge;
    use Filament\Forms\Components\DatePicker;
    use Filament\Forms\Components\TextInput;
    use Filament\Forms\Form;
    use Filament\Resources\Resource;
    use Filament\Tables\Actions\BulkActionGroup;
    use Filament\Tables\Actions\DeleteAction;
    use Filament\Tables\Actions\DeleteBulkAction;
    use Filament\Tables\Actions\EditAction;
    use Filament\Tables\Columns\TextColumn;
    use Filament\Tables\Table;

    class PrevJudgeResource extends Resource {
        protected static ?string $model = PrevJudge::class;

        protected static ?string $slug = 'prev-judges';

        protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

        PUBLIC static function form(Form $form): Form
        {
        return $form
        ->schema([//
        TextInput::make('DataID')
        ->required()
        ->integer(),

        DatePicker::make('ModificationDateTime'),

        DatePicker::make('CreationDateTime'),

        TextInput::make('JudgeNameHE')
        ->required(),

        TextInput::make('JudgeNameEN')
        ->required(),

        TextInput::make('Country'),

        TextInput::make('BreedID')
        ->integer(),

        TextInput::make('Email'),
        ]);
        }

        PUBLIC static function table(Table $table): Table
        {
        return $table
        ->columns([
        TextColumn::make('DataID'),

        TextColumn::make('ModificationDateTime')
        ->date(),

        TextColumn::make('CreationDateTime')
        ->date(),

        TextColumn::make('JudgeNameHE'),

        TextColumn::make('JudgeNameEN'),

        TextColumn::make('Country'),

        TextColumn::make('BreedID'),

        TextColumn::make('Email'),
        ])
        ->filters([
        //
        ])
        ->actions([
        EditAction::make(),
        DeleteAction::make(),
        ])
        ->bulkActions([
        BulkActionGroup::make([
        DeleteBulkAction::make(),
        ]),
        ]);
        }

        public static function getPages(): array
        {
        return [
        'index' => Pages\ListPrevJudges::route('/'),
'create' => Pages\CreatePrevJudge::route('/create'),
'edit' => Pages\EditPrevJudge::route('/{record}/edit'),
        ];
        }

        PUBLIC static function getGloballySearchableAttributes(): array
        {
        return [];
        }
    }
