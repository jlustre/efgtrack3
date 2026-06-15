<?php

namespace App\Services;

<<<<<<< HEAD
use App\Models\User;
use Illuminate\Support\Facades\DB;
=======
use App\Models\ProfileCompletionField;
use App\Models\User;
>>>>>>> 2ae99211b388cde4b56062c1cfbbc9ca81c523b0

class ProfileCompletionService
{
    /**
<<<<<<< HEAD
     * @return array{percent: int, filled: int, total: int, complete: bool}
     */
    public function snapshot(User $user): array
    {
        $percent = $this->percent($user);

        return [
            'percent' => $percent,
            'filled' => $this->filledCount($user),
            'total' => $this->activeFieldCount(),
            'complete' => $percent >= 100,
        ];
    }

    public function percent(User $user): int
    {
        $total = $this->activeFieldCount();

        if ($total === 0) {
            return 0;
        }

        return (int) round(($this->filledCount($user) / $total) * 100);
    }

    public function isComplete(User $user): bool
    {
        return $this->percent($user) >= 100;
    }

    private function activeFieldCount(): int
    {
        return (int) DB::table('profile_completion_fields')
            ->where('is_active', true)
            ->count();
    }

    private function filledCount(User $user): int
=======
     * @return list<array{key: string, label: string, filled: bool}>
     */
    public function fields(User $user): array
>>>>>>> 2ae99211b388cde4b56062c1cfbbc9ca81c523b0
    {
        $user->loadMissing('profile');
        $profile = $user->profile;

<<<<<<< HEAD
        return DB::table('profile_completion_fields')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['field_key'])
            ->filter(fn (object $field): bool => $this->fieldIsFilled($profile, $field->field_key))
            ->count();
    }

    private function fieldIsFilled(?object $profile, string $fieldKey): bool
    {
        if (! $profile) {
            return false;
        }

        $value = $profile->{$fieldKey} ?? null;

        if (is_bool($value)) {
            return $value;
        }

        return filled($value);
=======
        return ProfileCompletionField::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get()
            ->map(function (ProfileCompletionField $field) use ($user, $profile): array {
                $value = $field->source === 'user'
                    ? $user->getAttribute($field->field_key)
                    : $this->profileFieldValue($profile, $field->field_key);

                return [
                    'key' => $field->field_key,
                    'label' => $field->label,
                    'filled' => filled($value),
                ];
            })
            ->values()
            ->all();
    }

    public function percent(User $user): int
    {
        $fields = $this->fields($user);

        if ($fields === []) {
            return 100;
        }

        $filled = collect($fields)->where('filled', true)->count();

        return (int) round(($filled / count($fields)) * 100);
    }

    public function isComplete(User $user): bool
    {
        $fields = $this->fields($user);

        if ($fields === []) {
            return true;
        }

        return collect($fields)->every(fn (array $field): bool => $field['filled']);
    }

    /**
     * @return array{
     *     percent: int,
     *     is_complete: bool,
     *     fields: list<array{key: string, label: string, filled: bool}>
     * }
     */
    public function snapshot(User $user): array
    {
        $fields = $this->fields($user);

        return [
            'percent' => $this->percent($user),
            'is_complete' => $fields === [] || collect($fields)->every(fn (array $field): bool => $field['filled']),
            'fields' => $fields,
        ];
    }

    private function profileFieldValue(?\App\Models\Profile $profile, string $fieldKey): mixed
    {
        if ($profile === null) {
            return null;
        }

        $attribute = match ($fieldKey) {
            'country' => 'country_id',
            'province' => 'state_province_id',
            'timezone' => 'timezone_id',
            default => $fieldKey,
        };

        return $profile->getAttribute($attribute);
>>>>>>> 2ae99211b388cde4b56062c1cfbbc9ca81c523b0
    }
}
