<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use SplFileObject;

class ExportDynamicHtmlElementValues extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'dynamic-html:export {--element=} {--output=} {--connection=mysql} {--schema=}';

    /**
     * The console command description.
     */
    protected $description = 'Export dynamic_html_element_value to docs CSV (Options List, Value, Label Hebrew, Label English).';

    public function handle(): int
    {
        $elementId = $this->option('element');
        $output = $this->option('output');

        $defaultOutput = base_path('docs\\dynamic_html_element_values.csv');
        $outputPath = $output ? base_path($output) : $defaultOutput;

        // Build the query using provided connection and optional schema qualification.
        $connection = $this->option('connection') ?? config('database.default');
        $schema = $this->option('schema');
        $prefix = $schema ? ($schema . '.') : '';

        $db = DB::connection($connection);

        $query = $db->table(DB::raw($prefix . 'dynamic_html_element_value as v'))
            ->join(DB::raw($prefix . 'dynamic_html_element as e'), 'e.id', '=', 'v.html_element_id')
            ->select([
                DB::raw('e.id as html_element_id'),
                DB::raw('e.name as html_element_name'),
                DB::raw('v.option_value as option_value'),
                DB::raw('v.option_text_hebrew as option_text_hebrew'),
                DB::raw('v.option_text_english as option_text_english'),
            ]);

        if (!empty($elementId)) {
            $query->where('v.html_element_id', (int)$elementId);
        }

        $rows = $query->orderBy('e.id')->orderBy('v.option_value')->get();

        // Ensure directory exists
        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true) && !is_dir($dir)) {
                $this->error("Failed to create directory: {$dir}");

                return self::FAILURE;
            }
        }

        // Write CSV with UTF-8 BOM for Excel Hebrew support
        $csv = new SplFileObject($outputPath, 'w');
        $csv->fwrite("\xEF\xBB\xBF");

        // Header
        $header = ['Options List', 'Value', 'Label Hebrew', 'Label English'];
        $csv->fputcsv($header);

        $count = 0;

        foreach ($rows as $row) {
            $optionsList = $row->html_element_id . ' > ' . (string)$row->html_element_name;

            $csv->fputcsv([
                $optionsList,
                (string)$row->option_value,
                (string)($row->option_text_hebrew ?? ''),
                (string)($row->option_text_english ?? ''),
            ]);
            $count++;
        }

        $this->info("Exported {$count} rows to {$outputPath}");

        if ($count === 0) {
            $this->warn('No rows were found. Consider checking the connection/user permissions or using --element to target a specific element.');
        }

        return self::SUCCESS;
    }
}
