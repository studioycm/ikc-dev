<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevDogResource\Pages;
use App\Filament\Resources\PrevDogResource\RelationManagers;
use App\Models\PrevColor;
use App\Models\PrevDog;
use App\Models\PrevBreed;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
// use App\Filament\Exports\DogExporter;
// use App\Filament\Imports\DogImporter;
// use Filament\Tables\Actions\ExportAction;
// use Filament\Tables\Actions\ImportAction;

class PrevDogResource extends Resource
{
    protected static ?string $model = PrevDog::class;

    protected static ?string $label = 'Dog';
    protected static ?string $pluralLabel = 'Dogs';

    protected static ?string $navigationGroup = 'Dogs Management';

    protected static ?string $navigationLabel = 'Dogs';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'fas-dog';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                Tables\Columns\TextColumn::make('id')
                    ->label('id')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ModificationDateTime')
                    ->label('Modification Date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('CreationDateTime')
                    ->label('Creation Date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('SagirPrefix')
                    ->label('Prefix')
                    ->searchable(),
                Tables\Columns\TextColumn::make('SagirID')
                    ->label('Sagir')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('Heb_Name')
                    ->label('Hebrew Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Eng_Name')
                    ->label('English Name')    
                    ->searchable(),
                Tables\Columns\TextColumn::make('BeitGidulID')
                    ->label('Beit Gidul ID')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(),
                Tables\Columns\TextColumn::make('BeitGidulName')
                    ->label('Beit Gidul')
                    ->searchable(),
                Tables\Columns\TextColumn::make('RegDate')
                    ->label('Regiestration Date')
                    ->since()
                    ->dateTimeTooltip()
                    ->sortable(),
                Tables\Columns\TextColumn::make('BirthDate')
                    ->label('Birth Date')
                    ->date()
                    ->sinceTooltip()
                    ->sortable(),
                Tables\Columns\TextColumn::make('breed.BreedName')
                    ->label('Breed')
                    ->sortable(),
                Tables\Columns\TextColumn::make('Sex')
                    ->sortable(),
                Tables\Columns\TextColumn::make('color.ColorNameHE')
                    ->label('Color')
                    ->sortable(),
                Tables\Columns\TextColumn::make('hair.HairNameHE')
                    ->label('Hair')
                    ->sortable(),
                Tables\Columns\TextColumn::make('SupplementarySign')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('GrowerId')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('CurrentOwnerId')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('OwnershipDate')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('father.Eng_Name')
                    ->label('Father')
                    // ->description(fn (PrevDog $record): string => $record->father->Heb_Name ?? '', position: 'under')
                    ->sortable(),
                Tables\Columns\TextColumn::make('mother.Eng_Name')
                    ->label('Mother')
                    // ->description(fn (PrevDog $record): string => $record->mother->Heb_Name ?? '', position: 'under')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ShowsCount')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('Pelvis')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ImportNumber')
                    ->searchable(),
                Tables\Columns\TextColumn::make('SCH')
                    ->label('SCH')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(),
                Tables\Columns\TextColumn::make('RemarkCode')
                    ->label('Remark Code')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(),
                Tables\Columns\TextColumn::make('GenderID')
                    ->label('Gender ID')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('SizeID')
                    ->label('Size ID')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ProfileImage')
                    ->label('Profile Image')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('GroupID')
                    ->label('Group ID')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(),
                Tables\Columns\TextColumn::make('IsMagPass')
                    ->label('Mag Pass')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('MagDate')
                    ->label('Mag Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('MagJudge')
                    ->label('Mag Judge'),
                Tables\Columns\TextColumn::make('MagPlace')
                    ->label('Mag Place'),
                Tables\Columns\TextColumn::make('DnaID')
                    ->label('DNA ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Chip')
                    ->label('Chip')
                    ->searchable(),
                Tables\Columns\TextColumn::make('GidulShowType')
                    ->label('Gidul Show'),
                Tables\Columns\TextColumn::make('pedigree_color')
                    ->label('Pedigree Color'),
                Tables\Columns\TextColumn::make('PedigreeNotes')
                    ->label('Pedigree Notes')
                    ->limit(200)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                
                        // Only render the tooltip if the column content exceeds the length limit.
                        return $state;
                    }),
                Tables\Columns\TextColumn::make('HealthNotes')
                    ->label('Health Notes')
                    ->limit(200)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                
                        // Only render the tooltip if the column content exceeds the length limit.
                        return $state;
                    }),
                Tables\Columns\TextColumn::make('Status')
                    ->sortable(),
                Tables\Columns\TextColumn::make('Image2')
                    ->label('Profile Image 2')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('TitleName')
                    ->label('Title Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Breeder_Name')
                    ->label('Breeder Name'),
                Tables\Columns\TextColumn::make('BreedID')
                    ->label('Breed ID - check')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sheger_id')
                    ->label('Sheger ID')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
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
                Tables\Columns\IconColumn::make('encoding')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('is_correct')
                    ->label('Is Correct'),
                Tables\Columns\TextColumn::make('message_test')
                    ->label('Message Test')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('not_relevant')
                    ->label('Not Relevant')
                    ->boolean(),
                Tables\Columns\TextColumn::make('IsMagPass_2')
                    ->label('Mag Pass 2')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(),
                Tables\Columns\TextColumn::make('MagDate_2')
                    ->label('Mag Date 2')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('MagJudge_2')
                    ->label('Mag Judge 2'),
                Tables\Columns\TextColumn::make('MagPlace_2')
                    ->label('Mag Place 2'),
                Tables\Columns\TextColumn::make('PedigreeNotes_2')
                    ->label('Pedigree Notes 2')
                    ->limit(200)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                
                        // Only render the tooltip if the column content exceeds the length limit.
                        return $state;
                    }),
                Tables\Columns\TextColumn::make('Notes_2')
                    ->label('Notes 2')
                    ->limit(200)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                
                        // Only render the tooltip if the column content exceeds the length limit.
                        return $state;
                    }),
                Tables\Columns\IconColumn::make('red_pedigree')
                    ->label('Red Pedigree')
                    ->boolean(),
                Tables\Columns\TextColumn::make('Chip_2')
                    ->label('Chip 2')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Foreign_Breeder_name')
                    ->label('Foreign Breeder'),
                Tables\Columns\TextColumn::make('Breeding_ManagerID')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
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
