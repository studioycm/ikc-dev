<?php

namespace App\Console\Commands\LegacyResearch;

use App\Models\PrevShow;
use Illuminate\Support\Facades\DB;

class DogPaymentStatusCommand extends BaseLegacyResearchCommand
{
    protected $signature = 'legacy:dog-payment-status {--show= : The Show ID}';

    protected $description = 'For each dog entry, derive payment status via registration (read-only)';

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

        $this->info('Building dog payment status for show ID ' . $showId . '...');

        // Join dogs -> registrations (either ShowRegistrationID or new_show_registration_id) -> payments
        $rows = DB::connection('mysql_prev')
            ->table('Shows_Dogs_DB as sd')
            ->leftJoin('shows_registration as sr', function ($join) {
                $join->on('sd.ShowRegistrationID', '=', 'sr.id')
                    ->orOn('sd.new_show_registration_id', '=', 'sr.id');
            })
            ->leftJoin('shows_payments_info as p', function ($join) {
                $join->on('p.RegistrationID', '=', 'sr.id')
                    ->whereNull('p.deleted_at');
            })
            ->where('sd.ShowID', $showId)
            ->whereNull('sd.deleted_at')
            ->groupBy('sd.ShowID', 'sd.SagirID', 'sr.id', 'sd.ArenaID', 'sd.ClassID')
            ->orderBy('sd.ArenaID')
            ->orderBy('sd.SagirID')
            ->select([
                'sd.ShowID as show_id',
                'sd.SagirID as sagir_id',
                DB::raw('COALESCE(sr.id, 0) as registration_id'),
                DB::raw('COUNT(p.DataID) as payments_count'),
                DB::raw('COALESCE(SUM(p.PaymentAmount), 0) as total_amount'),
                'sd.ArenaID as arena_id',
                'sd.ClassID as class_id',
            ])->get();

        $csvPath = $this->writeCsv('dog_payment_status.csv', [
            'show_id', 'sagir_id', 'registration_id', 'paid', 'payments_count', 'total_amount', 'arena_id', 'class_id',
        ], $rows->map(fn($r) => [
            (int)$r->show_id,
            (int)$r->sagir_id,
            (int)$r->registration_id,
            (int)($r->payments_count > 0 ? 1 : 0),
            (int)$r->payments_count,
            (int)$r->total_amount,
            (int)($r->arena_id ?? 0),
            (int)($r->class_id ?? 0),
        ]));

        $md = "# Dog Payment Status\n\n" .
            "Show: {$show->TitleName} (ID {$showId})\n\n" .
            'Generated at: ' . now()->toDateTimeString() . "\n\n" .
            "See dog_payment_status.csv for full data.\n";

        $this->writeMd('dog_payment_status.md', $md);

        $this->info('Report generated:');
        $this->line(' - ' . $csvPath);
        $this->line(' - ' . $this->reportsPath() . DIRECTORY_SEPARATOR . 'dog_payment_status.md');

        return self::SUCCESS;
    }
}
