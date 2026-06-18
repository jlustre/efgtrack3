<?php

namespace App\Services;

use App\Models\Checklist;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MemberProfileTabsService
{
    public function __construct(private readonly ChecklistService $checklists) {}

    public function forUser(User $user): array
    {
        $user->loadMissing(['profile', 'rank', 'mentor']);

        $onboarding = $this->onboardingRows($user);
        $fap = $this->checklistRows($user, 'fap', 'Field Apprenticeship Program');
        $cfm = $this->checklistRows($user, 'cfm-training');
        $recruits = $this->recruitRows($user);
        $annualPremium = $this->annualPremiumRows($user, $onboarding, $fap, $cfm);
        $otherTraining = $this->otherTrainingRows($user);

        return [
            'onboarding' => $onboarding,
            'onboarding_started' => $this->checklists->hasTypeStarted($user, 'onboarding'),
            'fap' => $fap,
            'fap_started' => $this->checklists->hasTypeStarted($user, 'fap'),
            'cfm' => $cfm,
            'cfm_started' => $this->checklists->hasTypeStarted($user, 'cfm-training'),
            'recruits' => $recruits,
            'recruitsTotal' => count($recruits),
            'recruitsDirectTotal' => collect($recruits)->where('level', 1)->count(),
            'annualPremium' => $annualPremium,
            'annualPremiumTotal' => $this->sumAnnualPremium($annualPremium),
            'otherTraining' => $otherTraining,
        ];
    }

    public function annualPremiumTotal(User $user): int
    {
        $user->loadMissing(['profile', 'rank', 'mentor']);

        return $this->sumAnnualPremium($this->annualPremiumRows(
            $user,
            $this->onboardingRows($user),
            $this->checklistRows($user, 'fap', 'Field Apprenticeship Program'),
            $this->checklistRows($user, 'cfm-training'),
        ));
    }

    /**
     * @param  list<array{premium_amount: int}>  $rows
     */
    private function sumAnnualPremium(array $rows): int
    {
        return (int) collect($rows)->sum('premium_amount');
    }

    private function onboardingRows(User $user): array
    {
        if (! $this->checklists->hasTypeStarted($user, 'onboarding')) {
            return [];
        }

        $steps = $this->checklists->activeSteps('onboarding', $user->profile?->country);
        $progress = $this->checklists->userProgressFor($user->id, $steps->pluck('id'));

        return $steps
            ->map(fn (Checklist $step) => $this->formatChecklistRow(
                $step->title,
                $step->country ?: 'Global',
                (bool) $step->is_required,
                $progress->get($step->id)
            ))
            ->values()
            ->all();
    }

    private function checklistRows(User $user, string $typeCode, ?string $groupLabel = null): array
    {
        if (! $this->checklists->hasTypeStarted($user, $typeCode)) {
            return [];
        }

        $steps = $this->checklists->activeSteps($typeCode, null, $groupLabel);
        $progress = $this->checklists->userProgressFor($user->id, $steps->pluck('id'));

        return $steps
            ->map(function (Checklist $step) use ($progress): array {
                return $this->formatChecklistRow(
                    $step->title,
                    $step->group_label ?? '—',
                    (bool) $step->is_required,
                    $progress->get($step->id)
                );
            })
            ->values()
            ->all();
    }

    private function formatChecklistRow(string $title, string $category, bool $required, mixed $progress): array
    {
        $status = $progress?->status ?? 'not_started';

        return [
            'item' => $title,
            'category' => $category,
            'required' => $required ? 'Yes' : 'No',
            'status' => str($status)->replace('_', ' ')->title()->toString(),
            'status_key' => $status,
            'submitted_at' => $this->formatDate($progress?->submitted_at ?? null),
            'completed_at' => $this->formatDate($progress?->completed_at ?? null),
        ];
    }

    private function recruitRows(User $user): array
    {
        $hierarchy = app(DownlineHierarchyService::class);

        return $hierarchy->descendantsQuery($user)
            ->addSelect('user_hierarchy_paths.depth as recruit_level')
            ->with(['rank', 'mentor', 'profile', 'sponsor', 'roles'])
            ->orderBy('user_hierarchy_paths.depth')
            ->orderByDesc('users.joined_at')
            ->orderBy('users.name')
            ->get()
            ->map(function (User $recruit) use ($hierarchy): array {
                $progress = $hierarchy->progressSummary($recruit);
                $level = (int) ($recruit->recruit_level ?? 1);

                $country = $recruit->profile?->country;
                $province = $recruit->profile?->province;

                return [
                    'level' => $level,
                    'level_label' => (string) $level,
                    'name' => $recruit->name,
                    'email' => $recruit->email,
                    'profile_photo_url' => $recruit->profilePhotoUrl(),
                    'phone' => $recruit->profile?->phone ?: '—',
                    'role' => $this->recruitRoleLabel($recruit),
                    'rank' => $recruit->rank?->code ?? '—',
                    'status' => $recruit->is_active ? 'Active' : 'Inactive',
                    'status_key' => $recruit->is_active ? 'active' : 'inactive',
                    'sponsor' => $recruit->sponsor?->name ?? '—',
                    'cfm' => $recruit->mentor?->name ?? 'Unassigned',
                    'province' => $province ?: '—',
                    'country' => $country ?: '—',
                    'country_flag' => $this->countryFlag($country),
                    'joined_at' => $recruit->joined_at?->format('M j, Y') ?? '—',
                    'onboarding' => \App\Support\ChecklistProgressDisplay::label($progress['onboarding'] ?? 0),
                    'fap' => \App\Support\ChecklistProgressDisplay::label($progress['apprenticeship'] ?? 0),
                ];
            })
            ->values()
            ->all();
    }

    private function annualPremiumRows(User $user, array $onboarding, array $fap, array $cfm): array
    {
        $rows = collect();
        $year = now()->year;

        foreach ($onboarding as $row) {
            if ($row['status_key'] !== 'completed') {
                continue;
            }

            $amount = $row['required'] === 'Yes' ? 1200 : 600;
            $rows->push($this->formatAnnualPremiumRow($year, 'Onboarding', $row['item'], $amount, $row['completed_at']));
        }

        foreach ($fap as $row) {
            if ($row['status_key'] !== 'completed') {
                continue;
            }

            $amount = $row['required'] === 'Yes' ? 3500 : 1800;
            $rows->push($this->formatAnnualPremiumRow($year, 'Field Apprenticeship', $row['item'], $amount, $row['completed_at']));
        }

        foreach ($cfm as $row) {
            if ($row['status_key'] !== 'completed') {
                continue;
            }

            $amount = $row['required'] === 'Yes' ? 2400 : 1200;
            $rows->push($this->formatAnnualPremiumRow($year, 'CFM Training', $row['item'], $amount, $row['completed_at']));
        }

        User::query()
            ->where('sponsor_id', $user->id)
            ->where('is_active', true)
            ->orderByDesc('joined_at')
            ->get(['id', 'name', 'joined_at'])
            ->each(function (User $recruit) use ($rows, $year): void {
                $rows->push($this->formatAnnualPremiumRow(
                    $year,
                    'Direct Recruit',
                    $recruit->name.' — new team member',
                    5000,
                    $recruit->joined_at?->format('M j, Y') ?? '—'
                ));
            });

        if ($rows->isEmpty()) {
            return [[
                'period' => (string) $year,
                'source' => '—',
                'source_key' => '',
                'description' => 'Annual premium production will appear as you complete milestones and build your team.',
                'annual_premium' => '$0',
                'premium_amount' => 0,
                'status' => '—',
                'status_key' => '',
                'posted_at' => '—',
            ]];
        }

        return $rows
            ->sortByDesc(fn (array $row) => $row['premium_amount'])
            ->values()
            ->all();
    }

    private function formatAnnualPremiumRow(int $year, string $source, string $description, int $amount, string $postedAt): array
    {
        return [
            'period' => (string) $year,
            'source' => $source,
            'source_key' => $source,
            'description' => $description,
            'annual_premium' => '$'.number_format($amount),
            'premium_amount' => $amount,
            'status' => 'Posted',
            'status_key' => 'posted',
            'posted_at' => $postedAt !== '—' ? $postedAt : '—',
        ];
    }

    private function otherTrainingRows(User $user): array
    {
        $lessons = DB::table('training_lessons')
            ->join('training_modules', 'training_modules.id', '=', 'training_lessons.training_module_id')
            ->join('training_categories', 'training_categories.id', '=', 'training_modules.training_category_id')
            ->where('training_modules.is_published', true)
            ->whereNull('training_modules.deleted_at')
            ->whereNull('training_lessons.deleted_at')
            ->whereNull('training_categories.deleted_at')
            ->orderBy('training_categories.sort_order')
            ->orderBy('training_modules.sort_order')
            ->orderBy('training_lessons.sort_order')
            ->select([
                'training_lessons.id',
                'training_lessons.title as lesson',
                'training_modules.title as module',
                'training_categories.name as category',
            ])
            ->get();

        if ($lessons->isEmpty()) {
            return [];
        }

        $progress = DB::table('training_progress')
            ->where('user_id', $user->id)
            ->whereIn('training_lesson_id', $lessons->pluck('id'))
            ->get()
            ->keyBy('training_lesson_id');

        return $lessons
            ->map(function (object $lesson) use ($progress): array {
                $row = $progress->get($lesson->id);
                $status = $row->status ?? 'not_started';

                return [
                    'module' => $lesson->module,
                    'lesson' => $lesson->lesson,
                    'category' => $lesson->category,
                    'status' => str($status)->replace('_', ' ')->title()->toString(),
                    'status_key' => $status,
                    'completed_at' => $this->formatDate($row->completed_at ?? null),
                ];
            })
            ->values()
            ->all();
    }

    private function formatDate(mixed $value): string
    {
        if (! $value) {
            return '—';
        }

        return Carbon::parse($value)->format('M j, Y');
    }

    private function recruitRoleLabel(User $user): string
    {
        if ($user->hasRole('agency-owner')) {
            return 'AO';
        }

        if ($user->hasRole('certified-field-mentor')) {
            return 'CFM';
        }

        return 'Member';
    }

    private function countryFlag(?string $country): string
    {
        return match (strtolower((string) $country)) {
            'canada', 'ca' => 'CA',
            'united states', 'usa', 'us', 'u.s.', 'u.s.a.' => 'US',
            'philippines', 'ph' => 'PH',
            'global', '' => 'GL',
            default => strtoupper(str($country)->substr(0, 2)->value() ?: 'GL'),
        };
    }
}
