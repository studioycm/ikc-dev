<?php

namespace App\Services\Localization;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class FilamentLocalizationNormalizer
{
    /**
     * @var array<int, string>
     */
    private array $defaultPaths = [
        'app\\Filament',
        'app\\Livewire',
    ];

    /**
     * @var array<string, string>
     */
    private array $primaryTextMethodByComponent = [
        'Action' => 'label',
        'ActionGroup' => 'label',
        'AttachAction' => 'label',
        'AssociateAction' => 'label',
        'BadgeColumn' => 'label',
        'BooleanColumn' => 'label',
        'BulkAction' => 'label',
        'BulkActionGroup' => 'label',
        'Checkbox' => 'label',
        'CheckboxColumn' => 'label',
        'CheckboxList' => 'label',
        'ColorColumn' => 'label',
        'ColorEntry' => 'label',
        'ColorPicker' => 'label',
        'CreateAction' => 'label',
        'DatePicker' => 'label',
        'DateTimePicker' => 'label',
        'DeleteAction' => 'label',
        'DeleteBulkAction' => 'label',
        'DissociateAction' => 'label',
        'DissociateBulkAction' => 'label',
        'EditAction' => 'label',
        'ExportColumn' => 'label',
        'Fieldset' => 'heading',
        'FileUpload' => 'label',
        'Filter' => 'label',
        'Group' => 'label',
        'HeaderAction' => 'label',
        'IconColumn' => 'label',
        'IconEntry' => 'label',
        'ImageColumn' => 'label',
        'ImageEntry' => 'label',
        'ImportColumn' => 'label',
        'ImportAction' => 'label',
        'KeyValue' => 'label',
        'KeyValueEntry' => 'label',
        'MarkdownEditor' => 'label',
        'ModalAction' => 'label',
        'Placeholder' => 'label',
        'Radio' => 'label',
        'Repeater' => 'label',
        'RepeatableEntry' => 'label',
        'RestoreAction' => 'label',
        'RichEditor' => 'label',
        'Section' => 'heading',
        'Select' => 'label',
        'SelectColumn' => 'label',
        'SelectFilter' => 'label',
        'Split' => 'label',
        'SpatieMediaLibraryFileUpload' => 'label',
        'SpatieMediaLibraryImageColumn' => 'label',
        'SpatieTagsInput' => 'label',
        'Stack' => 'label',
        'Tab' => 'label',
        'Tabs' => 'label',
        'TagsColumn' => 'label',
        'TextColumn' => 'label',
        'TextEntry' => 'label',
        'TextInput' => 'label',
        'TextInputColumn' => 'label',
        'Textarea' => 'label',
        'TimePicker' => 'label',
        'Toggle' => 'label',
        'ToggleButtons' => 'label',
        'ToggleColumn' => 'label',
        'ViewAction' => 'label',
        'ViewColumn' => 'label',
    ];

    /**
     * @var array<int, string>
     */
    private array $translationMethods = [
        'label',
        'heading',
        'modalHeading',
        'modalDescription',
        'tooltip',
        'helperText',
        'hint',
        'description',
        'placeholder',
        'title',
        'searchPlaceholder',
        'emptyStateHeading',
        'emptyStateDescription',
        'successNotificationTitle',
        'failureNotificationTitle',
        'content',
        'body',
        'subject',
        'badge',
        'prefix',
        'suffix',
    ];

    /**
     * @var array<int, string>
     */
    private array $alwaysEnglishValues = [
        'user@example.com',
        'example@example.com',
    ];

    public function run(array $options = []): array
    {
        $goals = $this->normalizeGoals(Arr::get($options, 'goals', []));

        $paths = $this->resolvePaths(
            Arr::get($options, 'paths', []),
            Arr::get($options, 'exclude', [])
        );

        $files = $this->discoverPhpFiles($paths);
        $results = collect();
        $write = (bool)Arr::get($options, 'write', false);

        foreach ($files as $relativePath) {
            $result = $this->analyzeFile(base_path($relativePath), $relativePath, $goals, $write);

            if ($result['findings'] === [] && $result['changes'] === [] && $result['missing_hebrew'] === []) {
                continue;
            }

            $results->push($result);
        }

        return [
            'goals' => $goals,
            'paths' => $paths,
            'write' => $write,
            'files' => $results->all(),
            'summary' => [
                'files_scanned' => count($files),
                'files_with_findings' => $results->count(),
                'findings' => $results->sum(fn(array $file): int => count($file['findings'])),
                'changes' => $results->sum(fn(array $file): int => count($file['changes'])),
                'missing_hebrew' => $results->sum(fn(array $file): int => count($file['missing_hebrew'])),
            ],
        ];
    }

    /**
     * @param array<int, string> $paths
     * @param array<int, string> $exclude
     * @return array<int, string>
     */
    public function resolvePaths(array $paths = [], array $exclude = []): array
    {
        $targets = $paths === [] ? $this->defaultPaths : $paths;
        $excluded = collect($exclude)
            ->map(fn(string $path): string => $this->normalizeRelativePath($path))
            ->filter()
            ->values();

        return collect($targets)
            ->map(fn(string $path): string => $this->normalizeRelativePath($path))
            ->filter(fn(string $path): bool => $path !== '' && file_exists(base_path($path)))
            ->reject(fn(string $path): bool => $excluded->contains($path))
            ->values()
            ->all();
    }

    /**
     * @param array<int, string> $paths
     * @return array<int, string>
     */
    private function discoverPhpFiles(array $paths): array
    {
        return collect($paths)
            ->flatMap(function (string $path): array {
                $absolutePath = base_path($path);

                if (is_file($absolutePath)) {
                    return [str_replace('/', '\\', $path)];
                }

                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($absolutePath, \FilesystemIterator::SKIP_DOTS)
                );

                $files = [];

                foreach ($iterator as $file) {
                    if (!$file->isFile() || $file->getExtension() !== 'php') {
                        continue;
                    }

                    $files[] = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file->getPathname());
                }

                return $files;
            })
            ->map(fn(string $path): string => str_replace('/', '\\', $path))
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @param array<int, string> $goals
     * @return array<string, mixed>
     */
    private function analyzeFile(string $absolutePath, string $relativePath, array $goals, bool $write): array
    {
        $originalContent = file_get_contents($absolutePath);

        if (!is_string($originalContent)) {
            return [
                'path' => $relativePath,
                'findings' => [],
                'changes' => [],
                'missing_hebrew' => [],
            ];
        }

        $content = $originalContent;
        $findings = [];
        $changes = [];
        $missingHebrew = [];

        $componentAliases = $this->resolveImportedComponentAliases($content);

        if (in_array('labels', $goals, true)) {
            [$content, $labelFindings, $labelChanges] = $this->fixMissingPrimaryTextMethods($content, $write, $componentAliases);
            $findings = [...$findings, ...$labelFindings];
            $changes = [...$changes, ...$labelChanges];
        }

        if (in_array('translations', $goals, true)) {
            [$content, $translationFindings, $translationChanges] = $this->fixTranslationWrappers($content, $write);
            $findings = [...$findings, ...$translationFindings];
            $changes = [...$changes, ...$translationChanges];
        }

        if (in_array('missing-hebrew', $goals, true)) {
            $missingHebrew = $this->collectMissingHebrewTranslations($content);
        }

        if ($write && $content !== $originalContent) {
            file_put_contents($absolutePath, $content);
        }

        return [
            'path' => $relativePath,
            'findings' => $this->deduplicateEntries($findings),
            'changes' => $this->deduplicateEntries($changes),
            'missing_hebrew' => $this->deduplicateEntries($missingHebrew),
        ];
    }

    /**
     * @param array<string, string> $componentAliases
     * @return array{0: string, 1: array<int, array<string, mixed>>, 2: array<int, array<string, mixed>>}
     */
    private function fixMissingPrimaryTextMethods(string $content, bool $write, array $componentAliases): array
    {
        $findings = [];
        $changes = [];
        $calls = $this->findPrimaryComponentCalls($content, $componentAliases);

        foreach (array_reverse($calls) as $call) {
            $method = $this->primaryTextMethodByComponent[$call['component']];
            $snippet = substr($content, $call['start_offset'], $call['end_offset'] - $call['start_offset']);

            if (!is_string($snippet) || !$this->shouldAddPrimaryTextMethod($call['component'], $method, $snippet, $call['key'])) {
                continue;
            }

            $label = $this->humanizeKey($call['key']);
            $updatedSnippet = $this->insertPrimaryTextMethod($snippet, $call['make_close_offset'] - $call['start_offset'], $call['indent'], $method, $label);

            if ($updatedSnippet === $snippet) {
                continue;
            }

            $entry = [
                'type' => 'missing-primary-text-method',
                'component' => $call['component'],
                'method' => $method,
                'key' => $call['key'],
                'text' => $label,
            ];

            $findings[] = $entry;

            if ($write) {
                $changes[] = $entry;
            }

            $content = substr_replace(
                $content,
                $updatedSnippet,
                $call['start_offset'],
                $call['end_offset'] - $call['start_offset'],
            );
        }

        return [$content, $findings, $changes];
    }

    /**
     * @param array<string, string> $componentAliases
     * @return array<int, array<string, int|string>>
     */
    private function findPrimaryComponentCalls(string $content, array $componentAliases): array
    {
        $tokens = $this->tokenizeContent($content);
        $calls = [];

        foreach ($tokens as $index => $token) {
            if ($token['text'] !== '::') {
                continue;
            }

            $componentIndex = $this->previousSignificantTokenIndex($tokens, $index);
            $makeIndex = $this->nextSignificantTokenIndex($tokens, $index);

            if ($componentIndex === null || $makeIndex === null || strtolower($tokens[$makeIndex]['text']) !== 'make') {
                continue;
            }

            $openParenthesisIndex = $this->nextSignificantTokenIndex($tokens, $makeIndex);

            if ($openParenthesisIndex === null || $tokens[$openParenthesisIndex]['text'] !== '(') {
                continue;
            }

            $argumentIndex = $this->nextSignificantTokenIndex($tokens, $openParenthesisIndex);

            if ($argumentIndex === null || $tokens[$argumentIndex]['id'] !== T_CONSTANT_ENCAPSED_STRING) {
                continue;
            }

            $component = $this->resolveComponentName($tokens[$componentIndex]['text'], $componentAliases);

            if (!array_key_exists($component, $this->primaryTextMethodByComponent)) {
                continue;
            }

            $closingParenthesisIndex = $this->findMatchingTokenIndex($tokens, $openParenthesisIndex, '(', ')');

            if ($closingParenthesisIndex === null) {
                continue;
            }

            $boundaryIndex = $this->findChainBoundaryTokenIndex($tokens, $closingParenthesisIndex);
            $startOffset = $tokens[$componentIndex]['offset'];
            $endOffset = $boundaryIndex === null
                ? strlen($content)
                : $tokens[$boundaryIndex]['offset'];

            $calls[] = [
                'component' => $component,
                'key' => $this->decodePhpStringToken($tokens[$argumentIndex]['text']),
                'start_offset' => $startOffset,
                'end_offset' => $endOffset,
                'make_close_offset' => $tokens[$closingParenthesisIndex]['offset'] + strlen($tokens[$closingParenthesisIndex]['text']) - 1,
                'indent' => $this->lineIndentationAt($content, $startOffset),
            ];
        }

        return $calls;
    }

    /**
     * @return array<int, array{id: int|string|null, text: string, offset: int}>
     */
    private function tokenizeContent(string $content): array
    {
        $tokens = [];
        $offset = 0;

        foreach (token_get_all($content) as $token) {
            $text = is_array($token) ? $token[1] : $token;

            $tokens[] = [
                'id' => is_array($token) ? $token[0] : null,
                'text' => $text,
                'offset' => $offset,
            ];

            $offset += strlen($text);
        }

        return $tokens;
    }

    /**
     * @param array<int, array{id: int|string|null, text: string, offset: int}> $tokens
     */
    private function previousSignificantTokenIndex(array $tokens, int $index): ?int
    {
        for ($cursor = $index - 1; $cursor >= 0; $cursor--) {
            if (!$this->isIgnorableToken($tokens[$cursor])) {
                return $cursor;
            }
        }

        return null;
    }

    /**
     * @param array<int, array{id: int|string|null, text: string, offset: int}> $tokens
     */
    private function nextSignificantTokenIndex(array $tokens, int $index): ?int
    {
        for ($cursor = $index + 1; $cursor < count($tokens); $cursor++) {
            if (!$this->isIgnorableToken($tokens[$cursor])) {
                return $cursor;
            }
        }

        return null;
    }

    /**
     * @param array{id: int|string|null, text: string, offset: int} $token
     */
    private function isIgnorableToken(array $token): bool
    {
        return in_array($token['id'], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true);
    }

    /**
     * @param array<string, string> $componentAliases
     */
    private function resolveComponentName(string $componentExpression, array $componentAliases): string
    {
        $componentExpression = ltrim(trim($componentExpression), '\\');

        if (str_contains($componentExpression, '\\')) {
            return Str::afterLast($componentExpression, '\\');
        }

        return $componentAliases[$componentExpression] ?? $componentExpression;
    }

    /**
     * @param array<int, array{id: int|string|null, text: string, offset: int}> $tokens
     */
    private function findMatchingTokenIndex(array $tokens, int $openingIndex, string $openingToken, string $closingToken): ?int
    {
        $depth = 0;

        for ($cursor = $openingIndex; $cursor < count($tokens); $cursor++) {
            $text = $tokens[$cursor]['text'];

            if ($text === $openingToken) {
                $depth++;
            }

            if ($text === $closingToken) {
                $depth--;

                if ($depth === 0) {
                    return $cursor;
                }
            }
        }

        return null;
    }

    /**
     * @param array<int, array{id: int|string|null, text: string, offset: int}> $tokens
     */
    private function findChainBoundaryTokenIndex(array $tokens, int $closingParenthesisIndex): ?int
    {
        $parenthesesDepth = 0;
        $bracketsDepth = 0;
        $bracesDepth = 0;

        for ($cursor = $closingParenthesisIndex + 1; $cursor < count($tokens); $cursor++) {
            $text = $tokens[$cursor]['text'];

            if ($this->isIgnorableToken($tokens[$cursor])) {
                continue;
            }

            match ($text) {
                '(' => $parenthesesDepth++,
                ')' => $parenthesesDepth--,
                '[' => $bracketsDepth++,
                ']' => $bracketsDepth--,
                '{' => $bracesDepth++,
                '}' => $bracesDepth--,
                default => null,
            };

            if ($parenthesesDepth !== 0 || $bracketsDepth !== 0 || $bracesDepth !== 0) {
                continue;
            }

            if (in_array($text, [',', ';', ']', ')', '}'], true)) {
                return $cursor;
            }
        }

        return null;
    }

    private function insertPrimaryTextMethod(string $snippet, int $makeCloseOffset, string $indent, string $method, string $label): string
    {
        $methodCall = '->' . $method . "(__('" . addslashes($label) . "'))";
        $before = substr($snippet, 0, $makeCloseOffset + 1);
        $after = substr($snippet, $makeCloseOffset + 1);

        if (!is_string($before) || !is_string($after)) {
            return $snippet;
        }

        if ($after === '') {
            return $before . $methodCall;
        }

        if (preg_match('/^\s*->/', $after) === 1 && !preg_match('/\R/', $after)) {
            return $before . $methodCall . $after;
        }

        if (preg_match('/^\s*[;,\]}]/', $after) === 1) {
            return $before . $methodCall . $after;
        }

        return $before . PHP_EOL . $indent . '    ' . $methodCall . $after;
    }

    /**
     * @return array<string, string>
     */
    private function resolveImportedComponentAliases(string $content): array
    {
        preg_match_all('/^use\s+([^;{]+?)(?:\s+as\s+(\w+))?;/m', $content, $matches, PREG_SET_ORDER);

        $aliases = [];

        foreach ($matches as $match) {
            $import = trim($match[1]);
            $alias = $match[2] ?? Str::afterLast($import, '\\');
            $aliases[$alias] = Str::afterLast($import, '\\');
        }

        return $aliases;
    }

    private function decodePhpStringToken(string $token): string
    {
        $quote = substr($token, 0, 1);
        $contents = substr($token, 1, -1);

        if (!is_string($contents) || $contents === false || ($quote !== '\'' && $quote !== '"')) {
            return $token;
        }

        return $this->decodePhpStringContent($contents, $quote);
    }

    private function decodePhpStringContent(string $content, string $quote): string
    {
        $replacements = $quote === '"'
            ? ['\\"' => '"', '\\\\' => '\\']
            : ['\\\'' => '\'', '\\\\' => '\\'];

        return strtr($content, $replacements);
    }

    private function lineIndentationAt(string $content, int $offset): string
    {
        $lineStart = max(
                strrpos(substr($content, 0, $offset), "\n") ?: -1,
                strrpos(substr($content, 0, $offset), "\r") ?: -1,
            ) + 1;

        $linePrefix = substr($content, $lineStart, $offset - $lineStart);

        if (!is_string($linePrefix)) {
            return '';
        }

        preg_match('/^[ \t]*/', $linePrefix, $matches);

        return $matches[0] ?? '';
    }

    private function shouldAddPrimaryTextMethod(string $component, string $method, string $snippet, string $key): bool
    {
        if (!preg_match('/[A-Za-z]/', $key)) {
            return false;
        }

        if ($this->containsAnyMethod($snippet, [$method, 'hiddenLabel', 'hiddenHeading'])) {
            return false;
        }

        if ($this->containsLabelSuppression($snippet, $method)) {
            return false;
        }

        if ($component === 'Hidden') {
            return false;
        }

        if ($component === 'Action' && preg_match('/->iconButton\s*\(/', $snippet)) {
            return false;
        }

        if (preg_match('/->(?:labeledFrom|labelBetween)\s*\(/', $snippet)) {
            return false;
        }

        return true;
    }

    private function containsLabelSuppression(string $snippet, string $method): bool
    {
        if ($method === 'label') {
            return preg_match('/->label\s*\(\s*(?:false|[\'\"]\s*[\'\"])\s*\)/', $snippet) === 1;
        }

        if ($method === 'heading') {
            return preg_match('/->heading\s*\(\s*(?:false|[\'\"]\s*[\'\"])\s*\)/', $snippet) === 1;
        }

        return false;
    }

    private function containsAnyMethod(string $snippet, array $methods): bool
    {
        foreach ($methods as $method) {
            if (preg_match(sprintf('/->%s\s*\(/', preg_quote($method, '/')), $snippet) === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array{0: string, 1: array<int, array<string, mixed>>, 2: array<int, array<string, mixed>>}
     */
    private function fixTranslationWrappers(string $content, bool $write): array
    {
        $findings = [];
        $changes = [];

        foreach ($this->translationMethods as $method) {
            $pattern = sprintf('/->%s\(\s*(?P<literal>(?P<quote>[\'\"])(?P<text>(?:\\.|(?!(?P=quote)).)*)(?P=quote))\s*\)/s', preg_quote($method, '/'));

            $content = (string)preg_replace_callback($pattern, function (array $matches) use ($method, $write, &$findings, &$changes): string {
                $text = $this->decodePhpStringContent($matches['text'], $matches['quote']);

                if (!$this->shouldTranslateTextMethod($method, $text)) {
                    return $matches[0];
                }

                $updated = sprintf('->%s(__(%s))', $method, $matches['literal']);

                $entry = [
                    'type' => 'missing-translation-wrapper',
                    'method' => $method,
                    'text' => $text,
                ];

                $findings[] = $entry;

                if ($write) {
                    $changes[] = $entry;
                }

                return $updated;
            }, $content) ?? $content;
        }

        return [$content, $findings, $changes];
    }

    private function shouldTranslateTextMethod(string $method, string $text): bool
    {
        $trimmed = trim($text);

        if ($trimmed === '' || !preg_match('/[A-Za-z]/', $trimmed)) {
            return false;
        }

        if (in_array($trimmed, $this->alwaysEnglishValues, true)) {
            return false;
        }

        if (Str::contains($trimmed, ['::', '->', 'route(', 'view(', 'heroicon-', 'filament-', 'wire:', 'x-'])) {
            return false;
        }

        if ($method === 'placeholder' && filter_var($trimmed, FILTER_VALIDATE_EMAIL) !== false) {
            return false;
        }

        return true;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function collectMissingHebrewTranslations(string $content): array
    {
        $translations = $this->loadHebrewTranslations();
        $entries = [];
        $seen = [];

        $patterns = [
            '/__\(\s*(?P<literal>(?P<quote>[\'\"])(?P<key>(?:\\.|(?!(?P=quote)).)*)(?P=quote))/s',
            '/trans\(\s*(?P<literal>(?P<quote>[\'\"])(?P<key>(?:\\.|(?!(?P=quote)).)*)(?P=quote))/s',
            '/trans_choice\(\s*(?P<literal>(?P<quote>[\'\"])(?P<key>(?:\\.|(?!(?P=quote)).)*)(?P=quote))/s',
        ];

        foreach ($patterns as $pattern) {
            preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $normalizedKey = $this->decodePhpStringContent($match['key'], $match['quote']);

                if ($normalizedKey === '' || isset($seen[$normalizedKey])) {
                    continue;
                }

                $seen[$normalizedKey] = true;

                if ($this->translationExists($translations, $normalizedKey)) {
                    continue;
                }

                $entries[] = [
                    'type' => 'missing-hebrew-translation',
                    'key' => $normalizedKey,
                    'text' => $normalizedKey,
                ];
            }
        }

        return $entries;
    }

    /**
     * @return array<string, mixed>
     */
    private function loadHebrewTranslations(): array
    {
        static $translations;

        if (is_array($translations)) {
            return $translations;
        }

        $translations = [];
        $jsonPath = lang_path('he.json');

        if (is_file($jsonPath)) {
            $decoded = json_decode((string)file_get_contents($jsonPath), true);

            if (is_array($decoded)) {
                $translations = [...$translations, ...$decoded];
            }
        }

        $hePath = lang_path('he');

        if (!is_dir($hePath)) {
            return $translations;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($hePath, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $group = str_replace(['/', '\\', '.php'], ['.', '.', ''], substr($file->getPathname(), strlen($hePath) + 1));
            $data = include $file->getPathname();

            if (!is_array($data)) {
                continue;
            }

            foreach (Arr::dot($data, $group . '.') as $key => $value) {
                $translations[$key] = $value;
            }
        }

        return $translations;
    }

    /**
     * @param array<string, mixed> $translations
     */
    private function translationExists(array $translations, string $key): bool
    {
        return array_key_exists($key, $translations) && filled($translations[$key]);
    }

    /**
     * @param array<int, string> $goals
     * @return array<int, string>
     */
    private function normalizeGoals(array $goals): array
    {
        return collect($goals === [] ? ['labels', 'translations'] : $goals)
            ->map(fn(string $goal): string => Str::of($goal)->trim()->lower()->replace('_', '-')->value())
            ->filter(fn(string $goal): bool => in_array($goal, ['labels', 'translations', 'missing-hebrew'], true))
            ->unique()
            ->values()
            ->all();
    }

    private function normalizeRelativePath(string $path): string
    {
        return str_replace('/', '\\', trim($path));
    }

    private function humanizeKey(string $key): string
    {
        if (Str::contains($key, '.')) {
            $key = Str::afterLast($key, '.');
        }

        $label = preg_replace('/(?<=\p{Ll})(\p{Lu})/u', ' $1', $key) ?? $key;
        $label = str_replace(['_', '-'], ' ', $label);
        $label = preg_replace('/\s+/', ' ', $label) ?? $label;
        $label = Str::title(trim($label));

        return strtr($label, [
            ' Id' => ' ID',
            ' En' => ' EN',
            ' He' => ' HE',
            ' Otp' => ' OTP',
            ' Dna' => ' DNA',
            ' Fci' => ' FCI',
            ' Uuid' => 'UUID',
            ' Ip' => ' IP',
            ' Ok' => ' OK',
        ]);
    }

    /**
     * @param array<int, array<string, mixed>> $entries
     * @return array<int, array<string, mixed>>
     */
    private function deduplicateEntries(array $entries): array
    {
        return Collection::make($entries)
            ->unique(fn(array $entry): string => md5(json_encode($entry, JSON_THROW_ON_ERROR)))
            ->values()
            ->all();
    }
}
