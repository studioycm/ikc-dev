<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class DeploymentSelfCheck extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:deploy:health';

    /**
     * The console command description.
     */
    protected $description = 'Run environment checks (DB, cache, Redis, Horizon, storage link, app URL).';

    public function handle(): int
    {
        $status = 0;

        $this->info('=== Deployment Health Check ===');
        $this->line('App: ' . (string) Config::get('app.name'));
        $this->line('Env: ' . (string) Config::get('app.env'));
        $this->line('URL: ' . (string) Config::get('app.url'));

        // Database check
        try {
            $default = (string) Config::get('database.default');
            $result = DB::select('SELECT 1 AS one');
            $ok = isset($result[0]) && (int) (($result[0]->one ?? 0)) === 1;
            $this->line("DB [{$default}]: " . ($ok ? 'OK' : 'Unexpected response'));
            if (! $ok) {
                $status = 1;
            }
        } catch (\Throwable $e) {
            $status = 1;
            $this->error('DB error: ' . $e->getMessage());
        }

        // Cache check
        try {
            $store = (string) Config::get('cache.default');
            $key = 'deploy_health_check';
            Cache::put($key, 'ok', now()->addMinute());
            $ok = Cache::get($key) === 'ok';
            $this->line("Cache [{$store}]: " . ($ok ? 'OK' : 'Failed to read/write'));
            if (! $ok) {
                $status = 1;
            }
        } catch (\Throwable $e) {
            $status = 1;
            $this->error('Cache error: ' . $e->getMessage());
        }

        // Redis check (if configured/available)
        try {
            $client = (string) Config::get('database.redis.client');
            $pong = Redis::connection()->ping();
            $ok = is_string($pong) ? (stripos($pong, 'PONG') !== false) : (bool) $pong;
            $this->line("Redis [{$client}]: " . ($ok ? 'OK (PING)' : 'No PONG'));
            if (! $ok) {
                $status = 1;
            }
        } catch (\Throwable $e) {
            $this->warn('Redis check skipped/failed: ' . $e->getMessage());
        }

        // Storage symlink check
        $storageLink = public_path('storage');
        if (is_link($storageLink)) {
            $this->line('Storage symlink: OK');
        } else {
            $this->warn('Storage symlink missing. Run: php artisan storage:link');
        }

        // Horizon status (if installed)
        if (class_exists(\Laravel\Horizon\Horizon::class)) {
            try {
                $statusStr = \Laravel\Horizon\Horizon::status();
                $this->line('Horizon: ' . $statusStr);
            } catch (\Throwable $e) {
                $this->warn('Horizon status error: ' . $e->getMessage());
            }
        } else {
            $this->line('Horizon: not installed');
        }

        $this->line('===============================');
        return $status;
    }
}
