<?php

namespace Tests\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\DB;

trait StartsChecklistTypes
{
    protected function startChecklistType(
        User $user,
        string $typeCode,
        ?User $starter = null,
        ?string $startedAt = null,
    ): void {
        $starter ??= $user;

        DB::table('user_checklist_type_starts')->insert([
            'user_id' => $user->id,
            'checklist_type_id' => DB::table('checklist_types')->where('code', $typeCode)->value('id'),
            'started_at' => $startedAt ?? now()->toDateString(),
            'started_by' => $starter->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function startMemberChecklistTypes(User $user, ?User $starter = null): void
    {
        foreach (['onboarding', 'licensing', 'fap', 'cfm-training'] as $typeCode) {
            $this->startChecklistType($user, $typeCode, $starter);
        }
    }
}
