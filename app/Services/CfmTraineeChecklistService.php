<?php

namespace App\Services;

use App\Models\CfmTraineeChecklistItem;
use App\Models\CfmTraineeChecklistProgress;
use App\Models\MentorAssignment;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class CfmTraineeChecklistService
{
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
    public function checklistForAssignment(MentorAssignment $assignment): array
    {
        $assignment->loadMissing(['mentor', 'apprentice.rank', 'apprentice.profile']);

        $items = CfmTraineeChecklistItem::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $progressByItem = CfmTraineeChecklistProgress::query()
            ->where('mentor_assignment_id', $assignment->id)
            ->get()
            ->keyBy('cfm_trainee_checklist_item_id');

        $phases = $items
            ->groupBy('phase_number')
            ->map(function (Collection $phaseItems, int $phaseNumber) use ($progressByItem) {
                $first = $phaseItems->first();
                $completed = $phaseItems->filter(
                    fn (CfmTraineeChecklistItem $item) => ($progressByItem->get($item->id)?->status ?? 'not_started') === 'completed'
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
                                'items' => $sectionItems->map(function (CfmTraineeChecklistItem $item) use ($progressByItem) {
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
                                        'action_url' => $link ? route($link['route']) : null,
                                        'action_label' => $link['label'] ?? null,
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
            fn (CfmTraineeChecklistItem $item) => ($progressByItem->get($item->id)?->status ?? 'not_started') === 'completed'
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

    /**
     * @return array<string, mixed>
     */
    public function checklistJsonForAssignment(MentorAssignment $assignment): array
    {
        $payload = $this->checklistForAssignment($assignment);
        $trainee = $payload['trainee'];

        return [
            'trainee' => [
                'name' => $trainee->name,
                'rank' => $trainee->rank?->code ?? '—',
            ],
            'stats' => $payload['stats'],
            'phases' => $payload['phases']->map(function (array $phase) {
                return [
                    'phase_number' => $phase['phase_number'],
                    'phase_title' => $phase['phase_title'],
                    'phase_target' => $phase['phase_target'],
                    'total' => $phase['total'],
                    'completed' => $phase['completed'],
                    'percent' => $phase['percent'],
                    'sections' => collect($phase['sections'])->map(function (array $section) {
                        return [
                            'title' => $section['title'],
                            'items' => collect($section['items'])->map(function (array $item) {
                                return [
                                    'id' => $item['id'],
                                    'title' => $item['title'],
                                    'is_required' => $item['is_required'],
                                    'is_completed' => $item['is_completed'],
                                    'completed_at' => $item['completed_at']?->toDateString(),
                                    'notes' => $item['notes'],
                                    'action_url' => $item['action_url'] ?? null,
                                    'action_label' => $item['action_label'] ?? null,
                                ];
                            })->values()->all(),
                        ];
                    })->values()->all(),
                ];
            })->values()->all(),
        ];
    }

    public function updateProgress(
        MentorAssignment $assignment,
        CfmTraineeChecklistItem $item,
        User $actingUser,
        bool $completed,
        ?string $notes = null,
    ): CfmTraineeChecklistProgress {
        $this->ensureAssignmentAccess($assignment, $actingUser);

        if (! $item->is_active) {
            throw ValidationException::withMessages([
                'item' => 'This checklist item is no longer active.',
            ]);
        }

        return CfmTraineeChecklistProgress::query()->updateOrCreate(
            [
                'mentor_assignment_id' => $assignment->id,
                'cfm_trainee_checklist_item_id' => $item->id,
            ],
            [
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
    public function progressPercentsForAssignments(Collection $assignmentIds): array
    {
        if ($assignmentIds->isEmpty()) {
            return [];
        }

        $totalItems = CfmTraineeChecklistItem::query()->where('is_active', true)->count();

        if ($totalItems === 0) {
            return $assignmentIds->mapWithKeys(fn (int $id) => [$id => 0])->all();
        }

        $completedCounts = CfmTraineeChecklistProgress::query()
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
}
