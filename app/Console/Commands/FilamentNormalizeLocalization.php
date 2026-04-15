<?php

namespace App\Console\Commands;

use App\Services\Localization\FilamentLocalizationNormalizer;
use Illuminate\Console\Command;

use function Laravel\Prompts\multiselect;

class FilamentNormalizeLocalization extends Command
{
    protected $signature = 'filament:normalize-localization
        {paths?* : Files or directories relative to the project root}
        {--goal=* : Goals to run [labels, translations, missing-hebrew]}
        {--labels : Run the missing label / heading / modal heading goal}
        {--translations : Run the missing __() wrapper goal for Filament text methods}
        {--missing-hebrew : Only report translation keys / strings missing a Hebrew translation}
        {--write : Persist the proposed fixes to disk}
        {--exclude=* : Files or directories relative to the project root to skip}
        {--json : Output the full result as JSON}
    ';

    protected $description = 'Analyze and normalize Filament and Livewire Filament-component localization usage';

    public function handle(FilamentLocalizationNormalizer $normalizer): int
    {
        $result = $normalizer->run([
            'paths' => $this->argument('paths'),
            'exclude' => $this->option('exclude'),
            'goals' => $this->selectedGoals(),
            'write' => (bool)$this->option('write'),
        ]);

        if ((bool)$this->option('json')) {
            $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        }

        $summary = $result['summary'];

        $changeLabel = (bool)$this->option('write') ? 'applied' : 'proposed';

        $this->info(sprintf(
            'Scanned %d file(s); found %d issue(s), %d %s change(s), %d missing Hebrew translation(s).',
            $summary['files_scanned'],
            $summary['findings'],
            $summary['changes'],
            $changeLabel,
            $summary['missing_hebrew'],
        ));

        foreach ($result['files'] as $file) {
            $this->newLine();
            $this->line($file['path']);

            foreach ($file['findings'] as $finding) {
                $this->line('  ' . $this->formatFinding($finding));
            }

            foreach ($file['missing_hebrew'] as $missing) {
                $this->line(sprintf('  [missing-hebrew] %s', $missing['key']));
            }
        }

        if (!(bool)$this->option('write')) {
            $this->comment('Dry run only. Re-run with --write to apply the proposed fixes.');
        }

        return self::SUCCESS;
    }

    /**
     * @param array<string, mixed> $finding
     */
    private function formatFinding(array $finding): string
    {
        if (($finding['type'] ?? null) === 'missing-primary-text-method') {
            return sprintf(
                '[%s] %s make(%s) -> %s => %s',
                $finding['type'],
                $finding['component'] ?? 'component',
                $finding['key'] ?? 'key',
                $finding['method'] ?? 'method',
                $finding['text'] ?? 'text',
            );
        }

        if (($finding['type'] ?? null) === 'missing-translation-wrapper') {
            return sprintf(
                '[%s] %s => %s',
                $finding['type'],
                $finding['method'] ?? 'method',
                $finding['text'] ?? 'text',
            );
        }

        return sprintf(
            '[%s] %s%s',
            $finding['type'] ?? 'finding',
            $finding['method'] ?? $finding['component'] ?? $finding['key'] ?? 'item',
            isset($finding['text']) ? ' => ' . $finding['text'] : '',
        );
    }

    /**
     * @return array<int, string>
     */
    private function selectedGoals(): array
    {
        $goals = collect((array)$this->option('goal'))
            ->filter()
            ->values();

        if ((bool)$this->option('labels')) {
            $goals->push('labels');
        }

        if ((bool)$this->option('translations')) {
            $goals->push('translations');
        }

        if ((bool)$this->option('missing-hebrew')) {
            $goals->push('missing-hebrew');
        }

        if ($goals->isNotEmpty()) {
            return $goals->unique()->values()->all();
        }

        if ($this->input->isInteractive()) {
            return multiselect(
                label: 'Which localization goals should run?',
                options: [
                    'labels' => 'Add missing label / heading / modal heading methods where Filament is falling back to make() text',
                    'translations' => 'Wrap Filament text methods with __() when they are still hard-coded',
                    'missing-hebrew' => 'Report translation strings / keys that still have no Hebrew translation',
                ],
                default: ['labels', 'translations'],
                required: true,
            );
        }

        return ['labels', 'translations'];
    }
}
