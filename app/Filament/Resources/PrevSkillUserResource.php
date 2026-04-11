<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevSkillUserResource\Pages;
use App\Models\PrevSkill;
use App\Models\PrevSkillUser;
use App\Models\PrevUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrevSkillUserResource extends Resource
{
    protected static ?string $model = PrevSkillUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $recordTitleAttribute = 'id';

    public static function getModelLabel(): string
    {
        return __('User Skill');
    }

    public static function getPluralModelLabel(): string
    {
        return __('User Skills');
    }

    public static function getNavigationGroup(): string
    {
        return __('Authorisation Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('User Skills');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('User skill details'))
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label(__('User'))
                            ->searchable()
                            ->getSearchResultsUsing(fn(string $search): array => PrevUser::selectOptions($search, 50))
                            ->getOptionLabelUsing(fn($value): ?string => PrevUser::query()->find($value)?->name)
                            ->required(),
                        Forms\Components\Select::make('skill_id')
                            ->label(__('Skill'))
                            ->relationship('skill', 'skill_name')
                            ->searchable(['skill_name', 'skill_name_en'])
                            ->preload()
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn(Model $record): string => $record->skill_name_en ? $record->skill_name . ' | ' . $record->skill_name_en : $record->skill_name),
                        Forms\Components\Select::make('club_id')
                            ->label(__('Club'))
                            ->relationship('club', 'Name')
                            ->searchable(['Name', 'EngName'])
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn(Model $record): string => $record->EngName ? $record->Name . ' | ' . $record->EngName : $record->Name),
                        Forms\Components\Select::make('breed_id')
                            ->label(__('Breed'))
                            ->relationship('breed', 'BreedName')
                            ->searchable(['BreedName', 'BreedNameEN'])
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn(Model $record): string => $record->BreedNameEN ? $record->BreedName . ' | ' . $record->BreedNameEN : $record->BreedName),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->with(['user', 'skill', 'club', 'breed']);
            })
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user.name')
                    ->label(__('User'))
                    ->sortable(['last_name', 'first_name'])
                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en'], isIndividual: true, isGlobal: false),
                TextColumn::make('skill.skill_name')
                    ->label(__('Skill'))
                    ->description(fn(PrevSkillUser $record): ?string => $record->skill?->skill_name_en)
                    ->tooltip(fn(PrevSkillUser $record) => $record->skill_id)
                    ->searchable(['skills.skill_name', 'skills.id'], isIndividual: true, isGlobal: false)
                    ->sortable(),
                TextColumn::make('club.Name')
                    ->label(__('Club'))
                    ->sortable(['Name'])
                    ->searchable(['Name', 'EngName'], isIndividual: true, isGlobal: false),
                TextColumn::make('breed.BreedName')
                    ->label(__('Breed'))
                    ->sortable(['BreedName'])
                    ->searchable(['BreedName', 'BreedNameEN'], isIndividual: true, isGlobal: false),
                TextColumn::make('created_at')->label(__('Created At'))->dateTime()->sortable(),
                TextColumn::make('updated_at')->label(__('Updated At'))->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')->label(__('Deleted at'))->since()->dateTimeTooltip()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('skill')
                    ->relationship('skill', 'skill_name')
                    ->searchable(['id', 'skill_name'])
                    ->multiple()
                    ->preload(),
                Tables\Filters\Filter::make('skill_group_filter')
                    ->form([
                        Forms\Components\ToggleButtons::make('skill_group')
                            ->options([
                                'general' => __('General'),
                                'club' => __('Club'),
                                'committees' => __('Committees'),
                                'management' => __('Management'),
                                'office' => __('Office'),
                                'other' => __('Other'),
                            ])
                            ->inline(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $value = $data['skill_group'] ?? null;
                        if (!$value) {
                            return $query;
                        }

                        return $query->whereHas('skill', function (Builder $query) use ($data) {
                            $skillIds = PrevSkill::GROUPS[$data['skill_group']] ?? [];
                            $query->whereIn('id', $skillIds);
                        });
                    }),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->searchOnBlur()
            ->striped();
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make(__('User skill details'))
                    ->schema([
                        TextEntry::make('id')->label(__('ID')),
                        TextEntry::make('user.name')->label(__('User')),
                        TextEntry::make('skill.skill_name')->label(__('Skill')),
                        TextEntry::make('skill.skill_name_en')->label(__('Skill (EN)')),
                        TextEntry::make('created_at')->label(__('Created At'))->dateTime(),
                        TextEntry::make('updated_at')->label(__('Updated At'))->dateTime(),
                        TextEntry::make('deleted_at')->label(__('Deleted at'))->since()->dateTimeTooltip()->placeholder('-'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrevSkillUsers::route('/'),
            'create' => Pages\CreatePrevSkillUser::route('/create'),
            'view' => Pages\ViewPrevSkillUser::route('/{record}'),
            'edit' => Pages\EditPrevSkillUser::route('/{record}/edit'),
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
