<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevDogResource\Pages;
use App\Filament\Resources\PrevDogResource\RelationManagers;
use App\Models\PrevDog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrevDogResource extends Resource
{
    protected static ?string $model = PrevDog::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('DataID')
                    ->required()
                    ->numeric(),
                Forms\Components\DateTimePicker::make('ModificationDateTime'),
                Forms\Components\DateTimePicker::make('CreationDateTime'),
                Forms\Components\TextInput::make('SagirID')
                    ->numeric(),
                Forms\Components\TextInput::make('Heb_Name')
                    ->maxLength(200),
                Forms\Components\TextInput::make('Eng_Name')
                    ->maxLength(200),
                Forms\Components\TextInput::make('BeitGidulID')
                    ->numeric(),
                Forms\Components\TextInput::make('BeitGidulName')
                    ->maxLength(200),
                Forms\Components\DateTimePicker::make('RegDate'),
                Forms\Components\DateTimePicker::make('BirthDate'),
                Forms\Components\TextInput::make('RaceID')
                    ->numeric(),
                Forms\Components\TextInput::make('Sex')
                    ->maxLength(200),
                Forms\Components\TextInput::make('ColorID')
                    ->numeric(),
                Forms\Components\TextInput::make('HairID')
                    ->numeric(),
                Forms\Components\TextInput::make('SupplementarySign')
                    ->numeric(),
                Forms\Components\TextInput::make('GrowerId')
                    ->numeric(),
                Forms\Components\TextInput::make('CurrentOwnerId')
                    ->numeric(),
                Forms\Components\DateTimePicker::make('OwnershipDate'),
                Forms\Components\TextInput::make('FatherSAGIR')
                    ->numeric(),
                Forms\Components\TextInput::make('MotherSAGIR')
                    ->numeric(),
                Forms\Components\TextInput::make('ShowsCount')
                    ->numeric(),
                Forms\Components\TextInput::make('Pelvis')
                    ->maxLength(200),
                Forms\Components\Textarea::make('Notes')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('ImportNumber')
                    ->maxLength(200),
                Forms\Components\TextInput::make('SCH')
                    ->numeric(),
                Forms\Components\TextInput::make('RemarkCode')
                    ->numeric(),
                Forms\Components\TextInput::make('GenderID')
                    ->numeric(),
                Forms\Components\TextInput::make('SizeID')
                    ->numeric(),
                Forms\Components\TextInput::make('ProfileImage')
                    ->maxLength(300),
                Forms\Components\TextInput::make('GroupID')
                    ->numeric(),
                Forms\Components\TextInput::make('IsMagPass')
                    ->numeric(),
                Forms\Components\DateTimePicker::make('MagDate'),
                Forms\Components\TextInput::make('MagJudge')
                    ->maxLength(200),
                Forms\Components\TextInput::make('MagPlace')
                    ->maxLength(200),
                Forms\Components\TextInput::make('DnaID')
                    ->maxLength(200),
                Forms\Components\TextInput::make('Chip')
                    ->maxLength(200),
                Forms\Components\TextInput::make('GidulShowType')
                    ->maxLength(200),
                Forms\Components\TextInput::make('pedigree_color')
                    ->maxLength(30),
                Forms\Components\TextInput::make('PedigreeNotes')
                    ->maxLength(4000),
                Forms\Components\TextInput::make('HealthNotes')
                    ->maxLength(4000),
                Forms\Components\TextInput::make('Status')
                    ->maxLength(200),
                Forms\Components\TextInput::make('Image2')
                    ->maxLength(300),
                Forms\Components\TextInput::make('TitleName')
                    ->maxLength(300),
                Forms\Components\TextInput::make('Breeder_Name')
                    ->maxLength(300),
                Forms\Components\TextInput::make('BreedID')
                    ->numeric(),
                Forms\Components\TextInput::make('sheger_id')
                    ->numeric(),
                Forms\Components\TextInput::make('sagir_prefix')
                    ->maxLength(20),
                Forms\Components\Toggle::make('encoding'),
                Forms\Components\TextInput::make('is_correct')
                    ->maxLength(255),
                Forms\Components\Textarea::make('message')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('message_test')
                    ->maxLength(255),
                Forms\Components\Toggle::make('not_relevant'),
                Forms\Components\TextInput::make('IsMagPass_2')
                    ->numeric(),
                Forms\Components\DateTimePicker::make('MagDate_2'),
                Forms\Components\TextInput::make('MagJudge_2')
                    ->maxLength(255),
                Forms\Components\TextInput::make('MagPlace_2')
                    ->maxLength(255),
                Forms\Components\TextInput::make('PedigreeNotes_2')
                    ->maxLength(1000),
                Forms\Components\TextInput::make('Notes_2')
                    ->maxLength(1000),
                Forms\Components\Toggle::make('red_pedigree'),
                Forms\Components\TextInput::make('Chip_2')
                    ->maxLength(255),
                Forms\Components\TextInput::make('Foreign_Breeder_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('Breeding_ManagerID')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('DataID')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ModificationDateTime')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('CreationDateTime')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('SagirID')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('Heb_Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Eng_Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('BeitGidulID')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('BeitGidulName')
                    ->searchable(),
                Tables\Columns\TextColumn::make('RegDate')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('BirthDate')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('RaceID')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('Sex')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ColorID')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('HairID')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('SupplementarySign')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('GrowerId')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('CurrentOwnerId')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('OwnershipDate')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('FatherSAGIR')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('MotherSAGIR')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ShowsCount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('Pelvis')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ImportNumber')
                    ->searchable(),
                Tables\Columns\TextColumn::make('SCH')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('RemarkCode')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('GenderID')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('SizeID')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ProfileImage')
                    ->searchable(),
                Tables\Columns\TextColumn::make('GroupID')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('IsMagPass')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('MagDate')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('MagJudge')
                    ->searchable(),
                Tables\Columns\TextColumn::make('MagPlace')
                    ->searchable(),
                Tables\Columns\TextColumn::make('DnaID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Chip')
                    ->searchable(),
                Tables\Columns\TextColumn::make('GidulShowType')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pedigree_color')
                    ->searchable(),
                Tables\Columns\TextColumn::make('PedigreeNotes')
                    ->searchable(),
                Tables\Columns\TextColumn::make('HealthNotes')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Image2')
                    ->searchable(),
                Tables\Columns\TextColumn::make('TitleName')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Breeder_Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('BreedID')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sheger_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sagir_prefix')
                    ->searchable(),
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
                Tables\Columns\IconColumn::make('encoding')
                    ->boolean(),
                Tables\Columns\TextColumn::make('is_correct')
                    ->searchable(),
                Tables\Columns\TextColumn::make('message_test')
                    ->searchable(),
                Tables\Columns\IconColumn::make('not_relevant')
                    ->boolean(),
                Tables\Columns\TextColumn::make('IsMagPass_2')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('MagDate_2')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('MagJudge_2')
                    ->searchable(),
                Tables\Columns\TextColumn::make('MagPlace_2')
                    ->searchable(),
                Tables\Columns\TextColumn::make('PedigreeNotes_2')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Notes_2')
                    ->searchable(),
                Tables\Columns\IconColumn::make('red_pedigree')
                    ->boolean(),
                Tables\Columns\TextColumn::make('Chip_2')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Foreign_Breeder_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Breeding_ManagerID')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPrevDogs::route('/'),
            'create' => Pages\CreatePrevDog::route('/create'),
            'edit' => Pages\EditPrevDog::route('/{record}/edit'),
        ];
    }
}
