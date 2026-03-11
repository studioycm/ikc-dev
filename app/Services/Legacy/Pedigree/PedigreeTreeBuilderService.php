<?php

namespace App\Services\Legacy\Pedigree;

use App\Enums\Legacy\LegacyDogGender;
use App\Models\PrevDog;
use App\Models\PrevUser;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;

class PedigreeTreeBuilderService
{
    protected array $ancestorColumns = [
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

    protected array $rootColumns = [
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
        'RegDate',
        'PedigreeNotes',
        'GenderID',
        'Chip',
    ];

    public function build(
        int  $dogId,
        int  $depth = 4,
        string $direction = 'rtl',
        bool $includeNodeTitles = false,
    ): array
    {
        $depth = max(2, min(8, $depth));
        $direction = $direction === 'ltr' ? 'ltr' : 'rtl';

        $dog = $this->loadRootDog(
            dogId: $dogId,
            depth: $depth,
            includeNodeTitles: $includeNodeTitles,
        );

        return [
            'root' => $dog ? $this->normalizeRootDog($dog) : null,
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
        bool $includeNodeTitles,
    ): ?PrevDog
    {
        return PrevDog::query()
            ->select($this->rootColumns)
            ->with($this->rootRelations())
            ->with($this->pedigreeRelations($depth, $includeNodeTitles))
            ->find($dogId);
    }

    protected function rootRelations(): array
    {
        return [
            'breed:BreedCode,BreedName,BreedNameEN',
            'color:OldCode,ColorNameHE,ColorNameEN',
            'breedinghouse:id,GidulCode,HebName,EngName',
            'owners' => fn(BelongsToMany $query) => $query->select($this->ownerSelectColumns()),
            'breedinghouse.users' => fn(BelongsToMany $query) => $query->select($this->ownerSelectColumns()),
            'titles' => fn(BelongsToMany $query) => $query->select([
                'dogs_titles_db.TitleCode',
                'dogs_titles_db.TitleName',
            ]),
        ];
    }

    protected function nodeRelations(bool $includeTitles): array
    {
        $relations = [
            'breed:BreedCode,BreedName,BreedNameEN',
            'color:OldCode,ColorNameHE,ColorNameEN',
            'breedinghouse:id,GidulCode,HebName,EngName',
        ];

        if ($includeTitles) {
            $relations['titles'] = fn(BelongsToMany $query) => $query->select([
                'dogs_titles_db.TitleCode',
                'dogs_titles_db.TitleName',
            ]);
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
        int  $remainingDepth,
        bool $includeTitles,
    ): void
    {
        $query
            ->select($this->ancestorColumns)
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
            6 => __('6th generation'),
            7 => __('7th generation'),
            8 => __('8th generation'),
            default => __('Generation :generation', ['generation' => $generation]),
        };
    }

    protected function buildAncestorNodes(
        PrevDog $dog,
        int    $depth,
        string $direction,
    ): array
    {
        $nodes = [];
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
                        ? $this->normalizeNodeDog($ancestor)
                        : $this->emptyNodeDog(),
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

    protected function normalizeRootDog(PrevDog $dog): array
    {
        $titles = $this->normalizeTitles($dog);
        $firstOwner = $dog->relationLoaded('owners') ? $dog->owners->first() : null;
        $ownerData = $firstOwner ? $this->normalizeUser($firstOwner) : null;
        $breederNames = $this->resolveBreederNames($dog);

        return array_merge(
            $this->normalizeCommonDogData($dog),
            [
                'chip' => $this->blankToNull($dog->getAttribute('Chip') ?? $dog->getAttribute('chip')),
                'reg_date' => $dog->RegDate?->format('d/m/Y'),
                'pedigree_notes' => $this->blankToNull($dog->getAttribute('PedigreeNotes')),
                'gender_label_raw' => $this->standardGenderLabel($dog->GenderID),

                'owner_first_name' => $ownerData['first_name'] ?? null,
                'owner_last_name' => $ownerData['last_name'] ?? null,
                'owner_first_name_en' => $ownerData['first_name_en'] ?? null,
                'owner_last_name_en' => $ownerData['last_name_en'] ?? null,
                'owner_name_he' => $ownerData['name_he'] ?? null,
                'owner_name_en' => $ownerData['name_en'] ?? null,
                'owner_name' => $ownerData['display_name'] ?? null,
                'owner_address_array' => $ownerData['address_array'] ?? [],
                'owner_address' => $ownerData['address_text'] ?? null,

                'breeder_names' => $breederNames,
                'breeder_text' => filled($breederNames) ? implode(', ', $breederNames) : null,

                'titles' => $titles,
                'titles_count' => count($titles),
                'titles_text' => implode(' • ', $titles),
            ],
        );
    }

    protected function normalizeNodeDog(PrevDog $dog): array
    {
        $titles = $this->normalizeTitles($dog);
        $titlesText = implode(' • ', $titles);

        return array_merge(
            $this->normalizeCommonDogData($dog),
            [
                'gender_label' => $this->pedigreeGenderLabel($dog->GenderID),
                'titles' => $titles,
                'titles_count' => count($titles),
                'titles_text' => $titlesText,
                'titles_has_popup' => count($titles) > 2 || mb_strlen($titlesText) > 90,
            ],
        );
    }

    protected function normalizeCommonDogData(PrevDog $dog): array
    {
        return [
            'id' => $dog->getKey(),
            'sagir_id' => $dog->SagirID,
            'import_number' => $this->blankToNull($dog->ImportNumber),
            'name_he' => $this->blankToNull($dog->Heb_Name),
            'name_en' => $this->blankToNull($dog->Eng_Name),
            'full_name' => $dog->full_name,
            'breed_name' => $dog->relationLoaded('breed')
                ? $this->blankToNull($dog->breed?->BreedName ?? $dog->breed?->BreedNameEN)
                : null,
            'breeding_house' => $dog->relationLoaded('breedinghouse')
                ? $this->resolveBreedingHouseName($dog)
                : null,
            'color_name' => $dog->relationLoaded('color')
                ? $this->blankToNull($dog->color?->ColorNameHE ?? $dog->color?->ColorNameEN)
                : null,
            'birth_date' => $dog->BirthDate?->format('d/m/Y'),
            'age' => $this->formatAge($dog->BirthDate),
            'gender_value' => $this->normalizeGenderValue($dog->GenderID),
            'father_sagir' => $dog->FatherSAGIR,
            'mother_sagir' => $dog->MotherSAGIR,
        ];
    }

    protected function emptyNodeDog(): array
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
            'titles_count' => 0,
            'titles_text' => null,
            'titles_has_popup' => false,
        ];
    }

    protected function normalizeTitles(PrevDog $dog): array
    {
        if (!$dog->relationLoaded('titles')) {
            return [];
        }

        return $dog->titles
            ->pluck('TitleName')
            ->map(fn($title) => $this->blankToNull($title))
            ->filter()
            ->values()
            ->all();
    }

    protected function normalizeUser(PrevUser $user): array
    {
        $firstName = $this->blankToNull($user->first_name);
        $lastName = $this->blankToNull($user->last_name);
        $firstNameEn = $this->blankToNull($user->first_name_en);
        $lastNameEn = $this->blankToNull($user->last_name_en);

        $nameHe = $this->joinName([$firstName, $lastName]);
        $nameEn = $this->joinName([$firstNameEn, $lastNameEn]);
        $displayName = $this->joinLocalizedNames([$nameHe, $nameEn]);

        $addressArray = method_exists($user, 'addressArray')
            ? $user->addressArray()
            : ((array)($user->address ?? []));

        $addressText = method_exists($user, 'buildAddress')
            ? $this->blankToNull($user->buildAddress())
            : $this->joinAddressParts($addressArray);

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'first_name_en' => $firstNameEn,
            'last_name_en' => $lastNameEn,
            'name_he' => $nameHe,
            'name_en' => $nameEn,
            'display_name' => $displayName,
            'address_array' => $addressArray,
            'address_text' => $addressText,
        ];
    }

    protected function resolveBreederNames(PrevDog $dog): array
    {
        if ($dog->relationLoaded('breedinghouse') && $dog->breedinghouse) {
            $breedingHouse = $dog->breedinghouse;

            if (!$breedingHouse->relationLoaded('users')) {
                $breedingHouse->loadMissing([
                    'users' => fn(BelongsToMany $query) => $query->select($this->ownerSelectColumns()),
                ]);
            }

            return $breedingHouse->users
                ->map(fn(PrevUser $user) => $this->normalizeUser($user)['display_name'])
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        $dog->loadMissing([
            'mother.owners' => fn(BelongsToMany $query) => $query->select($this->ownerSelectColumns()),
        ]);

        $motherFirstOwner = $dog->mother?->owners?->first();

        if (!$motherFirstOwner instanceof PrevUser) {
            return [];
        }

        $displayName = $this->normalizeUser($motherFirstOwner)['display_name'];

        return filled($displayName) ? [$displayName] : [];
    }

    protected function ownerSelectColumns(): array
    {
        return [
            'users.id',
            'users.first_name',
            'users.last_name',
            'users.first_name_en',
            'users.last_name_en',
            'users.address_city',
            'users.address_city_en',
            'users.address_street',
            'users.address_street_en',
            'users.address_street_number',
            'users.house_number',
            'users.address_zip',
        ];
    }

    protected function resolveBreedingHouseName(PrevDog $dog): ?string
    {
        $house = $dog->breedinghouse;

        return $this->blankToNull(
            $house?->name
            ?? $house?->HebName
            ?? $house?->EngName,
        );
    }

    protected function normalizeGenderValue(mixed $gender): int|string|null
    {
        if ($gender instanceof LegacyDogGender) {
            return $gender->value;
        }

        return $gender;
    }

    protected function standardGenderLabel(mixed $gender): string
    {
        if ($gender instanceof LegacyDogGender) {
            return $gender->getLabel();
        }

        return match ($this->normalizeGenderValue($gender)) {
            1 => __('Male'),
            2 => __('Female'),
            default => __('Unknown'),
        };
    }

    protected function pedigreeGenderLabel(mixed $gender): string
    {
        return match ($this->normalizeGenderValue($gender)) {
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

    protected function joinName(array $parts): ?string
    {
        $parts = array_values(array_filter(array_map(fn($value) => $this->blankToNull($value), $parts)));

        return empty($parts) ? null : implode(' ', $parts);
    }

    protected function joinLocalizedNames(array $names): ?string
    {
        $names = array_values(array_unique(array_filter(array_map(fn($value) => $this->blankToNull($value), $names))));

        return empty($names) ? null : implode(' | ', $names);
    }

    protected function joinAddressParts(array $parts): ?string
    {
        $values = array_values(array_filter(array_map(fn($value) => $this->blankToNull($value), $parts)));

        return empty($values) ? null : implode(', ', $values);
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
