<?php

namespace App\Filament\Resources;

use App\Filament\Exports\PrevVetAuthExporter;
use App\Filament\Imports\PrevVetAuthImporter;
use App\Filament\Resources\PrevVetAuthResource\Pages;
use App\Models\PrevVetAuth;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PrevVetAuthResource extends Resource
{
    protected static ?string $model = PrevVetAuth::class;

    protected static ?string $slug = 'prev-vet-auths';

    protected static ?string $navigationIcon = 'fas-clinic-medical';

    protected static ?int $navigationSort = 10;

    public static function getModelLabel(): string
    {
        return __('Veterinarian Authority');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Veterinarian Authorities');
    }

    public static function getNavigationGroup(): string
    {
        return __('Legacy Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Veterinarian Authorities');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),

                TextInput::make('vet_email')
                    ->email()
                    ->required(),

                Placeholder::make('created_at')
                    ->label(__('Created Date'))
                    ->content(fn(?PrevVetAuth $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label(__('Last Modified Date'))
                    ->content(fn(?PrevVetAuth $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('vet_email')
                    ->searchable()
                    ->label(__('Email'))
                    ->sortable()
                    ->copyable()
                    ->copyMessage(__('filament::components/copyable.messages.copied'))
                    ->copyMessageDuration(1500)
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make()
                    ->label(__('Export Selected'))
                    ->icon('fas-file-export')
                    ->color('primary')
                    ->iconPosition('after')
                    ->exporter(PrevVetAuthExporter::class),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                ImportAction::make()
                    ->label(__('Import'))
                    ->icon('fas-file-import')
                    ->color('gray')
                    ->iconPosition('after')
                    ->importer(PrevVetAuthImporter::class),
                ExportAction::make()
                    ->label(__('Export All'))
                    ->icon('fas-file-export')
                    ->color('primary')
                    ->iconPosition('after')
                    ->exporter(PrevVetAuthExporter::class),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrevVetAuths::route('/'),
            'create' => Pages\CreatePrevVetAuth::route('/create'),
            'edit' => Pages\EditPrevVetAuth::route('/{record}/edit'),
        ];
    }
}
