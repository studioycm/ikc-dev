<?php

namespace App\Console\Commands\LegacyResearch;

use App\Models\PrevShow;
use Illuminate\Support\Facades\DB;

class RegistrationsAndDogsCommand extends BaseLegacyResearchCommand
{
    protected $signature = 'legacy:registrations-dogs {--show= : The Show ID}';

    protected $description = 'Generate report: registrations with their dogs for a given show';

    public function handle(): int
    {
        $showId = (int)($this->option('show') ?? 0);
        if ($showId <= 0) {
            $this->error('Please provide a valid --show=<ID>');

            return self::INVALID;
        }

        $show = PrevShow::on('mysql_prev')->find($showId);
        if (!$show) {
            $this->error('Show not found on mysql_prev: ID ' . $showId);

            return self::INVALID;
        }

        $this->info('Building registrations-with-dogs for show ID ' . $showId . '...');

        $rowsQuery = DB::connection('mysql_prev')
            ->table('Shows_Dogs_DB as sd')
            ->join('shows_registration as sr', function ($join) {
                $join->on('sd.ShowRegistrationID', '=', 'sr.id')
                    ->orOn('sd.new_show_registration_id', '=', 'sr.id');
            })
            ->leftJoin('DogsDB as d', 'd.SagirID', '=', 'sd.SagirID')
            ->where('sd.ShowID', $showId)
            ->whereNull('sd.deleted_at')
            ->orderBy('sr.id')
            ->orderBy('sd.ArenaID')
            ->select([
                'sd.ShowID as show_id',
                'sr.id as registration_id',
                'sr.registered_by',
                'sd.SagirID as sagir_id',
                'd.Heb_Name as dog_name_he',
                'sd.ArenaID as arena_id',
                'sd.ClassID as class_id',
            ]);

        $records = $rowsQuery->get();

        $csvPath = $this->writeCsv('registrations_and_dogs.csv', [
            'show_id', 'registration_id', 'registered_by', 'sagir_id', 'dog_name_he', 'arena_id', 'class_id',
        ], $records->map(fn($r) => [
            (int)$r->show_id,
            (int)$r->registration_id,
            (int)($r->registered_by ?? 0),
            (int)$r->sagir_id,
            (string)($r->dog_name_he ?? ''),
            (int)($r->arena_id ?? 0),
            (int)($r->class_id ?? 0),
        ]));

        $md = "# Registrations and Dogs\n\n" .
            "Show: {$show->TitleName} (ID {$showId})\n\n" .
            'Generated at: ' . now()->toDateTimeString() . "\n\n" .
            "See registrations_and_dogs.csv for full data.\n";

        $this->writeMd('registrations_and_dogs.md', $md);

        $this->info('Report generated:');
        $this->line(' - ' . $csvPath);
        $this->line(' - ' . $this->reportsPath() . DIRECTORY_SEPARATOR . 'registrations_and_dogs.md');

        return self::SUCCESS;
    }
}
