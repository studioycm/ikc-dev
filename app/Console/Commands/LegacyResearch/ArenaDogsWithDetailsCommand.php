<?php

namespace App\Console\Commands\LegacyResearch;

use App\Models\PrevShow;
use App\Models\PrevShowDog;

class ArenaDogsWithDetailsCommand extends BaseLegacyResearchCommand
{
    /** @var string */
    protected $signature = 'legacy:arena-dogs {--show= : The Show ID} {--arena= : Optional Arena ID to filter}';

    /** @var string */
    protected $description = 'Generate report of dogs with details per arena for a show (mysql_prev)';

    public function handle(): int
    {
        $showId = (int)($this->option('show') ?? 0);
        $arenaId = $this->option('arena');
        $arenaId = is_null($arenaId) ? null : (int)$arenaId;

        if ($showId <= 0) {
            $this->error('Please provide a valid --show=<ID>');

            return self::INVALID;
        }

        $show = PrevShow::on('mysql_prev')->find($showId);
        if (!$show) {
            $this->error('Show not found on mysql_prev: ID ' . $showId);

            return self::INVALID;
        }

        $this->info('Building arena dogs with details for show ID ' . $showId . '...');

        $query = PrevShowDog::query()
            ->on('mysql_prev')
            ->where('ShowID', $showId)
            ->with(['dog', 'breed', 'showClass'])
            ->orderBy('ArenaID')
            ->orderBy('ClassID');

        if (!is_null($arenaId) && $arenaId > 0) {
            $query->where('ArenaID', $arenaId);
        }

        $dogs = $query->get(['ShowID', 'ArenaID', 'SagirID', 'BreedID', 'ClassID']);

        $rows = $dogs->map(function (PrevShowDog $d): array {
            return [
                (int)$d->ShowID,
                (int)$d->ArenaID,
                (int)$d->SagirID,
                (string)($d->dog->Heb_Name ?? ''),
                (string)($d->dog->Eng_Name ?? ''),
                (int)($d->breed->BreedCode ?? $d->BreedID ?? 0),
                (string)($d->breed->BreedName ?? ''),
                (int)$d->ClassID,
                (int)($d->OrderID ?? 0),
            ];
        });

        $csvPath = $this->writeCsv('arena_dogs_with_details.csv', [
            'show_id', 'arena_id', 'sagir_id', 'dog_name_he', 'dog_name_en', 'breed_code', 'breed_name_he', 'class_id', 'order_id',
        ], $rows);

        $title = is_null($arenaId) ? 'all arenas' : ('arena ' . $arenaId);
        $md = "# Arena Dogs with Details\n\n" .
            "Show: {$show->TitleName} (ID {$showId}), {$title}\n\n" .
            'Generated at: ' . now()->toDateTimeString() . "\n\n" .
            "See arena_dogs_with_details.csv for the full dataset.\n";

        $this->writeMd('arena_dogs_with_details.md', $md);

        $this->info('Report generated:');
        $this->line(' - ' . $csvPath);
        $this->line(' - ' . $this->reportsPath() . DIRECTORY_SEPARATOR . 'arena_dogs_with_details.md');

        return self::SUCCESS;
    }
}
