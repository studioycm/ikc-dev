<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms;
use Filament\Forms\Form;
use App\Models\PrevDog;
use App\Models\PrevTitle;
use App\Models\PrevDogTitle;
use App\Models\PrevBreed;
use App\Models\PrevColor;
use App\Models\PrevHair;
use App\Models\PrevUser;
use App\Models\PrevUserDog;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Section;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Resources\PrevDogResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\PrevDogResource\RelationManagers;
// infolist use
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\Grid as InfolistGrid;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;

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
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                ->with('owners')
                ->with('currentOwner')
                ->with('titles');
                    // ->with('duplicates')
                    // ->withCount('duplicates');
            })
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('id')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Columns\TextColumn::make('prefixed_sagir')
                    ->label('Sagir')
                    ->searchable(['SagirID'], isIndividual: true, isGlobal: false)
                    ->sortable(['sagir_prefix','SagirID']),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Full Name')
                    ->searchable(['Heb_Name','Eng_Name'], isIndividual: true, isGlobal: false),
                Tables\Columns\TextColumn::make('BeitGidulID')
                    ->label('Beit Gidul ID')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('BeitGidulName')
                    ->label('Beit Gidul')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('RegDate')
                    ->label('Regiestration Date')
                    ->date()
                    ->sinceTooltip()
                    ->sortable(),
                Tables\Columns\TextColumn::make('BirthDate')
                    ->label('Birth Date')
                    ->date()
                    ->sinceTooltip()
                    ->sortable(),
                Tables\Columns\TextColumn::make('breed.BreedName')
                    ->label('Breed')
                    ->description(function (PrevDog $record): string {
                        $breed = $record->breed;
                        $nameEn = $breed->BreedNameEN ?? '~';
                        return $nameEn;
                    }, position: 'under')
                    ->sortable(),
                Tables\Columns\TextColumn::make('color.ColorNameHE')
                    ->label('Color')
                    ->description(function (PrevDog $record): string {
                        $color = $record->color;
                        $nameEn = $color->ColorNameEN ?? '~';
                        return $nameEn;
                    }, position: 'under')
                    ->sortable(['OldCode']),
                Tables\Columns\TextColumn::make('hair.HairNameHE')
                    ->label('Hair')
                    ->description(function (PrevDog $record): string {
                        $hair = $record->hair;
                        $nameEn = $hair->HairNameEN ?? '~';
                        return $nameEn;
                    }, position: 'under')
                    ->sortable(),
                Tables\Columns\TextColumn::make('Sex')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        "ז" => 'info',
                        "נ" => 'danger',
                        default => 'gray',
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('gender')
                    ->label('Gender')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        "M" => 'info',
                        "F" => 'danger',
                        default => 'gray',
                    })
                    ->sortable(['GenderID']),
                Tables\Columns\TextColumn::make('titles.name')
                    ->label('Titles')
                    ->listWithLineBreaks() 
                    ->limitList(3)
                    ->tooltip(fn (Tables\Columns\TextColumn $column): ?string => 
                        (($state = $column->getState()) === null) ? null : 
                        (is_array($state)
                            ? (count($state) > $column->getListLimit() ? implode(' | ', $state) : null)
                            : (string) $state
                        )
                    )
                    ->searchable(['dogs_titles_db.TitleName'], isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('owners')
                    ->label('Owners')
                    ->getStateUsing(function (PrevDog $record): string {
                        // Lists the owners' names
                        return $record->owners->pluck('full_name')->implode(', ');
                    })
                    ->description(function (PrevDog $record): string {
                        // Get the first two owners' names
                        return $record->owners->pluck('id')->implode(', ');
                    })
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('father.full_name')
                    ->label('Father')
                    ->description(function (PrevDog $record): string {
                        return $record->father?->SagirID ?? 'n/a';
                    }, position: 'under')
                    ->searchable(['Eng_Name','Heb_Name','SagirID'], isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('mother.full_name')
                    ->label('Mother')
                    ->description(function (PrevDog $record): string {
                        return $record->mother?->SagirID ?? 'n/a';
                    }, position: 'under')
                    ->searchable(['Eng_Name','Heb_Name','SagirID'], isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('Chip')
                    ->label('Chip')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('DnaID')
                    ->label('DNA ID')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('ImportNumber')
                    ->label('Import Number')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('Chip_2')
                    ->label('Chip 2')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('GrowerId')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('CurrentOwnerId')
                    ->label('Owner (depracted)')
                    ->wrapHeader()
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->getStateUsing(function (PrevDog $record): string {
                        if ($record->CurrentOwnerId === null) {
                            return 'n/a';
                        }
                        $ownerName = $record->currentOwner?->first_name ?? '<unknown>';
                        $ownershipDate = $record->OwnershipDate 
                            ? \Carbon\Carbon::parse($record->OwnershipDate)->format('d-m-Y') 
                            : 'n/a';
                        $ownershipDetails = $ownerName . ' | ' . $ownershipDate;
                        return $ownershipDetails;
                    })  
                    ->description(fn (PrevDog $record): string => $record->CurrentOwnerId ? (string)((int)$record->CurrentOwnerId) : 'n/a')
                    ->sortable(['OwnershipDate'])
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('Breeder_Name')
                    ->label('Breeder Name - depracted')
                    ->wrapHeader()
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('Foreign_Breeder_name')
                    ->label('Foreign Breeder')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('Breeding_ManagerID')
                    ->label('Breeding Manager ID - check')
                    ->wrapHeader()
                    ->description(fn (PrevDog $record): string => $record->breedingManager->full_name ?? 'n/a')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('Status')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('BreedID')
                    ->label('Breed ID - depracted')
                    ->wrapHeader()
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('SizeID')
                    ->label('Size ID')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('SupplementarySign')
                    ->label('Supplementary Sign')
                    ->wrapHeader()
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ShowsCount')
                    ->label('Shows Count - check')
                    ->wrapHeader()
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('Pelvis')
                    ->label('Pelvis')
                    ->limit(200)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('SCH')
                    ->label('SCH')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('RemarkCode')
                    ->label('Remark Code')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('TitleName')
                    ->label('Titles (pre ~2010)')
                    ->wrapHeader()
                    ->separator(',')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),                
                Tables\Columns\TextColumn::make('GroupID')
                    ->label('Group ID')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('GidulShowType')
                    ->label('Gidul Show')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pedigree_color')
                    ->label('Pedigree Color')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\IconColumn::make('red_pedigree')
                    ->label('Red Pedigree')
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('PedigreeNotes')
                    ->label('Pedigree Notes')
                    ->limit(200)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('PedigreeNotes_2')
                    ->label('Pedigree Notes 2')
                    ->limit(200)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();                        
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('HealthNotes')
                    ->label('Health Notes')
                    ->limit(200)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();               
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('Notes_2')
                    ->label('Notes 2')
                    ->limit(200)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();                        
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                        })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('message_test')
                    ->label('Message Test')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sheger_id')
                    ->label('Sheger ID')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // combine the Mag columns into one column, pass will be the value/state and the rest in a description
                Tables\Columns\TextColumn::make('IsMagPass')
                    ->label('Mag')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->description(function (PrevDog $record): string {
                        $magDate = $record->MagDate ?? '~';
                        $magJudge = $record->MagJudge ?? '~';
                        $magPlace = $record->MagPlace ?? '~';
                        
                        return "{$magDate} | {$magJudge} | {$magPlace}";
                    }, position: 'under')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // combine the Mag 2 columns into one column, pass will be the value/state and the rest in a description 
                Tables\Columns\TextColumn::make('IsMagPass_2')
                    ->label('Mag 2')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->description(function (PrevDog $record): string {
                        $magDate = $record->MagDate_2 ?? '~';
                        $magJudge = $record->MagJudge_2 ?? '~';
                        $magPlace = $record->MagPlace_2 ?? '~';
                        
                        return "{$magDate} | {$magJudge} | {$magPlace}";
                    }, position: 'under')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('is_correct')
                    ->label('Is Correct')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ProfileImage')
                    ->label('Profile Image')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('Image2')
                    ->label('Profile Image 2')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('not_relevant')
                    ->label('Not Relevant')
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('encoding')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                // Tables\Columns\TextColumn::make('duplicates_count')
                //     ->label('Duplicates')
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('duplicates_list')
                //     ->label('Other Duplicate IDs')
                //     ->getStateUsing(function (PrevDog $record): string {
                //         // Lists the IDs from the duplicates collection
                //         return $record->duplicates->pluck('id')->implode(', ');
                //     })
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
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
                Filter::make('trashed')
                    ->form([
                        Forms\Components\ToggleButtons::make('trashed')
                            ->options([
                                'all'         => 'All',
                                'deleted'     => 'Deleted',
                                'not_deleted' => 'Not Deleted',
                            ])
                            ->colors([
                                'all'         => 'gray',
                                'deleted'     => 'danger',
                                'not_deleted' => 'success',
                            ])
                            ->grouped(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['trashed']) || $data['trashed'] === 'all') {
                            return $query;
                        }
                        return match ($data['trashed']) {
                            'deleted'     => $query->onlyTrashed(),
                            'not_deleted' => $query->withoutTrashed(),
                        };
                    }),
                // create a filter for "duplicates_count > 1" and "all" - duplicates_count is made by filament modifyQueryUsing and withCount() method:
                // Tables\Filters\TernaryFilter::make('duplicates_count')
                //     ->label('Duplicates')
                //     ->placeholder('All')
                //     ->trueLabel('Duplicates')
                //     ->falseLabel('No Duplicates')
                //     ->queries(
                //         true: fn (Builder $query) => $query->has('duplicates', '>', 1),
                //         false: fn (Builder $query) => $query->has('duplicates', '<=', 1),
                //         blank: fn (Builder $query) => $query,
                //     ),    
                
                // create gender ("M","F") filter with toggle buttons 
                Filter::make('gender')
                    ->form([
                        Forms\Components\ToggleButtons::make('gender')
                            ->options([
                                'all'  => 'All',
                                'M'    => 'Male',
                                'F'    => 'Female',
                                'n/a'  => 'Other',
                            ])
                            ->colors([
                                'M'    => 'info',
                                'F'    => 'danger',
                                'n/a'  => 'warning',
                            ])
                            ->grouped(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        // If no specific gender is chosen, return unfiltered results.
                        if (empty($data['gender']) || $data['gender'] === 'all') {
                            return $query;
                        }
                        
                        // Apply a raw WHERE clause that replicates the accessor's logic.
                        // Adjust the CASE statement as needed to match your accessor.
                        return $query->whereRaw("
                            CASE 
                                WHEN GenderID = 1 THEN 'M'
                                WHEN GenderID = 2 THEN 'F'
                                ELSE 'n/a'
                            END = ?
                        ", [$data['gender']]);
                    }),
                // filter by "sagir_prefix" like the "gender" filter
                Filter::make('sagir_prefix')
                    ->form([
                        Forms\Components\ToggleButtons::make('sagir_prefix')
                            ->options([
                                'all'  => 'All',
                                'ISR'    => 'ISR',
                                'IMP'    => 'IMP',
                                'APX'    => 'APX',
                                'EXT'    => 'EXT',
                                'NUL'    => 'NUL',
                            ])
                            ->colors([
                                'all'  => 'gray',
                                'ISR'    => 'info',
                                'IMP'    => Color::Purple,
                                'APX'    => 'warning',
                                'EXT'    => 'success',
                                'NUL'    => 'danger',
                            ])
                            ->grouped(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        // If no specific prefix is chosen, return unfiltered results.
                        if (empty($data['sagir_prefix']) || $data['sagir_prefix'] === 'all') {
                            return $query;
                        }
                        
                        // Apply a raw WHERE clause that replicates the accessor's logic.
                        // Adjust the CASE statement as needed to match your accessor.
                        return $query->whereRaw("
                            CASE 
                                WHEN sagir_prefix = 1 THEN 'ISR'
                                WHEN sagir_prefix = 2 THEN 'IMP'
                                WHEN sagir_prefix = 3 THEN 'APX'
                                WHEN sagir_prefix = 4 THEN 'EXT'
                                WHEN sagir_prefix = 5 THEN 'NUL'
                            END = ?
                        ", [$data['sagir_prefix']]);
                    }),
                // Relationship filters for Breed, Color, and Hair
                Tables\Filters\SelectFilter::make('breed')
                    ->label('Breed')
                    ->relationship('breed', 'BreedName')
                    ->multiple()
                    ->searchable(),
                Tables\Filters\SelectFilter::make('color')
                    ->label('Color')
                    ->relationship('color', 'ColorNameHE')
                    ->multiple()
                    ->searchable(),
                Tables\Filters\SelectFilter::make('hair')
                    ->label('Hair')
                    ->relationship('hair', 'HairNameHE')
                    ->multiple()
                    ->searchable(),
                // Combined filter for Father (searching by Hebrew Name, English Name or SagirID)
                Filter::make('father')
                    ->label('Father')
                    ->query(function (Builder $query, array $data) {
                        if (! empty($data['search'])) {
                            $query->whereHas('father', function (Builder $query) use ($data) {
                                $query->where('Heb_Name', 'like', "%{$data['search']}%")
                                    ->orWhere('Eng_Name', 'like', "%{$data['search']}%")
                                    ->orWhere('SagirID', 'like', "%{$data['search']}%");
                            });
                        }
                    })
                    ->form([
                        Forms\Components\TextInput::make('search')
                            ->label('Father Hebrew, English Name or Sagir'),
                    ]),
                // Combined filter for Mother (searching by Hebrew Name, English Name or SagirID)
                Filter::make('mother')
                    ->label('Mother')
                    ->query(function (Builder $query, array $data) {
                        if (! empty($data['search'])) {
                            $query->whereHas('mother', function (Builder $query) use ($data) {
                                $query->where('Heb_Name', 'like', "%{$data['search']}%")
                                    ->orWhere('Eng_Name', 'like', "%{$data['search']}%")
                                    ->orWhere('SagirID', 'like', "%{$data['search']}%");
                            });
                        }
                    })
                    ->form([
                        Forms\Components\TextInput::make('search')
                            ->label('Mother Hebrew, English Name or Sagir'),
                    ]),
                // create filters to select and search by "owners" (PrevUser many 2 many relationship) fields: full_name, phone, id - owners is a relationship, full_name is a custom accessor using: ["first_name", "last_name", "first_name_en", "last_name_en"] 
                Filter::make('owners')
                    ->form([
                        Select::make('owners')
                            ->label('Owners')
                            ->multiple()
                            ->relationship(
                                'owners',
                                'id',
                                modifyQueryUsing: fn (Builder $query) => $query
                                    ->whereNull('users.deleted_at')
                                    ->where(function (Builder $q) {
                                        $q->has('dogs');
                                    })
                                    ->orderBy('users.first_name')
                                    ->orderBy('users.last_name'),
                            )
                            ->getOptionLabelFromRecordUsing(fn (PrevUser $record): string => $record->name)
                            ->searchable(['users.first_name', 'users.last_name', 'users.first_name_en', 'users.last_name_en', 'users.id'])
                            ->searchDebounce(2000),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['owners'] ?? null,
                            fn (Builder $q, $owners) => $q->whereHas('owners', fn (Builder $q2) => $q2->whereIn('users.id', $owners)),
                        );
                    }),
                // bulean filters for: IsMagPass, IsMagPass_2, not_relevant, red_pedigree 
                Tables\Filters\TernaryFilter::make('red_pedigree')
                    ->label('Red Pedigree')
                    ->placeholder('All')
                    ->trueLabel('Yes')
                    ->falseLabel('No')
                    ->queries(
                        true: fn (Builder $query) => $query->where('red_pedigree', 1),
                        false: fn (Builder $query) => $query->whereNot('red_pedigree', 1),
                        blank: fn (Builder $query) => $query,
                    ),
                Tables\Filters\TernaryFilter::make('IsMagPass')
                    ->label('MHG Pass')
                    ->placeholder('All')
                    ->trueLabel('Yes')
                    ->falseLabel('No')
                    ->queries(
                        true: fn (Builder $query) => $query->where('IsMagPass', 1),
                        false: fn (Builder $query) => $query->where('IsMagPass', 0),
                        blank: fn (Builder $query) => $query,
                    ),
                Tables\Filters\TernaryFilter::make('IsMagPass_2')
                    ->label('MHG 2nd Pass')
                    ->placeholder('All')
                    ->trueLabel('Yes')
                    ->falseLabel('No')
                    ->queries(
                        true: fn (Builder $query) => $query->where('IsMagPass_2', 1),
                        false: fn (Builder $query) => $query->where('IsMagPass_2', 0),
                        blank: fn (Builder $query) => $query,
                    ),
                // Date filters for RegDate, BirthDate, OwnershipDate
                Filter::make('RegDate')
                    ->label('Regiestration')
                    ->form([
                        DatePicker::make('RegDate')
                            ->placeholder('Regiestration Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['RegDate'],
                            fn (Builder $query, $date): Builder => $query->whereDate('RegDate', '>=', $date)
                        );
                    }),
                Filter::make('BirthDate')
                    ->label('Birth Date')
                    ->form([
                        DatePicker::make('BirthDate')
                            ->placeholder('Birth Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['BirthDate'],
                            fn (Builder $query, $date): Builder => $query->whereDate('BirthDate', '>=', $date)
                        );
                    }),
                Filter::make('OwnershipDate')
                    ->label('Ownership Date')
                    ->form([
                        DatePicker::make('OwnershipDate')
                            ->placeholder('Ownership Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['OwnershipDate'],
                            fn (Builder $query, $date): Builder => $query->whereDate('OwnershipDate', '>=', $date)
                        );
                    }),
            
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(5)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->paginated([10, 25, 50, 100, 200, 250, 300])
            ->defaultPaginationPageOption(25)
            ->defaultSort('SagirID', 'desc')
            ->searchOnBlur()
            ->striped()
            ->deferLoading()
            // ->recordUrl(fn (PrevDog $record): string => route('filament.admin.resources.prev-dogs.view', $record), shouldOpenInNewTab: false,);
            ->recordUrl(false);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Record')->tabs([
                    /***** 1. Overview *****/
                    Tab::make('Overview')->schema([
                        InfolistGrid::make(2)->schema([
                            TextEntry::make('sagir_prefix')->label('Prefix'),
                            TextEntry::make('SagirID')->label('Sagir ID'),
                            TextEntry::make('full_name')
                                ->label('Full Name')
                                ->columnSpanFull(),
                            TextEntry::make('RegDate')
                                ->label('Registration Date')
                                ->columnSpanFull(),
                            TextEntry::make('BirthDate')
                                ->label('Birth Date')
                                ->columnSpanFull(),
                        ]),
                        InfolistGrid::make(3)->schema([
                            TextEntry::make('breed.BreedName')->label('Breed'),
                            TextEntry::make('color.ColorNameHE')->label('Color'),
                            TextEntry::make('hair.HairNameHE')->label('Hair'),
                        ]),
                        InfolistGrid::make(2)->schema([
                            TextEntry::make('Sex')->label('Sex'),
                            TextEntry::make('gender')->label('Gender'),
                        ]),
                    ]),

                    /***** 2. Ownership & Breeding *****/
                    Tab::make('Ownership & Breeding')->schema([
                        InfolistSection::make('Ownership')->schema([
                            TextEntry::make('owners')
                                ->label('Owners')
                                ->state(fn(PrevDog $record) => $record->owners->pluck('full_name')->implode(', ')),
                            TextEntry::make('currentOwner.full_name')
                                ->label('Current Owner'),
                            TextEntry::make('OwnershipDate')
                                ->label('Ownership Date'),
                        ]),
                        InfolistSection::make('Breeding')->schema([
                            TextEntry::make('Breeder_Name')->label('Breeder'),
                            TextEntry::make('Foreign_Breeder_name')->label('Foreign Breeder'),
                            TextEntry::make('breedingManager.full_name')->label('Breeding Manager'),
                        ]),
                    ]),

                    /***** 3. Pedigree & Titles *****/
                    Tab::make('Pedigree & Titles')->schema([
                        InfolistGrid::make(2)->schema([
                            TextEntry::make('father.full_name')->label('Father'),
                            TextEntry::make('mother.full_name')->label('Mother'),
                        ]),
                        InfolistSection::make('Pedigree')->schema([
                            TextEntry::make('pedigree_color')->label('Pedigree Color'),
                            IconEntry::make('red_pedigree')->label('Red Pedigree'),
                            TextEntry::make('PedigreeNotes')
                                ->label('Pedigree Notes')
                                ->columnSpanFull(),
                        ]),
                        InfolistSection::make('Titles & Shows')->schema([
                            TextEntry::make('TitleName')->label('Titles (pre‑2010)'),
                            TextEntry::make('ShowsCount')->label('Shows Count'),
                            TextEntry::make('GidulShowType')->label('Gidul Show'),
                        ]),
                    ]),

                    /***** 4. Metrics & Performance *****/
                    Tab::make('Metrics & Performance')->schema([
                        InfolistGrid::make(2)->schema([
                            IconEntry::make('IsMagPass')->label('MHG Pass'),
                            IconEntry::make('IsMagPass_2')->label('MHG 2nd Pass'),
                        ]),
                        InfolistGrid::make(3)->schema([
                            TextEntry::make('SupplementarySign')->label('Supplementary Sign'),
                            TextEntry::make('SizeID')->label('Size ID'),
                            TextEntry::make('GroupID')->label('Group ID'),
                        ]),
                    ]),

                    /***** 5. Health & Notes *****/
                    Tab::make('Health & Notes')->schema([
                        TextEntry::make('HealthNotes')
                            ->label('Health Notes')
                            ->columnSpanFull(),
                        InfolistGrid::make(2)->schema([
                            TextEntry::make('Pelvis'),
                            TextEntry::make('SCH'),
                        ]),
                        TextEntry::make('Notes_2')
                            ->label('Additional Notes')
                            ->columnSpanFull(),
                        TextEntry::make('message_test')->label('Message Test'),
                    ]),

                    /***** 6. Media & Flags *****/
                    Tab::make('Media & Flags')->schema([
                        InfolistGrid::make(2)->schema([
                            ImageEntry::make('ProfileImage')->label('Profile Image'),
                            ImageEntry::make('Image2')->label('Image 2'),
                        ]),
                        InfolistGrid::make(2)->schema([
                            IconEntry::make('not_relevant')->label('Not Relevant'),
                            IconEntry::make('encoding')->label('Encoding Issue'),
                        ]),
                    ]),

                    /***** 7. Metadata *****/
                    Tab::make('Metadata')->schema([
                        InfolistGrid::make(2)->schema([
                            TextEntry::make('id')->label('ID'),
                            TextEntry::make('BeitGidulID')->label('Beit Gidul ID'),
                            TextEntry::make('CreationDateTime')->label('Created On'),
                            TextEntry::make('ModificationDateTime')->label('Modified On'),
                            TextEntry::make('created_at')->label('Created At'),
                            TextEntry::make('updated_at')->label('Updated At'),
                            TextEntry::make('deleted_at')->label('Deleted At'),
                        ]),
                    ]),
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
            'view' => Pages\ViewPrevDog::route('/{record}'),
            'edit' => Pages\EditPrevDog::route('/{record}/edit'),
        ];
    }
}
