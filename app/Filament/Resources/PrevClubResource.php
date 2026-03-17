<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevClubResource\Pages;
use App\Filament\Resources\PrevClubResource\RelationManagers\BreedsRelationManager;
use App\Filament\Resources\PrevClubResource\RelationManagers\ManagersRelationManager;
use App\Filament\Resources\PrevClubResource\RelationManagers\MembersRelationManager;
use App\Filament\Resources\PrevClubResource\RelationManagers\PaymentsRelationManager;
use App\Filament\Resources\PrevClubResource\RelationManagers\PromotersRelationManager;
use App\Filament\Resources\PrevClubResource\RelationManagers\UserRequestsRelationManager;
use App\Livewire\Prev\PrevClub\PrevClubBreedsTable;
use App\Models\PrevClub;
use App\Models\PrevUser;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs as FormTabs;
use Filament\Forms\Components\Tabs\Tab as FormTab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions\Action as InfolistAction;
use Filament\Infolists\Components\Grid as InfolistGrid;
use Filament\Infolists\Components\Livewire as LivewireEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;
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

    //    public static function getNavigationBadge(): ?string
    //    {
    //        return (string) static::$model::count();
    //    }

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
                FormTabs::make('club_form_tabs')
                    ->tabs([
                        FormTab::make(__('General'))
                            ->schema([
                                Section::make(__('General details'))
                                    ->schema([
                                        TextInput::make('DataID')
                                            ->label(__('Previous ID'))
                                            ->numeric(),
                                        TextInput::make('ClubCode')
                                            ->label(__('Club Code'))
                                            ->numeric(),
                                        TextInput::make('Name')
                                            ->label(__('Name'))
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('EngName')
                                            ->label(__('English Name'))
                                            ->maxLength(255),
                                        TextInput::make('status')
                                            ->label(__('Status'))
                                            ->numeric(),
                                        TextInput::make('Logo')
                                            ->label(__('Logo'))
                                            ->maxLength(500),
                                    ])
                                    ->columns(3),
                                Section::make(__('Contact'))
                                    ->schema([
                                        TextInput::make('Email')
                                            ->label(__('Email Address'))
                                            ->email() // Enforces email format validation
                                            ->required()
                                            ->maxLength(255)
                                            ->autocomplete('email') // Helps browsers autofill correctly
                                            ->placeholder('user@example.com')
                                            ->prefixIcon('heroicon-m-envelope') // Adds a visual indicator inside the input
                                            ->suffixIcon('heroicon-m-check-circle') // Optional: Visual confirm icon (static or dynamic)
                                            ->suffixIconColor('success') // Color for the suffix icon
                                            ->unique(ignoreRecord: true)
                                            ->helperText('We will never share your email with anyone else.'),
                                        TextInput::make('Address')
                                            ->label(__('Address'))
                                            ->maxLength(255),
                                        TextInput::make('Street')
                                            ->label(__('Street'))
                                            ->maxLength(255),
                                        TextInput::make('Number')
                                            ->label(__('Number'))
                                            ->maxLength(50),
                                    ])
                                    ->columns(2),
                            ]),
                        FormTab::make(__('Pricing'))
                            ->schema([
                                Section::make(__('Club prices'))
                                    ->schema([
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
                                    ])
                                    ->columns(3),
                            ]),
                        FormTab::make(__('Management'))
                            ->schema([
                                Section::make(__('Manager details'))
                                    ->schema([
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
                                        TextInput::make('ManagerID')
                                            ->label(__('Manager ID'))
                                            ->numeric(),
                                        TextInput::make('SpecialKey')
                                            ->label(__('Special Key'))
                                            ->maxLength(4000)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(3),
                                Section::make(__('Timeline'))
                                    ->schema([
                                        DatePicker::make('CreationDateTime')
                                            ->label(__('Created On')),
                                        DatePicker::make('ModificationDateTime')
                                            ->label(__('Modified On')),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpanFull(),
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
                    ->with([
                        'managers.skills' => fn($skillsQuery) => $skillsQuery->whereIn('skills.id', PrevClub::CLUB_STAFF_SKILL_IDS),
                        'breeds.promoters.skills' => fn($skillsQuery) => $skillsQuery->where('skills.id', PrevClub::PROMOTER_SKILL_ID),
                    ]);
            })
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(),
                TextColumn::make('Name')
                    ->label(__('Name'))
                    ->description(fn (PrevClub $record): string => $record->EngName ?? '')
                    ->wrap()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('managers.name')
                    ->label(__('Manager'))
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->tooltip(function (PrevClub $record): string {
                        return $record->managers->map(function ($manager) {
                            $initials = collect(explode(' ', trim($manager->full_name_heb ?: $manager->full_name_eng)))
                                ->filter()
                                ->map(fn ($n) => mb_substr($n, 0, 1))
                                ->join('.');

                            $contact = $manager->normalised_phone
                                ?? $manager->email
                                ?? '';

                            return $initials.': '.$contact;
                        })->join(', ');
                    })
                    ->toggleable(),
                TextColumn::make('manager_titles')
                    ->label(__('Manager Titles'))
                    ->state(fn(PrevClub $record): array => $record->managerTitleSummary())
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->toggleable(),
                TextColumn::make('promoters')
                    ->label(__('Promoters'))
                    ->state(fn(PrevClub $record): array => $record->promoterSummary())
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->toggleable(),
                TextColumn::make('Email')
                    ->label(__('Email'))
                    ->icon('fas-envelope') // Adds an icon next to the text
                    ->iconColor('primary') // Colors the icon
                    ->copyable() // Allows one-click copying to clipboard
                    ->copyMessage(__('filament::components/copyable.messages.copied'))
                    ->copyMessageDuration(1500)
                    ->searchable(isIndividual: true, isGlobal: false) // Enables searching by email
                    ->sortable()
                    ->weight('medium')
                    ->color(fn(?string $state) => filled($state) ? 'primary' : 'gray')
                    ->toggleable(isToggledHiddenByDefault: false),
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
                        'heroicon-o-x-circle' => fn ($state): bool => $state === 'not for use' || empty($state),
                        'heroicon-o-check-circle' => fn ($state): bool => $state === 'for use',
                    ])
                    ->colors([
                        'danger' => fn ($state): bool => $state === 'not for use',
                        'success' => fn ($state): bool => $state === 'for use',
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
                Filter::make('has_promoters')
                    ->label(__('Has promoters'))
                    ->query(fn(Builder $q): Builder => $q->whereHas('breeds.promoters')),
            ])
            ->actions([
                Action::make('contacts')
                    ->icon('heroicon-o-user-group')
                    ->label('')
                    ->iconButton()
                    ->tooltip(__('Club contacts'))
                    ->infolist([
                        InfolistSection::make(__('Club contact'))
                            ->schema([
                                TextEntry::make('Email')
                                    ->label(__('Club Email'))
                                    ->state(fn(PrevClub $record): ?string => $record->Email)
                                    ->copyable()
                                    ->copyMessage(__('filament::components/copyable.messages.copied'))
                                    ->copyMessageDuration(1500),
                                TextEntry::make('full_address')
                                    ->label(__('Club Address'))
                                    ->state(fn(PrevClub $record): string => $record->full_address)
                                    ->copyable()
                                    ->copyMessage(__('filament::components/copyable.messages.copied'))
                                    ->copyMessageDuration(1500),
                            ])
                            ->columns(2),
                        InfolistSection::make(__('Related users'))
                            ->schema([
                                RepeatableEntry::make('contact_directory')
                                    ->state(fn(PrevClub $record): array => $record->contactDirectoryRows()->all())
                                    ->schema([
                                        TextEntry::make('role_type')
                                            ->label(__('Relation'))
                                            ->badge(),
                                        TextEntry::make('name')
                                            ->label(__('Name')),
                                        TextEntry::make('titles')
                                            ->label(__('Titles')),
                                        TextEntry::make('breeds')
                                            ->label(__('Breeds')),
                                        TextEntry::make('email')
                                            ->label(__('Email'))
                                            ->copyable()
                                            ->copyMessage(__('filament::components/copyable.messages.copied'))
                                            ->copyMessageDuration(1500),
                                        TextEntry::make('mobile_phone')
                                            ->label(__('Mobile Phone'))
                                            ->copyable()
                                            ->copyMessage(__('filament::components/copyable.messages.copied'))
                                            ->copyMessageDuration(1500),
                                    ])
                                    ->columns(2)
                                    ->grid(2),
                            ]),
                    ])
                    ->modalHeading(fn(PrevClub $record): string => __('Club contacts: :club', ['club' => $record->Name]))
                    ->modalSubmitAction(false),
                Action::make('emails')
                    ->icon('heroicon-o-envelope')
                    ->label('')
                    ->iconButton()
                    ->tooltip(__('Club emails'))
                    ->infolist([
                        InfolistSection::make(__('Emails'))
                            ->schema([
                                TextEntry::make('Email')
                                    ->label(__('Club Email'))
                                    ->state(fn(PrevClub $record): ?string => $record->Email)
                                    ->copyable()
                                    ->copyMessage(__('filament::components/copyable.messages.copied'))
                                    ->copyMessageDuration(1500),
                                RepeatableEntry::make('email_directory')
                                    ->state(fn(PrevClub $record): array => $record->emailDirectoryRows()->all())
                                    ->schema([
                                        TextEntry::make('role_type')
                                            ->label(__('Relation'))
                                            ->badge(),
                                        TextEntry::make('name')
                                            ->label(__('Name')),
                                        TextEntry::make('titles')
                                            ->label(__('Titles')),
                                        TextEntry::make('email')
                                            ->label(__('Email'))
                                            ->copyable()
                                            ->copyMessage(__('filament::components/copyable.messages.copied'))
                                            ->copyMessageDuration(1500),
                                    ])
                                    ->columns(2)
                                    ->grid(2),
                            ]),
                    ])
                    ->modalHeading(fn(PrevClub $record): string => __('Club emails: :club', ['club' => $record->Name]))
                    ->modalSubmitAction(false),
                ViewAction::make()->label(__('View')),
                EditAction::make()->label(__('Edit')),
                //                DeleteAction::make()->label(__('Delete')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    //                    DeleteBulkAction::make()->label(__('Delete Selected')),
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
                    Tab::make('General')->schema([
                        // Use a grid layout to organise the main/general and pricing sections as columns
                        InfolistGrid::make()
                            ->columns(5)
                            ->schema([
                                TextEntry::make('Name')
                                    ->label(__('Name')),
                                TextEntry::make('EngName')
                                    ->label(__('English Name')),
                                TextEntry::make('id')
                                    ->label(__('Club ID'))
                                    ->state(fn (PrevClub $record): string => (string) ($record->id ?? '')),
                                TextEntry::make('total_dogs')
                                    ->label(__('Club Dogs Total'))
                                    ->state(fn (PrevClub $record): int => (int) $record->totalDogsCount()),
                                TextEntry::make('Email')
                                    ->label(__('Email'))
                                    ->icon('fas-copy')
                                    ->iconColor('primary')
                                    ->copyable() // Essential for copying the email quickly
                                    ->copyMessage(__('Email address copied to clipboard'))
                                    ->weight(FontWeight::Bold)
                                    ->color('gray')
                                    ->placeholder(__('No email provided')) // Better than an empty string
                                    ->suffixAction(
                                        InfolistAction::make('send_email')
                                            ->icon('fas-paper-plane')
                                            ->color('success')
                                            ->url(fn(TextEntry $component) => "mailto:{$component->getState()}")
                                            ->visible(fn(TextEntry $component) => filled($component->getState()))
                                    ),
                                TextEntry::make('full_address')
                                    ->label(__('Address'))
                                    ->state(fn (PrevClub $record): string => (string) ($record->full_address ?? '')),
                                // Managers as a repeatable block (like titles in PrevDogResource)
                                RepeatableEntry::make('managers')
                                    ->label(fn (PrevClub $record): string => __('Managers').' ('.($record->managers?->count() ?? 0).')')
                                    ->schema([
                                        TextEntry::make('full_name')
                                            ->label('')
                                            ->hiddenLabel()
                                            ->size(TextEntry\TextEntrySize::Large)
                                            ->weight(FontWeight::Bold)
                                            ->color(Color::Blue)
                                            ->columnSpan(1)
                                            ->formatStateUsing(fn ($state, ?PrevUser $manager = null) => $manager?->full_name ?? $state),
                                        TextEntry::make('normalised_phone')
                                            ->label('')
                                            ->hiddenLabel()
                                            ->size(TextEntry\TextEntrySize::Medium)
                                            ->color('success')
                                            ->columnSpan(1)
                                            ->formatStateUsing(fn ($state, ?PrevUser $manager = null) => $manager?->normalised_phone ?? $state),
                                        TextEntry::make('email')
                                            ->label('')
                                            ->hiddenLabel()
                                            ->columnSpan(1)
                                            ->formatStateUsing(fn ($state, ?PrevUser $manager = null) => $manager?->email ?? $state),
                                    ])
                                    ->columns(1)
                                    ->grid(4)
                                    ->columnSpan(5),
                            ]),

                        // Prices section placed below the grid, organised as 3 columns
                        InfolistSection::make(__('Prices'))
                            ->schema([
                                InfolistGrid::make()
                                    ->columns(3)
                                    ->schema([
                                        TextEntry::make('registration_price')
                                            ->label(__('Registration Price'))
                                            ->state(fn (PrevClub $record): string => (string) ($record->RegistrationPrice ?? ''))
                                            ->columnSpan(1),
                                        TextEntry::make('general_review_fee')
                                            ->label(__('General Review Fee'))
                                            ->state(fn (PrevClub $record): string => (string) ($record->GeneralReviewFee ?? ''))
                                            ->columnSpan(1),
                                        TextEntry::make('dog_review_fee')
                                            ->label(__('Dog Review Fee'))
                                            ->state(fn (PrevClub $record): string => (string) ($record->DogReviewFee ?? ''))
                                            ->columnSpan(1),

                                        TextEntry::make('breed_nonreg_price')
                                            ->label(__('Breed NonReg Price'))
                                            ->state(fn (PrevClub $record): string => (string) ($record->Breed_NonReg_Price ?? ''))
                                            ->columnSpan(1),
                                        TextEntry::make('perdog_nonreg_price')
                                            ->label(__('Per Dog NonReg Price'))
                                            ->state(fn (PrevClub $record): string => (string) ($record->PerDog_NonReg_Price ?? ''))
                                            ->columnSpan(1),
                                        TextEntry::make('test_price')
                                            ->label(__('Test Price'))
                                            ->state(fn (PrevClub $record): string => (string) ($record->TestPrice ?? ''))
                                            ->columnSpan(1),
                                    ]),
                            ]),
                    ]),

                    // Breeds tab unchanged (keeps Livewire entry)
                    Tab::make('Breeds')->schema([
                        InfolistSection::make(__('Breeds in Club'))
                            ->schema([
                                LivewireEntry::make(PrevClubBreedsTable::class)
                                    ->key('prev-club-breeds')
//                                    ->data(fn (PrevClub $record): array => [
//                                        'clubId' => (int) $record->id,
//                                    ])
                                    ->columnSpanFull(),
                            ]),
                    ]),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            MembersRelationManager::class,
            PaymentsRelationManager::class,
            UserRequestsRelationManager::class,
            ManagersRelationManager::class,
            PromotersRelationManager::class,
            BreedsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrevClubs::route('/'),
            'create' => Pages\CreatePrevClub::route('/create'),
            'edit' => Pages\EditPrevClub::route('/{record}/edit'),
            'view' => Pages\ViewPrevClub::route('/{record}'),
        ];
    }
}
