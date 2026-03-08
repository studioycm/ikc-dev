<?php

namespace App\Services\Legacy\Pedigree;

use App\Enums\Legacy\LegacyDogGender;
use App\Models\PrevDog;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;

class PedigreeTreeBuilderService
{
    protected array $dogColumns = [
        'id',
        'SagirID',
        'Heb_Name',
        'Eng_Name',
        'FatherSAGIR',
        'MotherSAGIR',
        'RaceID',
        'ColorID',
        'BeitGidulID',
        'ImportNumber',
        'BirthDate',
        'GenderID',
    ];

    public function build(
        int    $dogId,
        int    $depth = 4,
        string $direction = 'rtl',
        bool   $includeTitles = false,
    ): array
    {
        $depth = max(2, min(5, $depth));
        $direction = $direction === 'ltr' ? 'ltr' : 'rtl';

        $dog = $this->loadRootDog(
            dogId: $dogId,
            depth: $depth,
            includeTitles: $includeTitles,
        );

        return [
            'root' => $dog ? $this->normalizeDog($dog) : null,
            'depth' => $depth,
            'direction' => $direction,
            'column_count' => $depth,
            'row_count' => 2 ** $depth,
            'generation_headers' => $this->buildGenerationHeaders(
                depth: $depth,
                direction: $direction,
            ),
            'nodes' => $dog
                ? $this->buildAncestorNodes(
                    dog: $dog,
                    depth: $depth,
                    direction: $direction,
                )
                : [],
        ];
    }

    protected function loadRootDog(
        int  $dogId,
        int  $depth,
        bool $includeTitles,
    ): ?PrevDog
    {
        return PrevDog::query()
            ->select($this->dogColumns)
            ->with($this->nodeRelations($includeTitles))
            ->with($this->pedigreeRelations($depth, $includeTitles))
            ->find($dogId);
    }

    protected function nodeRelations(bool $includeTitles): array
    {
        $relations = [
            'breed:BreedCode,BreedName',
            'color:OldCode,ColorNameHE',
            'breedinghouse:GidulCode,HebName,EngName',
        ];

        if ($includeTitles) {
            $relations[] = 'titles';
        }

        return $relations;
    }

    protected function pedigreeRelations(int $depth, bool $includeTitles): array
    {
        if ($depth < 1) {
            return [];
        }

        return [
            'father' => fn(Relation $query) => $this->configureAncestorQuery(
                query: $query,
                remainingDepth: $depth,
                includeTitles: $includeTitles,
            ),
            'mother' => fn(Relation $query) => $this->configureAncestorQuery(
                query: $query,
                remainingDepth: $depth,
                includeTitles: $includeTitles,
            ),
        ];
    }

    protected function configureAncestorQuery(
        Relation $query,
        int      $remainingDepth,
        bool     $includeTitles,
    ): void
    {
        $query
            ->select($this->dogColumns)
            ->with($this->nodeRelations($includeTitles));

        if ($remainingDepth > 1) {
            $query->with(
                $this->pedigreeRelations($remainingDepth - 1, $includeTitles),
            );
        }
    }

    protected function buildGenerationHeaders(int $depth, string $direction): array
    {
        $headers = [];

        for ($generation = 1; $generation <= $depth; $generation++) {
            $headers[] = [
                'generation' => $generation,
                'label' => $this->generationLabel($generation),
                'column_start' => $direction === 'rtl'
                    ? ($depth - $generation + 1)
                    : $generation,
            ];
        }

        usort(
            $headers,
            fn(array $a, array $b): int => $a['column_start'] <=> $b['column_start'],
        );

        return $headers;
    }

    protected function generationLabel(int $generation): string
    {
        return match ($generation) {
            1 => __('Parents'),
            2 => __('Grandparents'),
            3 => __('Great Grandparents'),
            4 => __('4th generation'),
            5 => __('5th generation'),
            default => __(':n Generation', ['generation' => $generation]),
        };
    }

    protected function buildAncestorNodes(
        PrevDog $dog,
        int     $depth,
        string  $direction,
    ): array
    {
        $nodes = [];

        /**
         * The tree starts from parents, not from the root dog.
         * Nulls are preserved intentionally so later generations never collapse left/right.
         */
        $currentGeneration = [$dog->father, $dog->mother];

        for ($generation = 1; $generation <= $depth; $generation++) {
            $rowSpan = 2 ** ($depth - $generation);
            $columnStart = $direction === 'rtl'
                ? ($depth - $generation + 1)
                : $generation;

            foreach ($currentGeneration as $index => $ancestor) {
                $nodes[] = [
                    'key' => "g{$generation}-i{$index}",
                    'generation' => $generation,
                    'index' => $index,
                    'column_start' => $columnStart,
                    'row_start' => ($index * $rowSpan) + 1,
                    'row_span' => $rowSpan,
                    'is_placeholder' => !($ancestor instanceof PrevDog),
                    'dog' => $ancestor instanceof PrevDog
                        ? $this->normalizeDog($ancestor)
                        : $this->emptyDog(),
                ];
            }

            $nextGeneration = [];

            foreach ($currentGeneration as $ancestor) {
                if ($ancestor instanceof PrevDog) {
                    $nextGeneration[] = $ancestor->father;
                    $nextGeneration[] = $ancestor->mother;

                    continue;
                }

                $nextGeneration[] = null;
                $nextGeneration[] = null;
            }

            $currentGeneration = $nextGeneration;
        }

        return $nodes;
    }

    protected function normalizeDog(PrevDog $dog): array
    {
        $titles = [];

        if ($dog->relationLoaded('titles')) {
            $titles = $dog->titles
                ->pluck('TitleName')
                ->filter()
                ->take(8)
                ->values()
                ->all();
        }

        return [
            'id' => $dog->getKey(),
            'sagir_id' => $dog->SagirID,
            'import_number' => $this->blankToNull($dog->ImportNumber),
            'name_he' => $this->blankToNull($dog->Heb_Name),
            'name_en' => $this->blankToNull($dog->Eng_Name),
            'full_name' => $dog->full_name,
            'breed_name' => $dog->relationLoaded('breed')
                ? $this->blankToNull($dog->breed?->BreedName)
                : null,
            'breeding_house' => $dog->relationLoaded('breedinghouse')
                ? $this->blankToNull($dog->breedinghouse?->name)
                : null,
            'color_name' => $dog->relationLoaded('color')
                ? $this->blankToNull($dog->color?->ColorNameHE)
                : null,
            'birth_date' => $dog->BirthDate?->format('d/m/Y'),
            'age' => $this->formatAge($dog->BirthDate),
            'gender_value' => $this->normalizeGenderValue($dog->GenderID),
            'gender_label' => $this->genderLabel($dog->GenderID),
            'father_sagir' => $dog->FatherSAGIR,
            'mother_sagir' => $dog->MotherSAGIR,
            'titles' => $titles,
        ];
    }

    protected function emptyDog(): array
    {
        return [
            'id' => null,
            'sagir_id' => null,
            'import_number' => null,
            'name_he' => null,
            'name_en' => null,
            'full_name' => '—',
            'breed_name' => null,
            'breeding_house' => null,
            'color_name' => null,
            'birth_date' => null,
            'age' => null,
            'gender_value' => null,
            'gender_label' => __('Unknown'),
            'father_sagir' => null,
            'mother_sagir' => null,
            'titles' => [],
        ];
    }

    protected function normalizeGenderValue(mixed $gender): int|string|null
    {
        if ($gender instanceof LegacyDogGender) {
            return $gender->value;
        }

        return $gender;
    }

    protected function genderLabel(mixed $gender): string
    {
        $value = $this->normalizeGenderValue($gender);

        return match ($value) {
            1 => __('Sire'),
            2 => __('Dam'),
            default => __('Unknown'),
        };
    }

    protected function formatAge(mixed $birthDate): ?string
    {
        if (blank($birthDate)) {
            return null;
        }

        $birth = $birthDate instanceof Carbon
            ? $birthDate
            : Carbon::parse($birthDate);

        if ($birth->isFuture()) {
            return null;
        }

        $totalMonths = (int)$birth->diffInMonths(now());
        $years = intdiv($totalMonths, 12);
        $months = $totalMonths % 12;

        return "{$years}y {$months}m";
    }

    protected function blankToNull(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string)$value);

        return $value === '' ? null : $value;
    }
}
