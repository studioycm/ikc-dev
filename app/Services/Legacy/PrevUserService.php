<?php

declare(strict_types=1);

namespace App\Services\Legacy;

use App\Builders\PrevUserBuilder;
use App\Models\PrevDog;
use App\Models\PrevUser;
use App\Models\PrevUserRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PrevUserService
{
    public function query(): PrevUserBuilder
    {
        /** @var PrevUserBuilder $query */
        $query = PrevUser::query();

        return $query;
    }

    public function resolve(
        string|int|null $mobilePhone = null,
        string|int|null $phone = null,
        ?string         $email = null,
        int|string|null $ownerCode = null,
        ?string         $recordType = null,
    ): ?PrevUser
    {
        return $this->resolveOrderedIdentity(
            mobilePhone: $mobilePhone,
            phone: $phone,
            email: $email,
            ownerCode: $ownerCode,
            recordType: $recordType,
        );
    }

    public function resolveFromRequestIdentity(
        string|int|null $mobilePhone = null,
        ?string         $email = null,
        ?string         $recordType = null,
    ): ?PrevUser
    {
        return $this->resolveOrderedIdentity(
            mobilePhone: $mobilePhone,
            email: $email,
            recordType: $recordType,
        );
    }

    public function resolveByNormalisedMobilePhone(string|int|null $mobilePhone): ?PrevUser
    {
        return $this->query()
            ->whereNormalisedMobilePhone($mobilePhone)
            ->orderByResolutionPriority()
            ->first();
    }

    public function resolveByEmail(?string $email): ?PrevUser
    {
        return $this->query()
            ->whereEmail($email)
            ->orderByResolutionPriority()
            ->first();
    }

    public function resolveByOwnerCode(int|string|null $ownerCode): ?PrevUser
    {
        return $this->query()
            ->whereOwnerCode($ownerCode)
            ->orderByResolutionPriority()
            ->first();
    }

    public function resolveFromDog(PrevDog $dog, ?string $recordType = null): ?PrevUser
    {
        $owner = $this->pickBestCandidate($dog->owners()->get(), $recordType);

        if ($owner !== null) {
            return $this->resolvePreferredFromCandidate($owner, $recordType) ?? $owner;
        }

        return $this->resolve(
            ownerCode: $dog->CurrentOwnerId ?? null,
            recordType: $recordType,
        );
    }

    public function resolveFromRequest(PrevUserRequest $request): ?PrevUser
    {
        $directOwner = $request->owner;

        if ($directOwner !== null) {
            return $this->resolvePreferredFromCandidate($directOwner) ?? $directOwner;
        }

        if ($request->relationLoaded('dog') || filled($request->sagirID)) {
            $request->loadMissing('dog.owners', 'dog.legacyOwner');

            $dogOwner = $this->resolveRequestDogOwner($request);

            if ($dogOwner !== null) {
                return $dogOwner;
            }
        }

        return $this->resolveFromRequestIdentity(
            mobilePhone: $request->mobile_phone,
            email: $request->email,
        );
    }

    public function constrainRequestQueryToPrevUser(Builder $query, ?PrevUser $prevUser): Builder
    {
        if ($prevUser === null) {
            return $query->whereRaw('1 = 0');
        }

        $prevUser->loadMissing('dogs', 'history_dogs');

        $dogSagirIds = $prevUser->dogs
            ->pluck('SagirID')
            ->merge($prevUser->history_dogs->pluck('SagirID'))
            ->filter()
            ->unique()
            ->values()
            ->all();

        return $this->constrainRequestQuery(
            $query,
            mobilePhone: $prevUser->mobile_phone,
            phone: $prevUser->phone,
            email: $prevUser->email,
            ownerId: $prevUser->getKey(),
            dogSagirIds: $dogSagirIds,
        );
    }

    public function constrainRequestQuery(
        Builder         $query,
        string|int|null $mobilePhone = null,
        string|int|null $phone = null,
        ?string         $email = null,
        ?int            $ownerId = null,
        array           $dogSagirIds = [],
    ): Builder
    {
        $normalisedMobilePhone = self::normalisePhone($mobilePhone);
        $normalisedPhone = self::normalisePhone($phone);
        $normalisedEmail = self::normaliseEmail($email);
        $hasOwnerId = filled($ownerId);
        $dogSagirIds = array_values(array_filter($dogSagirIds, static fn(mixed $dogSagirId): bool => filled($dogSagirId)));
        $hasDogIds = $dogSagirIds !== [];

        if (!$hasOwnerId && !$hasDogIds && $normalisedMobilePhone === null && $normalisedPhone === null && $normalisedEmail === null) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $nestedQuery) use ($dogSagirIds, $hasDogIds, $hasOwnerId, $ownerId, $normalisedEmail, $normalisedMobilePhone, $normalisedPhone): void {
            $hasConstraint = false;

            if ($hasOwnerId) {
                $nestedQuery->where('owner_id', $ownerId);
                $hasConstraint = true;
            }

            if ($hasDogIds) {
                $method = $hasConstraint ? 'orWhereIn' : 'whereIn';
                $nestedQuery->{$method}('sagirID', $dogSagirIds);
                $hasConstraint = true;
            }

            if ($normalisedMobilePhone !== null) {
                $method = $hasConstraint ? 'orWhereRaw' : 'whereRaw';
                $nestedQuery->{$method}(self::normalisedIsraeliMobileSql('mobile_phone') . ' = ?', [$normalisedMobilePhone]);
                $hasConstraint = true;
            }

            if ($normalisedPhone !== null) {
                $method = $hasConstraint ? 'orWhereRaw' : 'whereRaw';
                $nestedQuery->{$method}(self::normalisedIsraeliMobileSql('mobile_phone') . ' = ?', [$normalisedPhone]);
                $hasConstraint = true;
            }

            if ($normalisedEmail !== null) {
                $method = $hasConstraint ? 'orWhere' : 'where';
                $nestedQuery->{$method}(function (Builder $emailQuery) use ($normalisedEmail, $normalisedMobilePhone, $normalisedPhone): void {
                    $emailQuery->whereRaw('LOWER(TRIM(email)) = ?', [$normalisedEmail]);

                    if ($normalisedMobilePhone === null && $normalisedPhone === null) {
                        return;
                    }

                    $normalisedRequestPhoneSql = self::normalisedIsraeliMobileSql('mobile_phone');

                    $emailQuery->where(function (Builder $phoneFallbackQuery) use ($normalisedMobilePhone, $normalisedPhone, $normalisedRequestPhoneSql): void {
                        $phoneFallbackQuery->whereRaw("COALESCE({$normalisedRequestPhoneSql}, '') = ''")
                            ->orWhere(function (Builder $nonMatchingPhoneQuery) use ($normalisedMobilePhone, $normalisedPhone, $normalisedRequestPhoneSql): void {
                                if ($normalisedMobilePhone !== null) {
                                    $nonMatchingPhoneQuery->whereRaw("COALESCE({$normalisedRequestPhoneSql}, '') <> ?", [$normalisedMobilePhone]);
                                }

                                if ($normalisedPhone !== null) {
                                    $nonMatchingPhoneQuery->whereRaw("COALESCE({$normalisedRequestPhoneSql}, '') <> ?", [$normalisedPhone]);
                                }
                            });
                    });
                });
            }
        });
    }

    public static function normalisePhone(string|int|null $raw): ?string
    {
        if ($raw === null) {
            return null;
        }

        $raw = trim((string)$raw);

        if ($raw === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $raw);

        if ($digits === null || $digits === '') {
            return null;
        }

        $digits = preg_replace('/^(00972|972)/', '', $digits) ?? $digits;
        $digits = ltrim($digits, '0');
        $digits = $digits === '' ? '' : '0' . $digits;

        return preg_match('/^05\d{8}$/', $digits) === 1 ? $digits : null;
    }

    public static function normaliseEmail(?string $email): ?string
    {
        if ($email === null) {
            return null;
        }

        $normalisedEmail = mb_strtolower(trim($email));

        return $normalisedEmail === '' ? null : $normalisedEmail;
    }

    public static function normalisedIsraeliMobileSql(string $column): string
    {
        $digitsOnly = "REGEXP_REPLACE(COALESCE({$column}, ''), '[^0-9]', '')";
        $withoutCountryCode = "REGEXP_REPLACE({$digitsOnly}, '^(00972|972)', '')";
        $withoutLeadingZeroes = "TRIM(LEADING '0' FROM {$withoutCountryCode})";
        $normalised = "CONCAT('0', {$withoutLeadingZeroes})";

        return "CASE WHEN {$normalised} REGEXP '^05[0-9]{8}$' THEN {$normalised} ELSE NULL END";
    }

    protected function resolveRequestDogOwner(PrevUserRequest $request): ?PrevUser
    {
        $dog = $request->dog;

        if ($dog === null) {
            return null;
        }

        $owner = $this->pickBestCandidate($dog->owners);

        if ($owner !== null) {
            return $this->resolvePreferredFromCandidate($owner) ?? $owner;
        }

        $legacyOwner = $dog->legacyOwner;

        if ($legacyOwner === null) {
            return null;
        }

        return $this->resolvePreferredFromCandidate($legacyOwner) ?? $legacyOwner;
    }

    protected function pickBestCandidate(Collection $candidates, ?string $recordType = null): ?PrevUser
    {
        if (filled($recordType)) {
            $candidates = $candidates
                ->filter(fn(PrevUser $prevUser): bool => $prevUser->record_type === $recordType)
                ->values();
        }

        /** @var PrevUser|null $candidate */
        $candidate = $candidates
            ->sortBy([
                fn(PrevUser $prevUser): int => $this->resolutionPriority($prevUser),
                fn(PrevUser $prevUser): int => (int)$prevUser->getKey(),
            ])
            ->first();

        return $candidate;
    }

    protected function resolutionPriority(PrevUser $prevUser): int
    {
        return match ($prevUser->record_type) {
            'Native' => 0,
            'Owners' => 1,
            'Members' => 2,
            default => 3,
        };
    }

    protected function resolvePreferredFromCandidate(PrevUser $candidate, ?string $recordType = null): ?PrevUser
    {
        $resolved = $this->resolve(
            mobilePhone: $candidate->mobile_phone,
            phone: $candidate->phone,
            email: $candidate->email,
            ownerCode: $candidate->owner_code,
            recordType: $recordType,
        );

        if ($resolved !== null) {
            return $resolved;
        }

        if (!filled($recordType) || $candidate->record_type === $recordType) {
            return $candidate;
        }

        return null;
    }

    protected function resolveOrderedIdentity(
        string|int|null $mobilePhone = null,
        string|int|null $phone = null,
        ?string         $email = null,
        int|string|null $ownerCode = null,
        ?string         $recordType = null,
    ): ?PrevUser
    {
        $query = $this->query()
            ->whereResolvesIdentity(
                mobilePhone: $mobilePhone,
                phone: $phone,
                email: $email,
                ownerCode: $ownerCode,
            );

        if (filled($recordType)) {
            $query->whereRecordType($recordType);
        }

        return $query
            ->orderByIdentityResolution(
                mobilePhone: $mobilePhone,
                phone: $phone,
                email: $email,
                ownerCode: $ownerCode,
            )
            ->orderByResolutionPriority()
            ->first();
    }

    protected function resolveUsingBuilder(
        \Closure $scope,
        ?string  $recordType = null,
    ): ?PrevUser
    {
        $query = $scope($this->query());

        if (filled($recordType)) {
            $query->whereRecordType($recordType);
        }

        return $query
            ->orderByResolutionPriority()
            ->first();
    }
}
