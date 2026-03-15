<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GetActivityTypes extends Command
{
    protected $signature = 'activity:types
        {table : Legacy table name}
        {column : Legacy column name}
        {--connection=mysql_prev : Database connection to read from}
        {--limit=100 : Maximum number of grouped rows to return}
    ';

    protected $description = 'Get distinct values for a legacy table column with counts';

    public function handle(): int
    {
        $connection = (string)$this->option('connection');
        $table = (string)$this->argument('table');
        $column = (string)$this->argument('column');
        $limit = max((int)$this->option('limit'), 1);

        $configuredDatabase = (string)(config("database.connections.{$connection}.database") ?? '');

        if ($configuredDatabase === '') {
            $this->error("Connection [{$connection}] is not configured.");

            return self::FAILURE;
        }

        if ((string)config("database.connections.{$connection}.driver") === 'mysql' && $configuredDatabase !== 'rbzmwnvqjq') {
            $this->error("Connection [{$connection}] must point to schema [rbzmwnvqjq].");

            return self::FAILURE;
        }

        if (!Schema::connection($connection)->hasTable($table)) {
            $this->error("Table [{$table}] does not exist on connection [{$connection}].");

            return self::FAILURE;
        }

        if (!Schema::connection($connection)->hasColumn($table, $column)) {
            $this->error("Column [{$column}] does not exist on table [{$table}].");

            return self::FAILURE;
        }

        $results = DB::connection($connection)
            ->table($table)
            ->select($column, DB::raw('COUNT(*) as count'))
            ->groupBy($column)
            ->orderByDesc('count')
            ->limit($limit)
            ->get();

        if ($results->isEmpty()) {
            $this->warn('No rows found.');

            return self::SUCCESS;
        }

        $this->table(
            [$column, 'count'],
            $results->map(fn(object $row): array => [
                $row->{$column},
                $row->count,
            ])->all()
        );

        return self::SUCCESS;
    }
}
