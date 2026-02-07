<?php

namespace App\Filament\Resources;

use App\Enums\Legacy\LegacyDogGender;
use App\Filament\Resources\PrevBreedingResource\Pages;
use App\Models\PrevBreeding;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrevBreedingResource extends Resource
{
    protected static ?string $model = PrevBreeding::class;

    protected static ?string $slug = 'prev-breedings';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return __('Breeding');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Breedings');
    }

    public static function getNavigationGroup(): string
    {
        return __('Breedings Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Breedings');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('SagirId')
                    ->label(__('Female'))
                    ->searchable(['SagirID', 'Heb_Name', 'Eng_Name', 'Chip', 'ImportNumber'])
                    ->relationship('female', 'SagirID', modifyQueryUsing: fn(Builder $query) => $query->where('GenderID', '=', LegacyDogGender::Female->value), ignoreRecord: true)
                    ->optionsLimit(20)
                    ->searchDebounce(1500)
                    ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->SagirID} - {$record->full_name}"),

                Select::make('MaleSagirId')
                    ->label(__('Male'))
                    ->searchable(['SagirID', 'Heb_Name', 'Eng_Name', 'Chip', 'ImportNumber'])
                    ->relationship('male', 'SagirID', modifyQueryUsing: fn(Builder $query) => $query->where('GenderID', '=', LegacyDogGender::Male->value), ignoreRecord: true)
                    ->optionsLimit(20)
                    ->searchDebounce(1500)
                    ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->SagirID} - {$record->full_name}"),
                DatePicker::make('BreddingDate'),

                Toggle::make('Rules_IsOwner')
                    ->label(__('Rules Is Owner')),

                Toggle::make('BreedMismatch')
                    ->label(__('Breed Mismatch')),

                Toggle::make('Male_More_Than_5')
                    ->label(__('Male more than 5')),

                Toggle::make('Male_More_Than_2')
                    ->label(__('Male more than 2')),

                Toggle::make('Male_DNA')
                    ->label(__('Male DNA')),

                Toggle::make('Female_DNA')
                    ->label(__('Female DNA')),

                Toggle::make('Male_Breeding_Not_Approved')
                    ->label(__('Male Breeding Not Approved')),

                Toggle::make('Female_Breeding_Not_Approved')
                    ->label(__('Female Breeding Not Approved')),

                Toggle::make('Foreign_Male_Records')
                    ->label(__('Foreign Male Records')),

                TextInput::make('female_rate'),

                TextInput::make('male_rebreed'),

                TextInput::make('male_rebreed_5'),

                TextInput::make('male_rebreed_2')
                    ->required(),

                TextInput::make('generations_note'),

                TextInput::make('live_male_puppie')
                    ->integer(),

                TextInput::make('live_female_puppie')
                    ->integer(),

                TextInput::make('dead_male_puppie')
                    ->integer(),

                TextInput::make('dead_female_puppie')
                    ->integer(),

                TextInput::make('total_dead')
                    ->integer(),

                Select::make('review_type')
                    ->options([
                        'breeding_promoter' => __('Breed promoter'),
                        'breeding_group' => __('Breeding group'),
                    ])
                    ->nullable(),

                Toggle::make('publish_data'),

                Toggle::make('share_data'),

                DatePicker::make('birthing_date'),

                ToggleButtons::make('filled_step')
                    ->options([
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                    ])
                    ->grouped(),

                ToggleButtons::make('payment_type')
                    ->options([
                        'phone_payment' => __('Phone Payment'),
                        'credit_card' => __('Credit Card'),
                        'cash' => __('Cash'),
                        null => __('Missing'),
                    ])
                    ->grouped()
                    ->nullable(),

                ToggleButtons::make('payment_status')
                    ->options([
                        'paid' => __('Paid'),
                        'waiting for payment' => __('Waiting for payment'),
                        null => __('Missing'),
                    ])
                    ->grouped()
                    ->nullable(),

                TextInput::make('price_per_dog')
                    ->numeric(),

                TextInput::make('review_price')
                    ->numeric(),

                TextInput::make('certificate_price')
                    ->numeric(),

                TextInput::make('total_payment')
                    ->numeric(),

                TextInput::make('total_refund')
                    ->numeric(),

                ToggleButtons::make('less_than_8_years')
                    ->options([
                        'yes' => __('Yes'),
                        'no' => __('No'),
                    ])
                    ->grouped(),

                ToggleButtons::make('more_than_18_months')
                    ->options([
                        'yes' => __('Yes'),
                        'no' => __('No'),
                    ])
                    ->grouped(),

                ToggleButtons::make('status')
                    ->options([
                        '0' => '0',
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                    ])
                    ->grouped(),

                TextInput::make('responsiable_owner')
                    ->integer(),

                Select::make('created_by')
                    ->relationship('createdBy', 'first_name')
                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en']),

                Select::make('breeding_house_id')
                    ->relationship('breedinghouse', 'HebName')
                    ->searchable(['HebName', 'EngName', 'GidulCode']),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?PrevBreeding $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?PrevBreeding $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Relationships (Using 'female' and 'male' relations from Breeding model)
                TextColumn::make('female.Eng_Name')
                    ->label('Mother (Dam)')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('male.Eng_Name')
                    ->label('Father (Sire)')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('BreddingDate')
                    ->label('Breeding Date')
                    ->date()
                    ->sortable(),

                // Booleans - Rules & Checks
                IconColumn::make('Rules_IsOwner')
                    ->label('Is Owner')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('BreedMismatch')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('Male_More_Than_5')
                    ->label('Male > 5 Litters')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('Male_More_Than_2')
                    ->label('Male > 2 Litters')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('Male_DNA')
                    ->label('Male DNA')
                    ->boolean(),

                IconColumn::make('Female_DNA')
                    ->label('Female DNA')
                    ->boolean(),

                IconColumn::make('Male_Breeding_Not_Approved')
                    ->label('Sire Not Approved')
                    ->boolean()
                    ->color('danger')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('Female_Breeding_Not_Approved')
                    ->label('Dam Not Approved')
                    ->boolean()
                    ->color('danger')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('Foreign_Male_Records')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Statistics
                TextColumn::make('female_rate')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('male_rebreed')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('generations_note')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                // Puppies Count
                TextColumn::make('live_male_puppie')
                    ->label('Live M')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('live_female_puppie')
                    ->label('Live F')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('dead_male_puppie')
                    ->label('Dead M')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('dead_female_puppie')
                    ->label('Dead F')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_dead')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Status & Dates
                TextColumn::make('review_type')
                    ->badge()
                    ->color('info'),

                TextColumn::make('birthing_date')
                    ->label('Whelping Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('filled_step')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'approved', 'completed' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),

                // Financials (Assuming ILS based on 'Asia/Jerusalem' timezone in context)
                TextColumn::make('payment_type')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('payment_status')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('price_per_dog')
                    ->money('ILS')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_payment')
                    ->money('ILS')
                    ->sortable(),

                TextColumn::make('total_refund')
                    ->money('ILS')
                    ->toggleable(isToggledHiddenByDefault: true),

                // Age Validation
                IconColumn::make('less_than_8_years')
                    ->label('< 8 Years')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('more_than_18_months')
                    ->label('> 18 Months')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Data Privacy
                IconColumn::make('publish_data')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('share_data')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('responsiable_owner')
                    ->label('Responsible Owner')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('createdBy.name')
                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en'])
                    ->sortable(['last_name', 'first_name']),

                TextColumn::make('breedinghouse.name')
                    ->searchable(['HebName', 'EngName', 'GidulCode'])
                    ->sortable(['HebName']),
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
            'index' => Pages\ListPrevBreedings::route('/'),
            'create' => Pages\CreatePrevBreeding::route('/create'),
            'edit' => Pages\EditPrevBreeding::route('/{record}/edit'),
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
