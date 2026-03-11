<?php

namespace App\Services\Legacy\Breeding;

use App\Models\PrevDog;
use App\Services\Legacy\LegacyMembershipResolverService;

class BreedingDogCheckService
{
    protected array $dogCache = [];

    public function __construct(
        protected readonly LegacyMembershipResolverService $membershipResolver,
    )
    {
    }

    public function buildBySagirId(?string $sagirId, string $role = 'female', array $overrides = []): ?array
    {
        if (!filled($sagirId)) {
            return null;
        }

        $dog = $this->resolveDog($sagirId);

        if (!$dog) {
            return null;
        }

        return $this->buildForDog($dog, $role, $overrides);
    }

    public function buildForDog(PrevDog $dog, string $role = 'female', array $overrides = []): array
    {
        $config = $this->resolveRoleConfig($role, $overrides);

        $checks = collect($config['checks'])
            ->map(fn(string $checkKey) => $this->buildCheck($dog, $role, $checkKey, $config))
            ->filter()
            ->values()
            ->all();

        return [
            'dog' => [
                'id' => $dog->id,
                'sagir_id' => $dog->SagirID,
                'name' => $dog->full_name,
                'breed' => $dog->breed?->BreedName,
                'gender' => $dog->GenderID?->getLabel(),
                'age_years' => $dog->age_years,
                'dna_id' => $dog->DnaID,
                'breeding_count' => $dog->breedingCount($role),
            ],
            'checks' => $checks,
            'summary' => [
                'blocking' => collect($checks)->contains(fn(array $row) => $row['state'] === 'absolute_no'),
                'needs_review' => collect($checks)->contains(fn(array $row) => $row['state'] === 'check_needed'),
            ],
        ];
    }

    protected function buildCheck(PrevDog $dog, string $role, string $checkKey, array $config): ?array
    {
        return match ($checkKey) {
            'breeding_approval' => $this->breedingApprovalCheck($dog),
            'age' => $this->ageCheck($dog, $config['min_age_months'] ?? 12),
            'dna' => $this->dnaCheck($dog),
            'red_pedigree' => $this->redPedigreeCheck($dog),
            'breeding_count' => $this->breedingCountCheck($dog, $role, $config['max_breedings'] ?? null),
            'last_breeding_date' => $this->lastBreedingDateCheck($dog),
            default => null,
        };
    }

    protected function breedingApprovalCheck(PrevDog $dog): array
    {
        $resolved = $dog->breedingApprovalResolved();

        $state = match ($resolved) {
            true => 'absolute_yes',
            false => 'absolute_no',
            null => 'check_needed',
        };

        return $this->makeRow(
            key: 'breeding_approval',
            label: __('Breeding approval'),
            state: $state,
            value: $resolved,
        );
    }

    protected function ageCheck(PrevDog $dog, int $minAgeMonths): array
    {
        $ageMonths = $dog->ageInMonths();

        $state = match (true) {
            $ageMonths === null => 'check_needed',
            $ageMonths < $minAgeMonths => 'absolute_no',
            default => 'absolute_yes',
        };

        return $this->makeRow(
            key: 'age',
            label: __('Age'),
            state: $state,
            value: $dog->age_years,
        );
    }

    protected function dnaCheck(PrevDog $dog): array
    {
        $state = $dog->hasDnaRecord() ? 'absolute_yes' : 'check_needed';

        return $this->makeRow(
            key: 'dna',
            label: __('DNA record'),
            state: $state,
            value: $dog->DnaID,
            actions: $this->resolveActionsFor('dna', $state),
        );
    }

    protected function redPedigreeCheck(PrevDog $dog): array
    {
        $state = match (true) {
            $dog->RedPedigree === null => 'check_needed',
            (bool)$dog->RedPedigree => 'absolute_yes',
            default => 'absolute_no',
        };

        return $this->makeRow(
            key: 'red_pedigree',
            label: __('Red pedigree'),
            state: $state,
            value: $dog->RedPedigree,
        );
    }

    protected function breedingCountCheck(PrevDog $dog, string $role, ?int $maxBreedings): array
    {
        $count = $dog->breedingCount($role);

        $state = match (true) {
            $count === null => 'check_needed',
            $maxBreedings !== null && $count > $maxBreedings => 'absolute_no',
            default => 'absolute_yes',
        };

        return $this->makeRow(
            key: 'breeding_count',
            label: __('Breeding count'),
            state: $state,
            value: $count,
        );
    }

    protected function lastBreedingDateCheck(PrevDog $dog): array
    {
        return $this->makeRow(
            key: 'last_breeding_date',
            label: __('Last breeding / litter date'),
            state: $dog->last_breeding_date ? 'absolute_yes' : 'check_needed',
            value: $dog->last_breeding_date,
        );
    }

    protected function makeRow(
        string $key,
        string $label,
        string $state,
        mixed  $value = null,
        array  $actions = [],
    ): array
    {
        $stateConfig = config("breeding_checks.states.{$state}");

        return [
            'key' => $key,
            'label' => $label,
            'state' => $state,
            'state_label' => __($stateConfig['label'] ?? $state),
            'icon' => $stateConfig['icon'] ?? 'heroicon-m-information-circle',
            'color' => $stateConfig['color'] ?? 'gray',
            'value' => $value,
            'actions' => $actions,
        ];
    }

    protected function resolveActionsFor(string $checkKey, string $state): array
    {
        return collect(config('breeding_checks.actions', []))
            ->filter(function (array $actionConfig) use ($checkKey, $state) {
                return ($actionConfig['visible_when'][$checkKey] ?? null) === $state;
            })
            ->map(function (array $actionConfig, string $actionKey) {
                return [
                    'key' => $actionKey,
                    'label' => __($actionConfig['label']),
                    'icon' => $actionConfig['icon'],
                    'color' => $actionConfig['color'],
                    'modal_heading' => __($actionConfig['modal_heading']),
                    'modal_description' => __($actionConfig['modal_description']),
                ];
            })
            ->values()
            ->all();
    }

    protected function resolveDog(string $sagirId): ?PrevDog
    {
        if (array_key_exists($sagirId, $this->dogCache)) {
            return $this->dogCache[$sagirId];
        }

        return $this->dogCache[$sagirId] = PrevDog::query()
            ->with(['breed'])
            ->withCount(['femaleBreedings', 'maleBreedings'])
            ->where('SagirID', $sagirId)
            ->first();
    }

    protected function resolveRoleConfig(string $role, array $overrides): array
    {
        return array_replace_recursive(
            config("breeding_checks.rules.{$role}", config('breeding_checks.rules.female')),
            $overrides,
        );
    }
}
