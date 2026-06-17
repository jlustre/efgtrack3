<?php

namespace App\Services;

use App\Models\Checklist;
use App\Models\ChecklistProgress;
use App\Models\ChecklistType;
use App\Models\MentorAssignment;
use App\Models\User;
use App\Support\ProfileLocationQuery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class ChecklistService
{
    /** @var array<string, int|null> */
    private array $typeIdCache = [];

    public function typeId(string $code): ?int
    {
        if (! array_key_exists($code, $this->typeIdCache)) {
            $this->typeIdCache[$code] = ChecklistType::query()
                ->where('code', $code)
                ->value('id');
        }

        return $this->typeIdCache[$code];
    }

    /**
     * @return Collection<int, Checklist>
     */
    public function activeSteps(string $typeCode, ?string $country = null, ?string $groupLabel = null): Collection
    {
        $query = Checklist::query()
            ->forTypeCode($typeCode)
            ->active()
            ->orderBy('sort_order')
            ->orderBy('title');

        if ($typeCode === 'onboarding') {
            $query->applicableToCountry($country);
        }

        if ($groupLabel !== null) {
            $query->where('group_label', $groupLabel);
        }

        return $query->get();
    }

    /**
     * @param  Collection<int, int>|array<int, int>  $checklistIds
     * @return Collection<int, ChecklistProgress>
     */
    public function userProgressFor(int $userId, Collection|array $checklistIds): Collection
    {
        $ids = collect($checklistIds);

        if ($ids->isEmpty()) {
            return collect();
        }

        return ChecklistProgress::query()
            ->where('user_id', $userId)
            ->whereNull('mentor_assignment_id')
            ->whereIn('checklist_id', $ids)
            ->get()
            ->keyBy('checklist_id');
    }

    /**
     * @param  Collection<int, int>|array<int, int>  $checklistIds
     * @return Collection<int, ChecklistProgress>
     */
    public function assignmentProgressFor(int $assignmentId, Collection|array $checklistIds): Collection
    {
        $ids = collect($checklistIds);

        if ($ids->isEmpty()) {
            return collect();
        }

        return ChecklistProgress::query()
            ->where('mentor_assignment_id', $assignmentId)
            ->whereIn('checklist_id', $ids)
            ->get()
            ->keyBy('checklist_id');
    }

    public function updateUserProgress(User $user, int $checklistId, bool $completed): void
    {
        abort_unless($this->activeChecklistExists($checklistId), 404);

        ChecklistProgress::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'checklist_id' => $checklistId,
                'mentor_assignment_id' => null,
            ],
            [
                'status' => $completed ? 'pending_confirmation' : 'not_started',
                'submitted_at' => $completed ? now() : null,
                'completed_at' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_comments' => null,
            ],
        );
    }

    public function reviewUserProgress(User $reviewer, int $progressId, string $decision, ?string $comments = null): void
    {
        $progress = ChecklistProgress::query()
            ->with(['user', 'checklist'])
            ->whereKey($progressId)
            ->memberProgress()
            ->pendingConfirmation()
            ->whereHas('user', fn (Builder $query) => $query->whereNull('deleted_at'))
            ->whereHas('checklist', fn (Builder $query) => $query->active())
            ->firstOrFail();

        $record = (object) [
            'sponsor_id' => $progress->user->sponsor_id,
            'mentor_id' => $progress->user->mentor_id,
            'notified_parties' => $progress->checklist->notified_parties,
        ];

        abort_unless($this->userCanConfirm($reviewer, $record), 403);

        $confirmed = $decision === 'confirmed';

        $progress->update([
            'status' => $confirmed ? 'completed' : 'rejected',
            'completed_at' => $confirmed ? now() : null,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'review_comments' => $comments,
        ]);
    }

    public function confirmationItemsFor(User $user, string $typeCode): Collection
    {
        $typeId = $this->typeId($typeCode);

        if (! $typeId) {
            return collect();
        }

        return ChecklistProgress::query()
            ->join('users', 'users.id', '=', 'checklist_progress.user_id')
            ->join('checklists', 'checklists.id', '=', 'checklist_progress.checklist_id')
            ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
            ->tap(fn ($query) => ProfileLocationQuery::joinCountry($query))
            ->memberProgress()
            ->pendingConfirmation()
            ->where('checklists.checklist_type_id', $typeId)
            ->whereNull('users.deleted_at')
            ->whereNull('checklists.deleted_at')
            ->where('checklists.is_active', true)
            ->where('users.id', '!=', $user->id)
            ->select(
                'checklist_progress.id',
                'checklist_progress.submitted_at',
                'users.id as member_id',
                'users.name as member_name',
                'users.email as member_email',
                'users.sponsor_id',
                'users.mentor_id',
                ProfileLocationQuery::memberCountrySelect(),
                'checklists.title',
                'checklists.description',
                'checklists.notified_parties'
            )
            ->orderBy('checklist_progress.submitted_at')
            ->get()
            ->filter(fn (object $item) => $this->userCanConfirm($user, $item))
            ->values();
    }

    public function userCanConfirm(User $user, object $item): bool
    {
        $notifiedParties = collect(explode(',', (string) $item->notified_parties))
            ->map(fn (string $party) => strtoupper(trim($party)))
            ->filter()
            ->values();

        if ($notifiedParties->contains('SP') && (int) $item->sponsor_id === $user->id) {
            return true;
        }

        if ($notifiedParties->contains('CFM') && (int) $item->mentor_id === $user->id) {
            return true;
        }

        $roleMap = [
            'AO' => 'agency-owner',
            'TL' => 'team-leader',
            'TR' => 'trainer',
        ];

        foreach ($roleMap as $party => $role) {
            if ($notifiedParties->contains($party) && $user->hasRole($role)) {
                return true;
            }
        }

        return $user->hasAnyRole(['super-admin', 'admin']);
    }

    public function checklistPercent(array $checklistIds, int $userId): int
    {
        if ($checklistIds === []) {
            return 0;
        }

        $completed = (int) ChecklistProgress::query()
            ->where('user_id', $userId)
            ->memberProgress()
            ->whereIn('checklist_id', $checklistIds)
            ->completed()
            ->count();

        return (int) round(($completed / count($checklistIds)) * 100);
    }

    public function activeChecklistExists(int $checklistId): bool
    {
        return Checklist::query()
            ->whereKey($checklistId)
            ->active()
            ->exists();
    }

    public function onboardingChecklistApplicable(Checklist $checklist, ?string $country): bool
    {
        return Checklist::query()
            ->whereKey($checklist->id)
            ->forTypeCode('onboarding')
            ->applicableToCountry($country)
            ->active()
            ->exists();
    }

    public function ensureAssignmentAccess(MentorAssignment $assignment, User $actingUser): void
    {
        if ($assignment->mentor_id !== $actingUser->id && ! $actingUser->hasAnyRole(['super-admin', 'admin'])) {
            abort(403);
        }
    }

    /**
     * @return array{
     *     assignment: MentorAssignment,
     *     trainee: User,
     *     stats: array<string, int|float>,
     *     phases: Collection<int, array<string, mixed>>
     * }
     */
    public function mentoringChecklistForAssignment(MentorAssignment $assignment): array
    {
        $assignment->loadMissing(['mentor', 'apprentice.rank', 'apprentice.profile']);

        $items = $this->activeSteps('cfm-mentoring');

        $progressByItem = $this->assignmentProgressFor($assignment->id, $items->pluck('id'));

        $phases = $items
            ->groupBy('phase_number')
            ->map(function (Collection $phaseItems, int $phaseNumber) use ($progressByItem) {
                $first = $phaseItems->first();
                $completed = $phaseItems->filter(
                    fn (Checklist $item) => ($progressByItem->get($item->id)?->status ?? 'not_started') === 'completed'
                )->count();

                return [
                    'phase_number' => $phaseNumber,
                    'phase_title' => $first->phase_title,
                    'phase_target' => $first->phase_target,
                    'total' => $phaseItems->count(),
                    'completed' => $completed,
                    'percent' => $phaseItems->count() > 0
                        ? (int) round(($completed / $phaseItems->count()) * 100)
                        : 0,
                    'sections' => $phaseItems
                        ->groupBy('section_title')
                        ->map(function (Collection $sectionItems) use ($progressByItem) {
                            return [
                                'title' => $sectionItems->first()->section_title,
                                'items' => $sectionItems->map(function (Checklist $item) use ($progressByItem) {
                                    $progress = $progressByItem->get($item->id);
                                    $link = config('fna.checklist_item_links.'.$item->slug);

                                    return [
                                        'id' => $item->id,
                                        'title' => $item->title,
                                        'slug' => $item->slug,
                                        'is_required' => $item->is_required,
                                        'is_completed' => ($progress?->status ?? 'not_started') === 'completed',
                                        'completed_at' => $progress?->completed_at,
                                        'notes' => $progress?->notes,
                                        'action_url' => $item->action_url ?: ($link ? route($link['route']) : null),
                                        'action_label' => $item->action_label ?: ($link['label'] ?? null),
                                    ];
                                })->values(),
                            ];
                        })
                        ->values(),
                ];
            })
            ->values();

        $total = $items->count();
        $completed = $items->filter(
            fn (Checklist $item) => ($progressByItem->get($item->id)?->status ?? 'not_started') === 'completed'
        )->count();

        return [
            'assignment' => $assignment,
            'trainee' => $assignment->apprentice,
            'stats' => [
                'total' => $total,
                'completed' => $completed,
                'remaining' => $total - $completed,
                'percent' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
            ],
            'phases' => $phases,
        ];
    }

    public function updateMentoringProgress(
        MentorAssignment $assignment,
        Checklist $item,
        User $actingUser,
        bool $completed,
        ?string $notes = null,
    ): ChecklistProgress {
        $this->ensureAssignmentAccess($assignment, $actingUser);

        if (! $item->is_active || $item->type?->code !== 'cfm-mentoring') {
            throw ValidationException::withMessages([
                'item' => 'This checklist item is no longer active.',
            ]);
        }

        return ChecklistProgress::query()->updateOrCreate(
            [
                'mentor_assignment_id' => $assignment->id,
                'checklist_id' => $item->id,
            ],
            [
                'user_id' => null,
                'status' => $completed ? 'completed' : 'not_started',
                'completed_at' => $completed ? now() : null,
                'completed_by' => $completed ? $actingUser->id : null,
                'notes' => $notes,
            ],
        );
    }

    /**
     * @param  Collection<int, int>  $assignmentIds
     * @return array<int, int>
     */
    public function mentoringProgressPercentsForAssignments(Collection $assignmentIds): array
    {
        if ($assignmentIds->isEmpty()) {
            return [];
        }

        $totalItems = Checklist::query()
            ->forTypeCode('cfm-mentoring')
            ->active()
            ->count();

        if ($totalItems === 0) {
            return $assignmentIds->mapWithKeys(fn (int $id) => [$id => 0])->all();
        }

        $completedCounts = ChecklistProgress::query()
            ->whereIn('mentor_assignment_id', $assignmentIds)
            ->where('status', 'completed')
            ->selectRaw('mentor_assignment_id, COUNT(*) as completed_count')
            ->groupBy('mentor_assignment_id')
            ->pluck('completed_count', 'mentor_assignment_id');

        return $assignmentIds->mapWithKeys(function (int $id) use ($completedCounts, $totalItems) {
            $completed = (int) ($completedCounts[$id] ?? 0);

            return [$id => (int) round(($completed / $totalItems) * 100)];
        })->all();
    }

    /**
     * @return Builder<Checklist>
     */
    public function activeStepsQuery(string $typeCode): Builder
    {
        return Checklist::query()
            ->forTypeCode($typeCode)
            ->active()
            ->orderBy('sort_order')
            ->orderBy('title');
    }

    /**
     * @return array<int, int>
     */
    public function activeChecklistIdsForType(string $typeCode): array
    {
        return Checklist::query()
            ->forTypeCode($typeCode)
            ->active()
            ->pluck('id')
            ->all();
    }
}
