<?php

namespace App\Console\Commands\LegacyResearch;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Base class for legacy data research commands.
 * - Provides helpers to write CSV/MD files into docs/legacy-data-research
 * - Ensures outputs are generated without mutating any database state.
 */
abstract class BaseLegacyResearchCommand extends Command
{
    protected function reportsPath(): string
    {
        $path = base_path('docs' . DIRECTORY_SEPARATOR . 'legacy-data-research');

        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        return $path;
    }

    /**
     * Write a CSV file into the reports folder.
     *
     * @param string $basename Filename like 'overview.csv'
     * @param array<int,string> $headers
     * @param iterable<array<int,scalar|null>> $rows
     * @return string Full path written
     */
    protected function writeCsv(string $basename, array $headers, iterable $rows): string
    {
        $path = $this->reportsPath() . DIRECTORY_SEPARATOR . $basename;

        $fh = fopen($path, 'w');
        if ($fh === false) {
            throw new \RuntimeException('Failed to open CSV for writing: ' . $path);
        }

        fputcsv($fh, $headers);
        foreach ($rows as $row) {
            fputcsv($fh, $row);
        }

        fclose($fh);

        return $path;
    }

    /**
     * Overwrite a Markdown file content in the reports folder.
     */
    protected function writeMd(string $basename, string $content): string
    {
        $path = $this->reportsPath() . DIRECTORY_SEPARATOR . $basename;
        File::put($path, $content);

        return $path;
    }

    /**
     * Append to a Markdown file in the reports folder (creates file if missing).
     */
    protected function appendMd(string $basename, string $content): string
    {
        $path = $this->reportsPath() . DIRECTORY_SEPARATOR . $basename;
        File::append($path, $content);

        return $path;
    }
}
