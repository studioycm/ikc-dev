<?php

namespace App\Services\Legacy\Pedigree;

use App\Enums\Legacy\LegacyDogGender;
use App\Models\PrevDog;
use App\Models\PrevUser;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;

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
        int $dogId,
        int $depth = 4,
        string $direction = 'rtl',
        bool $includeNodeTitles = false,
    ): array
    {
        $depth = max(2, min(10, $depth));
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
        int $dogId,
        int $depth,
        bool $includeNodeTitles,
    ): ?PrevDog
    {
        return PrevDog::query()
            ->select($this->rootColumns)
            ->with($this->rootRelations())
            ->with($this->pedigreeRelations($depth, $includeNodeTitles))
            ->find($dogId);
    }

    protected function titleRelationSelect(): array
    {
        return [
            'dogs_titles_db.TitleCode',
            'dogs_titles_db.TitleName',
        ];
    }

    protected function rootRelations(): array
    {
        return [
            'breed:BreedCode,BreedName,BreedNameEN',
            'color:OldCode,ColorNameHE,ColorNameEN',
            'breedinghouse:id,GidulCode,HebName,EngName',
            'owners' => fn(BelongsToMany $query) => $query->select($this->ownerSelectColumns()),
            'breedinghouse.users' => fn(BelongsToMany $query) => $query->select($this->ownerSelectColumns()),
            'mother.owners' => fn(BelongsToMany $query) => $query->select($this->ownerSelectColumns()),
            'titles' => fn(BelongsToMany $query) => $query->select($this->titleRelationSelect()),
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
            $relations['titles'] = fn(BelongsToMany $query) => $query->select($this->titleRelationSelect());
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
        int $remainingDepth,
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
        int $depth,
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
        $owners = $this->normalizeOwners($dog->relationLoaded('owners') ? $dog->owners->all() : []);
        $localizedNames = $this->localizedPair($dog->Heb_Name, $dog->Eng_Name);
        $breederNames = $this->resolveBreederNames($dog);

        return array_merge(
            $this->normalizeCommonDogData($dog),
            [
                'name_primary' => $localizedNames['primary'],
                'name_secondary' => $localizedNames['secondary'],
                'chip' => $this->blankToNull($dog->getAttribute('Chip') ?? $dog->getAttribute('chip')),
                'reg_date' => $dog->RegDate?->format('d/m/Y'),
                'pedigree_notes' => $this->blankToNull($dog->getAttribute('PedigreeNotes')),
                'gender_label_raw' => $this->standardGenderLabel($dog->GenderID),

                'owners' => $owners,
                'owner_names_text' => collect($owners)->pluck('display_name')->filter()->implode(', '),
                'owner_address_display' => $this->resolveOwnerAddressDisplay($owners),

                'breeder_names' => $breederNames,
                'breeder_text' => !empty($breederNames) ? implode(', ', $breederNames) : null,

                'titles' => $titles,
                'titles_count' => count($titles),
                'titles_text' => implode(' • ', $titles),
            ],
        );
    }

    protected function normalizeNodeDog(PrevDog $dog): array
    {
        $titles = $this->normalizeTitles($dog);
        $titlesText = implode(', ', $titles);
        $localizedNames = $this->localizedPair($dog->Heb_Name, $dog->Eng_Name);

        return array_merge(
            $this->normalizeCommonDogData($dog),
            [
                'name_primary' => $localizedNames['primary'],
                'name_secondary' => $localizedNames['secondary'],
                'gender_label' => $this->pedigreeGenderLabel($dog->GenderID),
                'titles' => $titles,
                'titles_count' => count($titles),
                'titles_text' => $titlesText,
                'titles_has_popup' => (count($titles) > 10 || mb_strlen($titlesText) > 180),
            ],
        );
    }

    protected function normalizeCommonDogData(PrevDog $dog): array
    {
        $breedNames = $this->localizedPair(
            $dog->relationLoaded('breed') ? $dog->breed?->BreedName : null,
            $dog->relationLoaded('breed') ? $dog->breed?->BreedNameEN : null,
        );

        $colorNames = $this->localizedPair(
            $dog->relationLoaded('color') ? $dog->color?->ColorNameHE : null,
            $dog->relationLoaded('color') ? $dog->color?->ColorNameEN : null,
        );

        return [
            'id' => $dog->getKey(),
            'sagir_id' => $dog->SagirID,
            'import_number' => $this->blankToNull($dog->ImportNumber),
            'name_he' => $this->blankToNull($dog->Heb_Name),
            'name_en' => $this->blankToNull($dog->Eng_Name),
            'full_name' => $dog->full_name,
            'breed_name' => $breedNames['primary'],
            'breed_name_secondary' => $breedNames['secondary'],
            'breeding_house' => $dog->relationLoaded('breedinghouse')
                ? $this->resolveBreedingHouseName($dog)
                : null,
            'color_name' => $colorNames['primary'],
            'color_name_secondary' => $colorNames['secondary'],
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
            'name_primary' => null,
            'name_secondary' => null,
            'full_name' => '—',
            'breed_name' => null,
            'breed_name_secondary' => null,
            'breeding_house' => null,
            'color_name' => null,
            'color_name_secondary' => null,
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

    protected function normalizeOwners(array $owners): array
    {
        return collect($owners)
            ->filter(fn($owner) => $owner instanceof PrevUser)
            ->map(fn(PrevUser $owner) => $this->normalizeUser($owner))
            ->filter(fn(array $owner) => filled($owner['display_name']))
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
        $localizedNames = $this->localizedPair($nameHe, $nameEn);

        $addressArray = method_exists($user, 'addressArray')
            ? $user->addressArray()
            : ((array)($user->address ?? []));

        $addressText = method_exists($user, 'buildAddress')
            ? $this->blankToNull($user->buildAddress())
            : $this->joinAddressParts($addressArray);

        return [
            'id' => $user->getKey(),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'first_name_en' => $firstNameEn,
            'last_name_en' => $lastNameEn,
            'name_he' => $nameHe,
            'name_en' => $nameEn,
            'display_name' => $localizedNames['primary'],
            'display_name_secondary' => $localizedNames['secondary'],
            'address_array' => $addressArray,
            'address_text' => $addressText,
        ];
    }

    protected function resolveOwnerAddressDisplay(array $owners): ?string
    {
        foreach ($owners as $owner) {
            if (filled($owner['display_name'] ?? null) && filled($owner['address_text'] ?? null)) {
                return "{$owner['display_name']}: {$owner['address_text']}";
            }
        }

        return null;
    }

    protected function resolveBreederNames(PrevDog $dog): array
    {
        if ($dog->relationLoaded('breedinghouse') && $dog->breedinghouse) {
            $users = $dog->breedinghouse->relationLoaded('users')
                ? $dog->breedinghouse->users
                : collect();

            $names = $users
                ->map(fn(PrevUser $user) => $this->normalizeUser($user)['display_name'])
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (!empty($names)) {
                return $names;
            }
        }

        $motherFirstOwner = $dog->relationLoaded('mother')
            ? $dog->mother?->owners?->first()
            : null;

        if (!$motherFirstOwner instanceof PrevUser) {
            return [];
        }

        $name = $this->normalizeUser($motherFirstOwner)['display_name'];

        return filled($name) ? [$name] : [];
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

        return $this->localizedValue(
            $house?->HebName,
            $house?->EngName,
        );
    }

    protected function localizedPair(mixed $he, mixed $en): array
    {
        $he = $this->blankToNull($he);
        $en = $this->blankToNull($en);

        $isRtl = $this->isRtlLocale();

        if ($isRtl) {
            $primary = $he ?: $en;
            $secondary = $primary === $he ? $en : $he;
        } else {
            $primary = $en ?: $he;
            $secondary = $primary === $en ? $he : $en;
        }

        return [
            'primary' => $primary,
            'secondary' => $secondary ?: null,
        ];
    }

    protected function localizedValue(mixed $he, mixed $en): ?string
    {
        return $this->localizedPair($he, $en)['primary'];
    }

    protected function isRtlLocale(): bool
    {
        $locale = App::currentLocale();

        if (!filled($locale)) {
            return true;
        }

        return str_starts_with($locale, 'he') || str_starts_with($locale, 'ar');
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
