<?php

namespace App\Filament\Resources;

use App\Models\PrevDog;
use App\Models\PrevHair;
use App\Models\PrevUser;
use App\Models\PrevBreed;
use App\Models\PrevColor;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
//use Filament\Tables\Filters\QueryBuilder;
//use Filament\Tables\Filters\SelectFilter;
//use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
//use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
//use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
//use Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint;
//use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
//use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Support\Colors\Color;
//use Filament\Support\Facades\FilamentColor;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\HtmlString;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PrevDogResource\Pages;
// use App\Filament\Resources\PrevDogResource\RelationManagers;
// infolist class
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
// use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Grid as InfolistGrid;
use Filament\Infolists\Components\Section as InfolistSection;
//use Filament\Infolists\Components\Split as InfolistSplit;

// use App\Filament\Exports\DogExporter;
// use App\Filament\Imports\DogImporter;
// use Filament\Tables\Actions\ExportAction;
// use Filament\Tables\Actions\ImportAction;

class PrevDogResource extends Resource
{
    protected static ?string $model = PrevDog::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'fas-paw';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::$model::count();
    }

    public static function getModelLabel(): string
    {
        return __('Dog');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Dogs');
    }

    public static function getNavigationGroup(): string
    {
        return __('Dogs Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Dogs');
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
                ->with('breed')
                ->with('color')
                ->with('hair')
                ->with('father')
                ->with('mother')
                ->with('owners')
                ->with('titles')
               ->with('duplicates');
//                ->with('currentOwner')
//                ->withCount('duplicates')
            })
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('id'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
               Tables\Columns\TextColumn::make('duplicates_count')
                   ->label(__('Duplicates Count'))
                   ->numeric()
                   ->counts('duplicates')
                   ->sortable(['duplicates_count'])
                   ->toggleable(isToggledHiddenByDefault: true),
               Tables\Columns\TextColumn::make('duplicates')
                   ->label(__('Other Duplicate IDs'))
                   ->formatStateUsing(function (PrevDog $record): HtmlString {
                       // format the related duplicates so each of the duplicates array items will be a link to the route of PrevDogResource view page using the id as the parameter
                       $duplicatesLinks = $record->duplicates?->pluck('id')->map(fn ($id) =>
                           '<a href="' . route('filament.admin.resources.prev-dogs.view', ['record' => $id]) . '" target="_blank">' . $id . '</a>'
                       )->implode(', ');
                       return new HtmlString($duplicatesLinks);
                   })
                   ->wrap()
                   // ->words(5)
                   // ->getStateUsing(fn (PrevDog $record): string =>$record->duplicates->pluck('id')->implode(', '))
                   ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('prefixed_sagir')
                    ->label(__('Sagir'))
                    ->searchable(['SagirID'], isIndividual: true, isGlobal: false)
                    ->sortable(['sagir_prefix','SagirID']),
                Tables\Columns\TextColumn::make('full_name')
                    ->label(__('Full Name'))
                    ->searchable(['Heb_Name','Eng_Name'], isIndividual: true, isGlobal: false),
                Tables\Columns\TextColumn::make('breed.BreedName')
                    ->label(__('Breed'))
                    ->description(function (PrevDog $record): string {
                        return $record->breed?->BreedNameEN ?? '~';
                    }, position: 'under')
                    ->sortable(['BreedName'])
                    ->toggleable(),
                Tables\Columns\TextColumn::make('color.ColorNameHE')
                    ->label(__('Color'))
                    ->description(function (PrevDog $record): string {
                        return $record->color?->ColorNameEN ?? '~';
                    }, position: 'under')
                    ->sortable(['ColorNameHE'])
                    ->toggleable(),
                Tables\Columns\TextColumn::make('hair.HairNameHE')
                    ->label(__('Hair'))
                    ->description(function (PrevDog $record): string {
                        return $record->hair?->HairNameEN ?? '~';
                    }, position: 'under')
                    ->sortable(['HairNameHE'])
                    ->toggleable(),
                // Tables\Columns\TextColumn::make('Sex')
                //     ->badge()
                //     ->color(fn (string $state): string => match ($state) {
                //         "ז" => 'info',
                //         "נ" => 'danger',
                //         default => 'gray',
                //     })
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('gender')
                    ->label(__('Gender'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        "M" => 'blue',
                        "F" => 'pink',
                        default => 'gray',
                    })
                    ->sortable(['GenderID']),
                Tables\Columns\TextColumn::make('BirthDate')
                    ->label(__('Birth Date'))
                    ->date()
                    ->sinceTooltip()
                    ->sortable(),
                Tables\Columns\TextColumn::make('RegDate')
                    ->label(__('Registration Date'))
                    ->date()
                    ->sinceTooltip()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('titles.name')
                    ->label(__('Titles'))
                    ->listWithLineBreaks()
                    ->limitList(1)
                    ->tooltip(fn (Tables\Columns\TextColumn $column): ?string =>
                        (($state = $column->getState()) === null) ? null :
                        (is_array($state)
                            ? (count($state) > $column->getListLimit() ? implode(' | ', $state) : null)
                            : (string) $state
                        )
                    )
                    ->searchable(['dogs_titles_db.TitleName'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('owners.full_name')
                    ->label(__('Owners'))
                    ->description(function (PrevDog $record): string {
                        // Get the first two owners' names
                        return $record->owners?->pluck('id')->implode(', ');
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('father.full_name')
                    ->label(__('Father'))
                    ->description(function (PrevDog $record): string {
                        return $record->father?->SagirID ?? 'n/a';
                    }, position: 'under')
                    ->searchable(['Eng_Name','Heb_Name','SagirID'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('mother.full_name')
                    ->label(__('Mother'))
                    ->description(function (PrevDog $record): string {
                        return $record->mother?->SagirID ?? 'n/a';
                    }, position: 'under')
                    ->searchable(['Eng_Name','Heb_Name','SagirID'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('Chip')
                    ->label(__('Chip'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('DnaID')
                    ->label(__('DNA ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('ImportNumber')
                    ->label(__('Import Number'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('Chip_2')
                    ->label(__('Chip 2'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('BeitGidulID')
                    ->label(__('Beit Gidul ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('BeitGidulName')
                    ->label(__('Beit Gidul'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('GrowerId')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
//                Tables\Columns\TextColumn::make('CurrentOwnerId')
//                    ->label(__('Owner (deprecated)'))
//                    ->wrapHeader()
//                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
//                    ->getStateUsing(function (PrevDog $record): string {
//                        if ($record->CurrentOwnerId === null) {
//                            return 'n/a';
//                        }
//                        $ownerName = $record->currentOwner?->first_name ?? '<unknown>';
//                        $ownershipDate = $record->OwnershipDate
//                            ? \Carbon\Carbon::parse($record->OwnershipDate)->format('d-m-Y')
//                            : 'n/a';
//                        $ownershipDetails = $ownerName . ' | ' . $ownershipDate;
//                        return $ownershipDetails;
//                    })
//                    ->description(fn (PrevDog $record): string => $record->CurrentOwnerId ? (string)((int)$record->CurrentOwnerId) : 'n/a')
//                    ->sortable(['CurrentOwnerId'])
//                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('Breeder_Name')
                    ->label(__('Breeder Name - deprecated'))
                    ->wrapHeader()
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('Foreign_Breeder_name')
                    ->label(__('Foreign Breeder'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('Breeding_ManagerID')
                    ->label(__('Breeding Manager ID - check'))
                    ->wrapHeader()
                    ->description(fn (PrevDog $record): string => $record->breedingManager->full_name ?? 'n/a')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('Status')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('BreedID')
                    ->label(__('Breed ID - depracted'))
                    ->wrapHeader()
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('SizeID')
                    ->label(__('Size ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('SupplementarySign')
                    ->label(__('Supplementary Sign'))
                    ->wrapHeader()
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ShowsCount')
                    ->label(__('Shows Count - check'))
                    ->wrapHeader()
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('Pelvis')
                    ->label(__('Pelvis'))
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
                    ->label(__('SCH'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('RemarkCode')
                    ->label(__('Remark Code'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('TitleName')
                    ->label(__('Titles (pre ~2010)'))
                    ->wrapHeader()
                    ->separator()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('GroupID')
                    ->label(__('Group ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('GidulShowType')
                    ->label(__('Gidul Show'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pedigree_color')
                    ->label(__('Pedigree Color'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('red_pedigree')
                    ->label(__('Red Pedigree'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('PedigreeNotes')
                    ->label(__('Pedigree Notes'))
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
                    ->label(__('Pedigree Notes 2'))
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
                    ->label(__('Health Notes'))
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
                    ->label(__('Notes 2'))
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
                    ->label(__('Message Test'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sheger_id')
                    ->label(__('Sheger ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // combine the Mag columns into one column, pass will be the value/state and the rest in a description
                Tables\Columns\TextColumn::make('IsMagPass')
                    ->label(__('Mag'))
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
                    ->label(__('Mag 2'))
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
                    ->label(__('Is Correct'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ProfileImage')
                    ->label(__('Profile Image'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('Image2')
                    ->label(__('Profile Image 2'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('not_relevant')
                    ->label(__('Not Relevant'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('encoding')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('ModificationDateTime')
                    ->label(__('Modification Date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('CreationDateTime')
                    ->label(__('Creation Date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                            ->label(__('Trashed'))
                            ->options([
                                'not_deleted' => 'Not Deleted',
                                'deleted'     => 'Deleted',
                                'all'         => 'All',
                            ])
                            ->colors([
                                'not_deleted' => 'success',
                                'deleted'     => 'danger',
                                'all'         => 'gray',
                            ])
                            ->default('not_deleted')
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

                Filter::make('gender')
                    ->form([
                        Forms\Components\ToggleButtons::make('gender')
                            ->label(__('Gender'))
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
                Filter::make('sagir_prefix')
                    ->form([
                        Forms\Components\ToggleButtons::make('sagir_prefix')
                            ->label(__('Sagir Prefix'))
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
                Tables\Filters\SelectFilter::make('breed')
                    ->label(__('Breed'))
                    ->relationship('breed', 'BreedName')
                    ->multiple()
                    ->searchable(['BreedName', 'BreedNameEN'])
                    ->getOptionLabelFromRecordUsing(fn (PrevBreed $record): string => $record->BreedName . ' | ' . $record->BreedNameEN),
                Tables\Filters\SelectFilter::make('color')
                    ->label(__('Color'))
                    ->relationship('color', 'ColorNameHE')
                    ->multiple()
                    ->searchable(['ColorNameHE', 'ColorNameEN'])
                    ->getOptionLabelFromRecordUsing(fn (PrevColor $record): string => $record->ColorNameHE . ' | ' . $record->ColorNameEN),
                Tables\Filters\SelectFilter::make('hair')
                    ->label(__('Hair'))
                    ->relationship('hair', 'HairNameHE')
                    ->multiple()
                    ->searchable(['HairNameHE', 'HairNameEN'])
                    ->getOptionLabelFromRecordUsing(fn (PrevHair $record): string => $record->HairNameHE . ' | ' . $record->HairNameEN),
                // Combined filter for Father (searching by Hebrew Name, English Name or SagirID)
                Filter::make('father')
                    ->query(function (Builder $query, array $data) {
                        if (! empty($data['father_search'])) {
                            $query->whereHas('father', function (Builder $query) use ($data) {
                                $query->where('Heb_Name', 'like', "%{$data['father_search']}%")
                                    ->orWhere('Eng_Name', 'like', "%{$data['father_search']}%")
                                    ->orWhere('SagirID', 'like', "%{$data['father_search']}%");
                            });
                        }
                    })
                    ->form([
                        Forms\Components\TextInput::make('father_search')
                            ->label(__('Father'))
                            ->hint('Name \ Sagir')
                            ->helperText('Search by Hebrew\English Name or Sagir'),
                    ]),
                // Combined filter for Mother (searching by Hebrew Name, English Name or SagirID)
                Filter::make('mother')
                    ->label(__('Mother'))
                    ->query(function (Builder $query, array $data) {
                        if (! empty($data['mother_search'])) {
                            $query->whereHas('mother', function (Builder $query) use ($data) {
                                $query->where('Heb_Name', 'like', "%{$data['mother_search']}%")
                                    ->orWhere('Eng_Name', 'like', "%{$data['mother_search']}%")
                                    ->orWhere('SagirID', 'like', "%{$data['mother_search']}%");
                            });
                        }
                    })
                    ->form([
                        Forms\Components\TextInput::make('mother_search')
                            ->label(__('Mother'))
                            ->hint('Name \ Sagir')
                            ->helperText('Search by Hebrew\English Name or Sagir'),
                    ]),
                // create filters to select and search by "owners" (PrevUser many 2 many relationship) fields: full_name, phone, id - owners is a relationship, full_name is a custom accessor using: ["first_name", "last_name", "first_name_en", "last_name_en"]
                Filter::make('owners')
                    ->form([
                        Select::make('owners')
                            ->label(__('Owners'))
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
                // boolean filters for: IsMagPass, IsMagPass_2, not_relevant, red_pedigree
                // Tables\Filters\TernaryFilter::make('red_pedigree')
                //     ->label(__('Red Pedigree'))
                //     ->placeholder('All')
                //     ->trueLabel('Yes')
                //     ->falseLabel('No')
                //     ->queries(
                //         true: fn (Builder $query) => $query->where('red_pedigree', 1),
                //         false: fn (Builder $query) => $query->whereNot('red_pedigree', 1),
                //         blank: fn (Builder $query) => $query,
                //     ),
                // Tables\Filters\TernaryFilter::make('IsMagPass')
                //     ->label(__('MHG Pass'))
                //     ->placeholder('All')
                //     ->trueLabel('Yes')
                //     ->falseLabel('No')
                //     ->queries(
                //         true: fn (Builder $query) => $query->where('IsMagPass', 1),
                //         false: fn (Builder $query) => $query->where('IsMagPass', 0),
                //         blank: fn (Builder $query) => $query,
                //     ),
                // Tables\Filters\TernaryFilter::make('IsMagPass_2')
                //     ->label(__('MHG 2nd Pass'))
                //     ->placeholder('All')
                //     ->trueLabel('Yes')
                //     ->falseLabel('No')
                //     ->queries(
                //         true: fn (Builder $query) => $query->where('IsMagPass_2', 1),
                //         false: fn (Builder $query) => $query->where('IsMagPass_2', 0),
                //         blank: fn (Builder $query) => $query,
                //     ),
                // Date filters for RegDate, BirthDate, OwnershipDate
                Filter::make('RegDate')
                    ->form([
                        DatePicker::make('RegDate')
                            ->label(__('Registration Date')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['RegDate'],
                            fn (Builder $query, $date): Builder => $query->whereDate('RegDate', '>=', $date)
                        );
                    }),
                Filter::make('BirthDate')
                    ->form([
                        DatePicker::make('BirthDate')
                            ->label(__('Birth Date')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['BirthDate'],
                            fn (Builder $query, $date): Builder => $query->whereDate('BirthDate', '>=', $date)
                        );
                    }),
                Filter::make('OwnershipDate')
                    ->form([
                        DatePicker::make('OwnershipDate')
                            ->label(__('Ownership Date')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['OwnershipDate'],
                            fn (Builder $query, $date): Builder => $query->whereDate('OwnershipDate', '>=', $date)
                        );
                    }),
                Tables\Filters\TernaryFilter::make('duplicates_count')
                    ->label(__('Duplicates'))
                    ->placeholder('All')
                    ->trueLabel('Duplicates')
                    ->falseLabel('No Duplicates')
                    ->queries(
                        true: fn (Builder $query) => $query->has('duplicates', '>', 1),
                        false: fn (Builder $query) => $query->has('duplicates', '<=', 1),
                        blank: fn (Builder $query) => $query,
                    ),


            ], layout: FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(3)
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
            ->recordUrl(false)
            ->recordClasses(fn (Model $record) => $record->trashed() ? 'fi-ta-row-deleted' : null);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                    Tabs::make('Dog Record')->tabs([
                        /***** 1. Overview *****/
                        Tab::make('General')->schema([
                            InfolistGrid::make(4)->schema([
                                TextEntry::make('SagirID')
                                    ->label(__('Sagir'))
                                    ->state(fn(PrevDog $record) => $record->sagir_prefix . ' - ' . $record->SagirID),
                                TextEntry::make('full_name')
                                    ->label(__('Full Name')),
                                TextEntry::make('RegDate')
                                    ->label(__('Registration Date'))
                                    ->date(),
                                TextEntry::make('BirthDate')
                                    ->label(__('Birth Date'))
                                    ->date(),
                            ]),
                            InfolistGrid::make(4)->schema([
                                TextEntry::make('gender')
                                    ->label(__('Gender'))
                                    ->state(fn(PrevDog $record): string =>
                                        $record->gender
                                        . (!empty($record->Sex)
                                            ? " ({$record->Sex})"
                                            : ''
                                        )
                                    ),
                                TextEntry::make('breed.BreedName')->label(__('Breed')),
                                TextEntry::make('color.ColorNameHE')->label(__('Color')),
                                TextEntry::make('hair.HairNameHE')->label(__('Hair')),
                            ]),
                        ])
                        ->label(__('General')),

                        /***** 2. Ownership & Breeding *****/
                        Tab::make('Ownership & Breeding')->schema([
                            InfolistSection::make('Ownership')->schema([
                                RepeatableEntry::make('owners')
                                    ->schema([
                                        TextEntry::make('full_name')->label(__('Full Name')),
                                        TextEntry::make('mobile_phone')->label(__('Phone')),
                                        TextEntry::make('email')->label(__('Email')),
                                    ])
                                    ->label(fn(PrevDog $record): string => __('Owners') . " ({$record->owners->count()})")
                                    ->grid(4),
                                TextEntry::make('currentOwner.full_name')
                                    ->label(__('Current Owner')),
                                TextEntry::make('OwnershipDate')
                                    ->label(__('Ownership Date')),
                            ])
                            ->label(__('Ownership')),
                            InfolistSection::make('Breeding')->schema([
                                TextEntry::make('Breeder_Name')->label(__('Breeder')),
                                TextEntry::make('Foreign_Breeder_name')->label(__('Foreign Breeder')),
                                TextEntry::make('breedingManager.full_name')->label(__('Breeding Manager')),
                                TextEntry::make('BeitGidulID')->label(__('Beit Gidul ID')),
                            ])
                            ->label(__('Breeding')),
                        ])
                        ->label(__('Ownership & Breeding')),

                        /***** 3. Pedigree & Titles *****/
                        Tab::make('Pedigree & Titles')->schema([
                            InfolistSection::make('Pedigree')->schema([
                                InfolistSection::make('Parants')->schema([
                                    InfolistSection::make('Father Details')->schema([
                                        TextEntry::make('father.full_name')->label(__('Father Name')),
                                        TextEntry::make('father.SagirID')->label(__('Father Sagir ID')),
                                    ])->columns(2),
                                    InfolistSection::make('Mother Details')->schema([
                                        TextEntry::make('mother.full_name')->label(__('Mother Name')),
                                        TextEntry::make('mother.SagirID')->label(__('Mother Sagir ID')),
                                    ])->columns(2),
                                ])->columns(2),
                                TextEntry::make('pedigree_color')->label(__('Pedigree Color')),
                                IconEntry::make('red_pedigree')->label(__('Red Pedigree')),
                                TextEntry::make('PedigreeNotes')
                                    ->label(__('Pedigree Notes'))
                                    ->columnSpanFull(),
                            ])
                            ->label(__('Pedigree')),
                            InfolistSection::make('Titles & Shows')->schema([
                                RepeatableEntry::make('titles')
                                    ->label(fn(PrevDog $record): string => __('Titles') . " ({$record->titles->count()})")
                                    ->schema([
                                        TextEntry::make('name')
                                            ->hiddenLabel()
                                            ->size(TextEntry\TextEntrySize::Large)
                                            ->weight(FontWeight::Bold)
                                            ->color(Color::Blue)
                                            ->columnSpan(2),
                                        TextEntry::make('awarding.EventPlace')
                                            ->hiddenLabel()
                                            //->badge()
                                            ->color('warning')
                                            ->size(TextEntry\TextEntrySize::Medium)
                                            ->columnSpan(3),
                                        TextEntry::make('awarding.EventDate')
                                            ->hiddenLabel()
                                            ->date()
                                            ->columnSpan(2),
                                        TextEntry::make('awarding.EventName')
                                            ->hiddenLabel()
                                            ->columnSpan(3),
                                    ])
                                    ->columns(5)
                                    ->grid(5),
                                TextEntry::make('ShowsCount')->label(__('Shows Count')),
                            ])
                            ->label(__('Titles & Shows')),
                            TextEntry::make('TitleName')->label(__('Titles (pre‑2010)')),
                        ])
                        ->label(__('Pedigree & Titles')),

                        /***** 4. Metrics & Performance *****/
                        Tab::make('Metrics & Performance')->schema([
                            InfolistGrid::make(2)->schema([
                                IconEntry::make('IsMagPass')->label(__('MHG Pass')),
                                IconEntry::make('IsMagPass_2')->label(__('MHG 2nd Pass')),
                            ]),
                            InfolistGrid::make(3)->schema([
                                TextEntry::make('SupplementarySign')->label(__('Supplementary Sign')),
                                TextEntry::make('SizeID')->label(__('Size ID')),
                                TextEntry::make('GroupID')->label(__('Group ID')),
                            ]),
                        ]),

                        /***** 5. Health & Notes *****/
                        Tab::make('Health & Notes')->schema([
                            TextEntry::make('HealthNotes')
                                ->label(__('Health Notes'))
                                ->columnSpanFull(),
                            InfolistGrid::make(2)->schema([
                                TextEntry::make('Pelvis')->label(__('Pelvis')),
                                TextEntry::make('SCH')->label(__('SCH')),
                            ]),
                            TextEntry::make('Notes_2')
                                ->label(__('Additional Notes'))
                                ->columnSpanFull(),
                            TextEntry::make('message_test')->label(__('Message Test')),
                        ]),

                        /***** 6. Media & Flags *****/
                        Tab::make('Media')->schema([
                            InfolistGrid::make(2)->schema([
                                ImageEntry::make('ProfileImage')->label(__('Profile Image')),
                                ImageEntry::make('Image2')->label(__('Image 2')),
                            ]),
                        ]),

                        /***** 7. Metadata *****/
                        Tab::make('Metadata')->schema([
                            InfolistGrid::make(5)->schema([
                                TextEntry::make('id')->label(__('ID')),
                                IconEntry::make('not_relevant')->label(__('Not Relevant')),
                                IconEntry::make('encoding')->label(__('Encoding Issue')),
                            ]),
                            InfolistGrid::make(5)->schema([
                                TextEntry::make('CreationDateTime')
                                    ->label(__('Created On'))
                                    ->date(),
                                TextEntry::make('ModificationDateTime')
                                    ->label(__('Modified On'))
                                    ->date(),
                                TextEntry::make('created_at')
                                    ->label(__('Created At'))
                                    ->date(),
                                TextEntry::make('updated_at')
                                    ->label(__('Updated At'))
                                    ->date(),
                                TextEntry::make('deleted_at')
                                    ->label(__('Deleted At'))
                                    ->date(),
                            ]),
                        ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),

            ]);
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
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

    public static function getWidgets(): array
    {
        return [
            PrevDogResource\Widgets\DogStats::class,
        ];
    }
}
