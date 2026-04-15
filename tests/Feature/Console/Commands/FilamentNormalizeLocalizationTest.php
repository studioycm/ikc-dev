<?php

use App\Services\Localization\FilamentLocalizationNormalizer;

beforeEach(function (): void {
    $fixtureDirectory = base_path('tests\\Fixtures\\FilamentLocalization');

    if (!is_dir($fixtureDirectory)) {
        mkdir($fixtureDirectory, 0777, true);
    }
});

it('defaults to the filament and livewire folders', function () {
    $paths = app(FilamentLocalizationNormalizer::class)->resolvePaths();

    expect($paths)
        ->toContain('app\\Filament')
        ->toContain('app\\Livewire');
});

it('reports findings without counting dry-run changes as applied', function () {
    $fixturePath = base_path('tests\\Fixtures\\FilamentLocalization\\DryRunCountsFixture.php');

    file_put_contents($fixturePath, <<<'PHP'
<?php

use Filament\Forms\Components\Section;

return Section::make('identity')
    ->schema([]);
PHP
    );

    $result = app(FilamentLocalizationNormalizer::class)->run([
        'paths' => ['tests\\Fixtures\\FilamentLocalization\\DryRunCountsFixture.php'],
        'goals' => ['labels'],
        'write' => false,
    ]);

    expect($result['summary']['findings'])->toBe(1)
        ->and($result['summary']['changes'])->toBe(0)
        ->and($result['files'][0]['findings'])->toHaveCount(1)
        ->and($result['files'][0]['changes'])->toBeEmpty();
});

it('adds missing heading methods for section components in dry-run mode', function () {
    $fixturePath = base_path('tests\\Fixtures\\FilamentLocalization\\SectionFixture.php');

    file_put_contents($fixturePath, <<<'PHP'
<?php

use Filament\Forms\Components\Section;

return Section::make('identity')
    ->schema([]);
PHP
    );

    $this->artisan('filament:normalize-localization', [
        'paths' => ['tests\\Fixtures\\FilamentLocalization\\SectionFixture.php'],
        '--goal' => ['labels'],
    ])
        ->assertSuccessful();

    expect(file_get_contents($fixturePath))->not->toContain("->heading(__('Identity'))");
});

it('writes missing heading methods when write mode is enabled', function () {
    $fixturePath = base_path('tests\\Fixtures\\FilamentLocalization\\SectionWriteFixture.php');

    file_put_contents($fixturePath, <<<'PHP'
<?php

use Filament\Forms\Components\Section;

return Section::make('identity')
    ->schema([]);
PHP
    );

    $this->artisan('filament:normalize-localization', [
        'paths' => ['tests\\Fixtures\\FilamentLocalization\\SectionWriteFixture.php'],
        '--goal' => ['labels'],
        '--write' => true,
    ])->assertSuccessful();

    expect(file_get_contents($fixturePath))->toContain("->heading(__('Identity'))");
});

it('wraps translatable text methods and skips always-english email placeholders', function () {
    $fixturePath = base_path('tests\\Fixtures\\FilamentLocalization\\TranslationFixture.php');

    file_put_contents($fixturePath, <<<'PHP'
<?php

use Filament\Forms\Components\TextInput;

return TextInput::make('email')
    ->label('Email')
    ->placeholder('user@example.com')
    ->helperText('Search by email');
PHP
    );

    $this->artisan('filament:normalize-localization', [
        'paths' => ['tests\\Fixtures\\FilamentLocalization\\TranslationFixture.php'],
        '--goal' => ['translations'],
        '--write' => true,
    ])->assertSuccessful();

    $content = file_get_contents($fixturePath);

    expect($content)
        ->toContain("->label(__('Email'))")
        ->toContain("->helperText(__('Search by email'))")
        ->toContain("->placeholder('user@example.com')");
});

it('wraps multiline text methods', function () {
    $fixturePath = base_path('tests\\Fixtures\\FilamentLocalization\\MultilineTranslationFixture.php');

    file_put_contents($fixturePath, <<<'PHP'
<?php

use Filament\Forms\Components\TextInput;

return TextInput::make('email')
    ->label(
        'Email Address'
    )
    ->helperText(
        'Search by email'
    );
PHP
    );

    $this->artisan('filament:normalize-localization', [
        'paths' => ['tests\\Fixtures\\FilamentLocalization\\MultilineTranslationFixture.php'],
        '--goal' => ['translations'],
        '--write' => true,
    ])->assertSuccessful();

    $content = file_get_contents($fixturePath);

    expect($content)
        ->toContain("->label(__('Email Address'))")
        ->toContain("->helperText(__('Search by email'))");
});

it('reports missing hebrew translations when requested', function () {
    $fixturePath = base_path('tests\\Fixtures\\FilamentLocalization\\MissingHebrewFixture.php');

    file_put_contents($fixturePath, <<<'PHP'
<?php

return __('A brand new translation key');
PHP
    );

    $this->artisan('filament:normalize-localization', [
        'paths' => ['tests\\Fixtures\\FilamentLocalization\\MissingHebrewFixture.php'],
        '--goal' => ['missing-hebrew'],
    ])
        ->assertSuccessful()
        ->expectsOutputToContain('[missing-hebrew] A brand new translation key');
});

it('reports missing hebrew translations for trans and trans_choice helpers', function () {
    $fixturePath = base_path('tests\\Fixtures\\FilamentLocalization\\HelperTranslationFixture.php');

    file_put_contents($fixturePath, <<<'PHP'
<?php

return trans('A different missing translation key').trans_choice('Another missing translation key', 2);
PHP
    );

    $result = app(FilamentLocalizationNormalizer::class)->run([
        'paths' => ['tests\\Fixtures\\FilamentLocalization\\HelperTranslationFixture.php'],
        'goals' => ['missing-hebrew'],
        'write' => false,
    ]);

    expect($result['files'][0]['missing_hebrew'])
        ->toHaveCount(2)
        ->sequence(
            fn($entry) => $entry->toMatchArray(['key' => 'A different missing translation key']),
            fn($entry) => $entry->toMatchArray(['key' => 'Another missing translation key']),
        );
});

afterEach(function (): void {
    $fixtureDirectory = base_path('tests\\Fixtures\\FilamentLocalization');

    if (!is_dir($fixtureDirectory)) {
        return;
    }

    foreach (glob($fixtureDirectory . DIRECTORY_SEPARATOR . '*.php') ?: [] as $file) {
        @unlink($file);
    }

    @rmdir($fixtureDirectory);
});
