<?php

namespace App\Services;

use App\Models\CfmMentorProfile;
use App\Models\User;
use App\Support\LocationOptions;
use Illuminate\Support\Facades\DB;

class CfmPortalService
{
    public function __construct(private readonly CfmManagementService $cfmManagement) {}

    public function payloadFor(User $viewer, ?int $cfmUserId = null): array
    {
        $cfmUser = $this->resolveCfmUser($viewer, $cfmUserId);
        $profile = $this->cfmManagement->profileFor($viewer, $cfmUser);
        $training = $this->trainingProgressFor($cfmUser);
        $rankStructure = $this->cfmManagement->rankStructureFor();

        return [
            'cfmUser' => $cfmUser,
            'profile' => $profile,
            'training' => $training,
            'achievements' => $this->achievementsFor($profile, $training),
            'rankTiers' => $rankStructure['tiers'],
            'advancementGuideline' => $rankStructure['guideline'],
            'isAdminView' => $viewer->hasAnyRole(['super-admin', 'admin']) && $viewer->id !== $cfmUser->id,
            'canEditProfile' => $viewer->id === $cfmUser->id && $viewer->hasRole('certified-field-mentor'),
            'editForm' => $this->editFormFor($cfmUser, $profile),
            'locationOptions' => LocationOptions::forPortal(),
            'cfmOptions' => $this->cfmOptionsFor($viewer),
            'selectedCfmId' => $cfmUser->id,
        ];
    }

    public function updateProfile(User $cfm, array $data): void
    {
        if (! $cfm->hasRole('certified-field-mentor')) {
            abort(403);
        }

        DB::transaction(function () use ($cfm, $data): void {
            $cfm->profile()->updateOrCreate(
                ['user_id' => $cfm->id],
                [
                    'phone' => $data['phone'] ?? null,
                    'city' => $data['city'] ?? null,
                    'province' => $data['province'] ?? null,
                    'country' => $data['country'] ?? null,
                    'timezone' => $data['timezone'] ?? null,
                ]
            );

            $mentorProfile = CfmMentorProfile::firstOrCreate(
                ['user_id' => $cfm->id],
                [
                    'certification_status' => 'certified',
                    'hierarchy_access' => 'my_hierarchy',
                    'max_apprentices' => 6,
                    'fap_completion_rate' => 0,
                    'calendar_busyness_percent' => 0,
                    'avg_apprentice_progress' => 0,
                    'recommendation_score' => 75,
                    'languages' => ['English'],
                    'specialties' => ['Field Apprenticeship'],
                ]
            );

            $mentorProfile->update([
                'mentor_bio' => $data['mentor_bio'] ?? null,
                'languages' => $this->parseList($data['languages'] ?? ''),
                'specialties' => $this->parseList($data['specialties'] ?? ''),
                'licensed_jurisdictions' => $data['licensed_jurisdictions'] ?? [],
                'manual_unavailable' => (bool) ($data['manual_unavailable'] ?? false),
                'last_mentor_activity_at' => now(),
            ]);
        });
    }

    private function editFormFor(User $cfmUser, array $profile): array
    {
        return [
            'phone' => old('phone', $cfmUser->profile?->phone ?? ''),
            'city' => old('city', $cfmUser->profile?->city ?? ''),
            'province' => old('province', $cfmUser->profile?->province ?? ''),
            'country' => old('country', $cfmUser->profile?->country ?? ''),
            'timezone' => old('timezone', $cfmUser->profile?->timezone ?? ''),
            'mentor_bio' => old('mentor_bio', $profile['bio'] ?? ''),
            'languages' => old('languages', implode(', ', $profile['languages'] ?? [])),
            'specialties' => old('specialties', implode(', ', $profile['specialties'] ?? [])),
            'manual_unavailable' => old('manual_unavailable', $cfmUser->cfmMentorProfile?->manual_unavailable ?? false),
            'licensed_jurisdictions' => old(
                'licensed_jurisdictions',
                $cfmUser->cfmMentorProfile?->licensed_jurisdictions ?? []
            ),
        ];
    }

    private function parseList(string $value): array
    {
        if (trim($value) === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $value))));
    }

    private function resolveCfmUser(User $viewer, ?int $cfmUserId): User
    {
        if ($viewer->hasAnyRole(['super-admin', 'admin'])) {
            if ($cfmUserId) {
                return User::query()
                    ->role('certified-field-mentor')
                    ->where('is_active', true)
                    ->whereKey($cfmUserId)
                    ->firstOrFail();
            }

            if ($viewer->hasRole('certified-field-mentor')) {
                return $viewer->loadMissing(['profile', 'rank', 'team', 'sponsor', 'cfmMentorProfile']);
            }

            return User::query()
                ->role('certified-field-mentor')
                ->where('is_active', true)
                ->orderBy('name')
                ->with(['profile', 'rank', 'team', 'sponsor', 'cfmMentorProfile'])
                ->firstOrFail();
        }

        if (! $viewer->hasRole('certified-field-mentor')) {
            abort(403);
        }

        return $viewer->loadMissing(['profile', 'rank', 'team', 'sponsor', 'cfmMentorProfile']);
    }

    private function cfmOptionsFor(User $viewer): array
    {
        if (! $viewer->hasAnyRole(['super-admin', 'admin'])) {
            return [];
        }

        return User::query()
            ->role('certified-field-mentor')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (User $user) => ['id' => $user->id, 'name' => $user->name])
            ->values()
            ->all();
    }

    private function trainingProgressFor(User $cfmUser): array
    {
        $steps = DB::table('cfm_training_modules')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'title', 'is_required']);

        if ($steps->isEmpty()) {
            return [
                'total' => 0,
                'completed' => 0,
                'pending' => 0,
                'percent' => 0,
                'requiredTotal' => 0,
                'requiredCompleted' => 0,
                'requiredPercent' => 0,
                'modules' => [],
            ];
        }

        $progress = DB::table('cfm_training_progress')
            ->where('user_id', $cfmUser->id)
            ->whereIn('cfm_training_module_id', $steps->pluck('id'))
            ->get()
            ->keyBy('cfm_training_module_id');

        $modules = $steps->map(function (object $step) use ($progress): array {
            $row = $progress->get($step->id);
            $status = $row->status ?? 'not_started';

            return [
                'title' => $step->title,
                'status' => str($status)->replace('_', ' ')->title()->toString(),
                'isRequired' => (bool) $step->is_required,
                'isCompleted' => $status === 'completed',
                'isPending' => $status === 'pending_confirmation',
            ];
        })->values()->all();

        $total = count($modules);
        $completed = collect($modules)->where('isCompleted', true)->count();
        $pending = collect($modules)->where('isPending', true)->count();
        $requiredTotal = collect($modules)->where('isRequired', true)->count();
        $requiredCompleted = collect($modules)->where('isRequired', true)->where('isCompleted', true)->count();

        return [
            'total' => $total,
            'completed' => $completed,
            'pending' => $pending,
            'percent' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
            'requiredTotal' => $requiredTotal,
            'requiredCompleted' => $requiredCompleted,
            'requiredPercent' => $requiredTotal > 0 ? (int) round(($requiredCompleted / $requiredTotal) * 100) : 0,
            'modules' => $modules,
        ];
    }

    private function achievementsFor(array $profile, array $training): array
    {
        $achievements = [];

        if (($profile['completedApprentices'] ?? 0) >= 1) {
            $achievements[] = [
                'title' => 'First Graduate',
                'description' => 'Successfully mentored your first apprentice through FAP.',
                'icon' => '🎓',
            ];
        }

        if (($profile['completedApprentices'] ?? 0) >= 5) {
            $achievements[] = [
                'title' => 'Mentor Milestone',
                'description' => 'Five apprentices have completed the Field Apprenticeship Program under your guidance.',
                'icon' => '⭐',
            ];
        }

        if (($profile['fapCompletionRate'] ?? 0) >= 80) {
            $achievements[] = [
                'title' => 'High Completion Rate',
                'description' => 'Maintained an 80% or higher FAP completion rate among your trainees.',
                'icon' => '🏆',
            ];
        }

        if (($training['requiredPercent'] ?? 0) >= 100) {
            $achievements[] = [
                'title' => 'CFM Training Complete',
                'description' => 'All required CFM training modules are complete.',
                'icon' => '✅',
            ];
        }

        if (($profile['recommendationScore'] ?? 0) >= 90) {
            $achievements[] = [
                'title' => 'Top Performer',
                'description' => 'Earned a recommendation score of 90 or higher.',
                'icon' => '💎',
            ];
        }

        if (($profile['activeApprentices'] ?? 0) >= 3 && ($profile['workloadKey'] ?? '') === 'available') {
            $achievements[] = [
                'title' => 'Balanced Mentor',
                'description' => 'Managing multiple apprentices while maintaining availability.',
                'icon' => '⚖️',
            ];
        }

        if ($achievements === []) {
            $achievements[] = [
                'title' => 'CFM Journey Started',
                'description' => 'Your mentorship achievements will appear here as you progress.',
                'icon' => '🌱',
            ];
        }

        return $achievements;
    }
}
