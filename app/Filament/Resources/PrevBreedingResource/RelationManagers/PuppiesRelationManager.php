<?php

namespace App\Filament\Resources\PrevBreedingResource\RelationManagers;

use App\Filament\Exports\PrevBreedingRelatedDogExporter;
use App\Filament\Imports\PrevBreedingRelatedDogImporter;
use App\Filament\Resources\PrevDogResource;
use App\Models\PrevBreedingRelatedDog;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PuppiesRelationManager extends RelationManager
{
    protected static string $relationship = 'puppies';

    protected static ?string $recordTitleAttribute = 'temparory_name';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Puppies');
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(),
                TextColumn::make('temparory_name')
                    ->label(__('Puppy Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('dog.SagirID')
                    ->label(__('Registered Dog'))
                    ->description(fn(PrevBreedingRelatedDog $record): ?string => $record->dog?->full_name)
                    ->toggleable(),
                TextColumn::make('chip_number')
                    ->label(__('Chip Number'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('colorName.ColorName')
                    ->label(__('Color'))
                    ->toggleable(),
                TextColumn::make('gender')
                    ->label(__('Gender'))
                    ->badge()
                    ->toggleable(),
                IconColumn::make('is_submit')
                    ->label(__('Submitted'))
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->headerActions([
                ImportAction::make()
                    ->label(__('Import Puppies'))
                    ->icon('fas-file-import')
                    ->color('gray')
                    ->iconPosition('after')
                    ->importer(PrevBreedingRelatedDogImporter::class)
                    ->options(['breedingId' => $this->getOwnerRecord()->getKey()]),
                ExportAction::make()
                    ->label(__('Export Puppies'))
                    ->icon('fas-file-export')
                    ->color('primary')
                    ->iconPosition('after')
                    ->exporter(PrevBreedingRelatedDogExporter::class),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('View Dog'))
                    ->visible(fn(PrevBreedingRelatedDog $record): bool => $record->dog !== null)
                    ->url(fn(PrevBreedingRelatedDog $record): string => PrevDogResource::getUrl('edit', ['record' => $record->dog]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                ExportBulkAction::make()
                    ->label(__('Export Selected'))
                    ->icon('fas-file-export')
                    ->color('primary')
                    ->iconPosition('after')
                    ->exporter(PrevBreedingRelatedDogExporter::class),
            ]);
    }
}
