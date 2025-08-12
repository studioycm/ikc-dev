<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevClubResource\Pages;
use App\Filament\Resources\PrevClubResource\RelationManagers\BreedsRelationManager;
use App\Livewire\Prev\PrevClub\PrevClubBreedsTable;
use App\Models\PrevClub;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Livewire as LivewireEntry;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PrevClubResource extends Resource
{
    protected static ?string $model = PrevClub::class;

    protected static ?string $slug = 'prev-clubs';

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?int $navigationSort = 70;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::$model::count();
    }

    public static function getModelLabel(): string
    {
        return __('Club');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Clubs');
    }

    public static function getNavigationGroup(): string
    {
        return __('Clubs Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Clubs');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('DataID')
                    ->label(__('Previous ID'))
                    ->numeric(),
                DatePicker::make('ModificationDateTime')
                    ->label(__('Modified On')),
                DatePicker::make('CreationDateTime')
                    ->label(__('Created On')),
                TextInput::make('ClubCode')
                    ->label(__('Club Code'))
                    ->numeric(),
                TextInput::make('Name')
                    ->label(__('Name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('Address')
                    ->label(__('Address'))
                    ->maxLength(255),
                TextInput::make('Street')
                    ->label(__('Street'))
                    ->maxLength(255),
                TextInput::make('Number')
                    ->label(__('Number'))
                    ->maxLength(50),
                TextInput::make('Email')
                    ->label(__('Email'))
                    ->email()
                    ->maxLength(255),
                TextInput::make('RegistrationPrice')
                    ->label(__('Registration Price'))
                    ->numeric(),
                TextInput::make('GeneralReviewFee')
                    ->label(__('General Review Fee'))
                    ->numeric(),
                TextInput::make('DogReviewFee')
                    ->label(__('Dog Review Fee'))
                    ->numeric(),
                TextInput::make('Breed_NonReg_Price')
                    ->label(__('Breed NonReg Price'))
                    ->numeric(),
                TextInput::make('PerDog_NonReg_Price')
                    ->label(__('Per Dog NonReg Price'))
                    ->numeric(),
                TextInput::make('TestPrice')
                    ->label(__('Test Price'))
                    ->numeric(),
                TextInput::make('Logo')
                    ->label(__('Logo'))
                    ->maxLength(500),
                TextInput::make('ManagerName')
                    ->label(__('Manager Name'))
                    ->maxLength(255),
                TextInput::make('ManagerEmail')
                    ->label(__('Manager Email'))
                    ->email()
                    ->maxLength(255),
                TextInput::make('ManagerMobile')
                    ->label(__('Manager Mobile'))
                    ->tel()
                    ->maxLength(50),
                TextInput::make('SpecialKey')
                    ->label(__('Special Key'))
                    ->maxLength(4000),
                TextInput::make('ManagerID')
                    ->label(__('Manager ID'))
                    ->numeric(),
                TextInput::make('EngName')
                    ->label(__('English Name'))
                    ->maxLength(255),
                TextInput::make('status')
                    ->label(__('Status'))
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {
                $connection = new PrevClub()->getConnectionName();

                $dogsCountSub = DB::connection($connection)
                    ->table('breed_club as bc')
                    ->selectRaw('COUNT(d.SagirID)')
                    ->join('BreedsDB as b', 'bc.breed_id', '=', 'b.id')
                    ->leftJoin('DogsDB as d', 'd.RaceID', '=', 'b.BreedCode')
                    ->whereColumn('bc.club_id', 'clubs.id')
                    ->whereNull('bc.deleted_at')
                    ->whereNull('d.deleted_at');

                return $query
                    ->withCount(['breeds'])
                    ->selectRaw('clubs.*')
                    ->selectSub($dogsCountSub, 'dogs_count')
                    ->with(['managers']);
            })
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(),
                TextColumn::make('Name')
                    ->label(__('Name'))
                    ->description(fn (PrevClub $record): string => $record->EngName ?? '')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
//                TextColumn::make('ClubCode')
//                    ->label(__('Club Code'))
//                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
//                    ->sortable()
//                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('managers.name')
                    ->label(__('Manager'))
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->tooltip(function (PrevClub $record): string {
                        return $record->managers->map(function ($manager) {
                            $initials = collect(explode(' ', trim($manager->full_name_heb ?: $manager->full_name_eng)))
                                ->filter()
                                ->map(fn($n) => mb_substr($n, 0, 1))
                                ->join('.');

                            $contact = $manager->mobile_phone
                                ?? $manager->phone
                                ?? $manager->email
                                ?? '';

                            return $initials . ': ' . $contact;
                        })->join(', ');
                    })
                    ->toggleable(),
                TextColumn::make('Email')
                    ->label(__('Email'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('full_address')
                    ->label(__('Address'))
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('breeds_count')
                    ->label(__('Breeds'))
                    ->counts('breeds')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(['breeds_count'])
                    ->toggleable(),
                TextColumn::make('dogs_count')
                    ->label(__('Dogs Count'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(['dogs_count'])
                    ->toggleable(),
                TextColumn::make('RegistrationPrice')
                    ->label(__('Registration Price'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('GeneralReviewFee')
                    ->label(__('General Review Fee'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('DogReviewFee')
                    ->label(__('Dog Review Fee'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('TestPrice')
                    ->label(__('Test Price'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ModificationDateTime')
                    ->label(__('Modified On'))
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('CreationDateTime')
                    ->label(__('Created On'))
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('status')
                    ->label(__('Status'))
                    ->icons([
                        'heroicon-o-x-circle' => fn($state): bool => $state === 'not for use' || empty($state),
                        'heroicon-o-check-circle' => fn($state): bool => $state === 'for use',
                    ])
                    ->colors([
                        'danger' => fn($state): bool => $state === 'not for use',
                        'success' => fn($state): bool => $state === 'for use',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('has_breeds')
                    ->label(__('Has breeds'))
                    ->query(fn (Builder $q): Builder => $q->has('breeds')),
                Filter::make('has_managers')
                    ->label(__('Has managers'))
                    ->query(fn (Builder $q): Builder => $q->has('managers')),
            ])
            ->actions([
                ViewAction::make()->label(__('View')),
                EditAction::make()->label(__('Edit')),
                DeleteAction::make()->label(__('Delete')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label(__('Delete Selected')),
                ]),
            ])
            ->recordUrl(false);
    }

    /**
     * Build the view Infolist for a given Club record.
     */
    public static function getInfolistForRecord(PrevClub $record): Infolist
    {
        $record->loadMissing('managers');
        return Infolist::make()
            ->record($record)
            ->schema([
                Tabs::make('ClubTabs')->tabs([
                    Tab::make(__('Main'))->schema([
                        InfolistSection::make(__('General'))
                            ->schema([
                                TextEntry::make('Name')
                                    ->label(__('Name')),
                                TextEntry::make('EngName')
                                    ->label(__('English Name')),
                                TextEntry::make('id')
                                    ->label(__('Club ID'))
                                    ->state(fn (PrevClub $record): string => (string) ($record->ClubCode ?? '')),
                                TextEntry::make('total_dogs')
                                    ->label(__('Club Dogs Total'))
                                    ->state(fn (PrevClub $record): string => (string) $record->totalDogsCount()),
                                TextEntry::make('managers.full_name')
                                    ->label(__('Manager'))
                                    ->state(function (PrevClub $record): string {
                                        $rel = $record->getRelationValue('managers');
                                        if (!$rel || !isset($rel[0])) return __('n/a');

                                        $manager = $rel[0];
                                        $name = (string)($manager->full_name ?? '');
                                        $phone = (string)($manager->mobile_phone ?? '');
                                        return trim($name . ($phone ? " ($phone)" : '')) ?: __('n/a');
                                    }),
                                TextEntry::make('Email')
                                    ->label(__('Email'))
                                    ->state(fn (PrevClub $record): string => (string) ($record->Email ?? '')),
                                TextEntry::make('full_address')
                                    ->label(__('Address'))
                                    ->state(fn (PrevClub $record): string => (string) ($record->full_address ?? '')),
                                TextEntry::make('created')
                                    ->label(__('Created On'))
                                    ->state(fn (PrevClub $record): string => (string) ($record->CreationDateTime ?? '')),
                                TextEntry::make('modified')
                                    ->label(__('Modified On'))
                                    ->state(fn (PrevClub $record): string => (string) ($record->ModificationDateTime ?? '')),
                            ]),
                        InfolistSection::make(__('Prices'))
                            ->schema([
                                TextEntry::make('registration_price')
                                    ->label(__('Registration Price'))
                                    ->state(fn (PrevClub $record): string => (string) ($record->RegistrationPrice ?? '')),
                                TextEntry::make('general_review_fee')
                                    ->label(__('General Review Fee'))
                                    ->state(fn (PrevClub $record): string => (string) ($record->GeneralReviewFee ?? '')),
                                TextEntry::make('dog_review_fee')
                                    ->label(__('Dog Review Fee'))
                                    ->state(fn (PrevClub $record): string => (string) ($record->DogReviewFee ?? '')),
                                TextEntry::make('breed_nonreg_price')
                                    ->label(__('Breed NonReg Price'))
                                    ->state(fn (PrevClub $record): string => (string) ($record->Breed_NonReg_Price ?? '')),
                                TextEntry::make('perdog_nonreg_price')
                                    ->label(__('Per Dog NonReg Price'))
                                    ->state(fn (PrevClub $record): string => (string) ($record->PerDog_NonReg_Price ?? '')),
                                TextEntry::make('test_price')
                                    ->label(__('Test Price'))
                                    ->state(fn (PrevClub $record): string => (string) ($record->TestPrice ?? '')),
                            ]),
                    ]),
                    Tab::make(__('Breeds'))->schema([
                        InfolistSection::make(__('Breeds in Club'))
                            ->schema([
                                LivewireEntry::make(PrevClubBreedsTable::class)
                                    ->key('prev-club-breeds')
                                    ->data(fn (PrevClub $record): array => [
                                        'clubId' => (int) $record->id,
                                    ])
                                    ->columnSpanFull(),
                            ]),
                    ]),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            BreedsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPrevClubs::route('/'),
            'create' => Pages\CreatePrevClub::route('/create'),
            'edit'   => Pages\EditPrevClub::route('/{record}/edit'),
            'view'   => Pages\ViewPrevClub::route('/{record}'),
        ];
    }
}
