<?php

namespace App\Console\Commands\LegacyResearch;

use App\Models\PrevBreed;
use App\Models\PrevDog;
use App\Models\PrevJudge;
use App\Models\PrevShow;
use App\Models\PrevShowArena;
use App\Models\PrevShowBreed;
use App\Models\PrevShowClass;
use App\Models\PrevShowDog;
use App\Models\PrevShowPayment;
use App\Models\PrevShowRegistration;
use App\Models\PrevShowResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OverviewCommand extends BaseLegacyResearchCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'legacy:overview';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate overview counts for legacy tables (mysql_prev)';

    public function handle(): int
    {
        $this->info('Generating legacy data overview...');

        $entities = [
            ['table' => 'ShowsDB', 'model' => PrevShow::class],
            ['table' => 'Shows_Dogs_DB', 'model' => PrevShowDog::class],
            ['table' => 'Shows_Structure', 'model' => PrevShowArena::class],
            ['table' => 'Shows_Breeds', 'model' => PrevShowBreed::class],
            ['table' => 'Shows_Classes', 'model' => PrevShowClass::class],
            ['table' => 'shows_results', 'model' => PrevShowResult::class],
            ['table' => 'JudgesDB', 'model' => PrevJudge::class],
            ['table' => 'BreedsDB', 'model' => PrevBreed::class],
            ['table' => 'DogsDB', 'model' => PrevDog::class],
            ['table' => 'shows_registration', 'model' => PrevShowRegistration::class],
            ['table' => 'shows_payments_info', 'model' => PrevShowPayment::class],
        ];

        $rows = [];

        foreach ($entities as $e) {
            $table = $e['table'];
            $model = $e['model'];

            $total = DB::connection('mysql_prev')->table($table)->count();
            $hasDeletedAt = Schema::connection('mysql_prev')->hasColumn($table, 'deleted_at');

            $deleted = null;
            $active = $total;

            if ($hasDeletedAt) {
                $deleted = DB::connection('mysql_prev')->table($table)->whereNotNull('deleted_at')->count();
                $active = $total - $deleted;
            }

            $rows[] = [
                $table,
                class_basename($model),
                $total,
                $active,
                $deleted,
            ];
        }

        // Write CSV
        $csvPath = $this->writeCsv('overview.csv', ['table', 'model', 'total', 'active', 'deleted'], $rows);

        // Write / Append Markdown summary
        $summary = "# Legacy Data Overview\n\n" .
            'Generated at: ' . now()->toDateTimeString() . "\n\n" .
            "Rows by table (see overview.csv for details).\n";

        $this->writeMd('overview.md', $summary);

        $this->info('Overview generated:');
        $this->line(' - ' . $csvPath);
        $this->line(' - ' . $this->reportsPath() . DIRECTORY_SEPARATOR . 'overview.md');

        return self::SUCCESS;
    }
}
