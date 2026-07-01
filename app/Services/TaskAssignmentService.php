<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class TaskAssignmentService
{
    private const SEARCH_MIN_LENGTH = 3;

    /**
     * @return Collection<int, User>
     */
    public function assignableUsersFor(User $viewer): Collection
    {
        return $this->assignableUsersQuery($viewer)
            ->limit($viewer->hasAnyRole(['super-admin', 'admin']) ? 300 : 200)
            ->get(['id', 'name', 'email']);
    }

    /**
     * @return Collection<int, User>
     */
    public function searchAssignableUsersFor(User $viewer, string $query, int $limit = 20): Collection
    {
        $needle = trim($query);

        if (strlen($needle) < self::SEARCH_MIN_LENGTH) {
            return collect();
        }

        $like = '%'.$needle.'%';

        return $this->assignableUsersQuery($viewer)
            ->where(function (Builder $builder) use ($like): void {
                $builder->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like);
            })
            ->limit($limit)
            ->get(['id', 'name', 'email']);
    }

    public function viewerCanAssignTo(User $viewer, int $assigneeId): bool
    {
        return $this->assignableUsersQuery($viewer)
            ->where('id', $assigneeId)
            ->exists();
    }

    /**
     * @return Collection<int, Task>
     */
    public function libraryTasksForCategory(?int $categoryId): Collection
    {
        if (! $categoryId) {
            return collect();
        }

        return $this->libraryTasksQuery($categoryId)
            ->get(['id', 'title', 'description', 'default_priority', 'related_module', 'task_category_id']);
    }

    /**
     * @return Collection<int, Task>
     */
    public function searchLibraryTasksForCategory(?int $categoryId, string $query, int $limit = 20): Collection
    {
        if (! $categoryId) {
            return collect();
        }

        $needle = trim($query);

        if (strlen($needle) < self::SEARCH_MIN_LENGTH) {
            return collect();
        }

        return $this->libraryTasksQuery($categoryId)
            ->where('title', 'like', '%'.$needle.'%')
            ->limit($limit)
            ->get(['id', 'title', 'description', 'default_priority', 'related_module', 'task_category_id']);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function assign(User $assignor, array $attributes): TaskUser
    {
        $validated = validator($attributes, [
            'assignee_id' => ['required', 'integer', 'exists:users,id'],
            'task_category_id' => ['required', 'integer', 'exists:task_categories,id'],
            'task_id' => ['required', 'integer', 'exists:tasks,id'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'status' => ['required', 'in:to_do,in_progress,waiting,overdue,completed,cancelled'],
            'due_date' => ['nullable', 'date'],
            'additional_notes' => ['nullable', 'string', 'max:5000'],
            'related_person' => ['nullable', 'string', 'max:120'],
            'related_module' => ['nullable', 'string', 'max:60'],
        ])->validate();

        if (! $this->viewerCanAssignTo($assignor, (int) $validated['assignee_id'])) {
            throw ValidationException::withMessages([
                'assignee_id' => 'You do not have permission to assign tasks to this member.',
            ]);
        }

        $task = Task::query()->findOrFail($validated['task_id']);

        if ((int) $task->task_category_id !== (int) $validated['task_category_id']) {
            throw ValidationException::withMessages([
                'task_id' => 'The selected task does not belong to the chosen category.',
            ]);
        }

        return TaskUser::query()->create([
            'assignee_id' => $validated['assignee_id'],
            'assignor_id' => $assignor->id,
            'task_id' => $task->id,
            'task_category_id' => $task->task_category_id,
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'due_date' => $validated['due_date'] ?? null,
            'additional_notes' => $validated['additional_notes'] ?? null,
            'related_person' => $validated['related_person'] ?? null,
            'related_module' => $validated['related_module'] ?? $task->related_module,
            'progress' => 0,
        ]);
    }

    /**
     * @return Builder<User>
     */
    private function assignableUsersQuery(User $viewer): Builder
    {
        $query = User::query()
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->where('id', '!=', \App\Support\SystemTaskAssignor::USER_ID)
            ->orderBy('name');

        if ($viewer->hasAnyRole(['super-admin', 'admin'])) {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($viewer): void {
            $builder->where('id', $viewer->id);

            if ($viewer->team_id) {
                $builder->orWhere('team_id', $viewer->team_id);
            }

            if ($viewer->hasAnyRole(['agency-owner', 'team-leader'])) {
                $builder->orWhere('sponsor_id', $viewer->id);
            }
        });
    }

    /**
     * @return Builder<Task>
     */
    private function libraryTasksQuery(int $categoryId): Builder
    {
        return Task::query()
            ->where('task_category_id', $categoryId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('title');
    }
}
