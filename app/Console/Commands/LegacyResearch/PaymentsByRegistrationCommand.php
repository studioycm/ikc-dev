<?php

namespace App\Console\Commands\LegacyResearch;

use App\Models\PrevShow;
use Illuminate\Support\Facades\DB;

class PaymentsByRegistrationCommand extends BaseLegacyResearchCommand
{
    protected $signature = 'legacy:payments-by-registration {--show= : The Show ID}';

    protected $description = 'Generate payments grouped by registration for a given show';

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

        $this->info('Building payments-by-registration for show ID ' . $showId . '...');

        $rows = DB::connection('mysql_prev')
            ->table('shows_payments_info as p')
            ->join('shows_registration as sr', 'sr.id', '=', 'p.RegistrationID')
            ->where('sr.ShowID', $showId)
            ->whereNull('p.deleted_at')
            ->groupBy('sr.ShowID', 'p.RegistrationID')
            ->orderBy('p.RegistrationID')
            ->select([
                'sr.ShowID as show_id',
                'p.RegistrationID as registration_id',
                DB::raw('COUNT(*) as payments_count'),
                DB::raw('COALESCE(SUM(p.PaymentAmount), 0) as total_amount'),
            ])->get();

        $csvPath = $this->writeCsv('payments_by_registration.csv', [
            'show_id', 'registration_id', 'payments_count', 'total_amount',
        ], $rows->map(fn($r) => [
            (int)$r->show_id,
            (int)$r->registration_id,
            (int)$r->payments_count,
            (int)$r->total_amount,
        ]));

        $md = "# Payments by Registration\n\n" .
            "Show: {$show->TitleName} (ID {$showId})\n\n" .
            'Generated at: ' . now()->toDateTimeString() . "\n\n" .
            "See payments_by_registration.csv for full data.\n";

        $this->writeMd('payments_by_registration.md', $md);

        $this->info('Report generated:');
        $this->line(' - ' . $csvPath);
        $this->line(' - ' . $this->reportsPath() . DIRECTORY_SEPARATOR . 'payments_by_registration.md');

        return self::SUCCESS;
    }
}
