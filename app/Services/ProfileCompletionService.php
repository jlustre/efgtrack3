<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProfileCompletionService
{
    /**
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
    {
        $user->loadMissing('profile');
        $profile = $user->profile;

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
    }
}
