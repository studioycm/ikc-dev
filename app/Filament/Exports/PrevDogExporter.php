<?php

namespace App\Filament\Exports;

use App\Models\PrevDog;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PrevDogExporter extends Exporter
{
    protected static ?string $model = PrevDog::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('DataID'),
            ExportColumn::make('ModificationDateTime'),
            ExportColumn::make('CreationDateTime'),
            ExportColumn::make('SagirID'),
            ExportColumn::make('Heb_Name'),
            ExportColumn::make('Eng_Name'),
            ExportColumn::make('BeitGidulID'),
            ExportColumn::make('BeitGidulName'),
            ExportColumn::make('RegDate'),
            ExportColumn::make('BirthDate'),
            ExportColumn::make('RaceID'),
            ExportColumn::make('Sex'),
            ExportColumn::make('ColorID'),
            ExportColumn::make('HairID'),
            ExportColumn::make('SupplementarySign'),
            ExportColumn::make('GrowerId'),
            ExportColumn::make('CurrentOwnerId'),
            ExportColumn::make('OwnershipDate'),
            ExportColumn::make('FatherSAGIR'),
            ExportColumn::make('MotherSAGIR'),
            ExportColumn::make('ShowsCount'),
            ExportColumn::make('Pelvis'),
            ExportColumn::make('Notes'),
            ExportColumn::make('ImportNumber'),
            ExportColumn::make('SCH'),
            ExportColumn::make('RemarkCode'),
            ExportColumn::make('GenderID'),
            ExportColumn::make('SizeID'),
            ExportColumn::make('ProfileImage'),
            ExportColumn::make('GroupID'),
            ExportColumn::make('IsMagPass'),
            ExportColumn::make('MagDate'),
            ExportColumn::make('MagJudge'),
            ExportColumn::make('MagPlace'),
            ExportColumn::make('DnaID'),
            ExportColumn::make('Chip'),
            ExportColumn::make('GidulShowType'),
            ExportColumn::make('pedigree_color'),
            ExportColumn::make('PedigreeNotes'),
            ExportColumn::make('HealthNotes'),
            ExportColumn::make('Status'),
            ExportColumn::make('Image2'),
            ExportColumn::make('TitleName'),
            ExportColumn::make('Breeder_Name'),
            ExportColumn::make('BreedID'),
            ExportColumn::make('sheger_id'),
            ExportColumn::make('sagir_prefix'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
            ExportColumn::make('encoding'),
            ExportColumn::make('is_correct'),
            ExportColumn::make('message'),
            ExportColumn::make('message_test'),
            ExportColumn::make('not_relevant'),
            ExportColumn::make('IsMagPass_2'),
            ExportColumn::make('MagDate_2'),
            ExportColumn::make('MagJudge_2'),
            ExportColumn::make('MagPlace_2'),
            ExportColumn::make('PedigreeNotes_2'),
            ExportColumn::make('Notes_2'),
            ExportColumn::make('red_pedigree'),
            ExportColumn::make('Chip_2'),
            ExportColumn::make('Foreign_Breeder_name'),
            ExportColumn::make('Breeding_ManagerID'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your prev dog export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
