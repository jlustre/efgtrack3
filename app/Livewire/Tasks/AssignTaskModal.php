<?php

namespace App\Livewire\Tasks;

use App\Models\Task;
use App\Models\User;
use App\Services\TaskAssignmentService;
use App\Services\TaskCategoryService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class AssignTaskModal extends Component
{
    private const SEARCH_MIN_LENGTH = 3;

    public bool $show = false;

    public ?int $assigneeId = null;

    public string $assigneeSearch = '';

    public bool $assigneePickerOpen = false;

    public ?int $taskCategoryId = null;

    public ?int $taskId = null;

    public string $taskSearch = '';

    public bool $taskPickerOpen = false;

    public string $priority = 'medium';

    public string $status = 'to_do';

    public ?string $dueDate = null;

    public string $additionalNotes = '';

    public string $relatedPerson = '';

    public string $relatedModule = '';

    public ?string $selectedTaskDescription = null;

    #[On('open-assign-task-modal')]
    public function open(): void
    {
        $this->resetForm();
        $this->show = true;
    }

    public function close(): void
    {
        $this->show = false;
        $this->assigneePickerOpen = false;
        $this->taskPickerOpen = false;
    }

    public function updatedAssigneeSearch(): void
    {
        if ($this->assigneeId && $this->assigneeSearch !== $this->assigneeLabelFor($this->assigneeId)) {
            $this->assigneeId = null;
        }

        $this->assigneePickerOpen = strlen(trim($this->assigneeSearch)) >= self::SEARCH_MIN_LENGTH;
    }

    public function updatedTaskSearch(): void
    {
        if ($this->taskId && $this->taskSearch !== $this->taskLabelFor($this->taskId)) {
            $this->taskId = null;
            $this->selectedTaskDescription = null;
        }

        $this->taskPickerOpen = strlen(trim($this->taskSearch)) >= self::SEARCH_MIN_LENGTH;
    }

    public function updatedTaskCategoryId(): void
    {
        $this->taskId = null;
        $this->taskSearch = '';
        $this->taskPickerOpen = false;
        $this->selectedTaskDescription = null;
    }

    public function updatedTaskId(): void
    {
        $this->syncDefaultsFromTask();
    }

    public function openAssigneePicker(): void
    {
        if (strlen(trim($this->assigneeSearch)) >= self::SEARCH_MIN_LENGTH) {
            $this->assigneePickerOpen = true;
        }
    }

    public function openTaskPicker(): void
    {
        if ($this->taskCategoryId && strlen(trim($this->taskSearch)) >= self::SEARCH_MIN_LENGTH) {
            $this->taskPickerOpen = true;
        }
    }

    public function selectAssignee(int $assigneeId): void
    {
        $this->assigneeId = $assigneeId;
        $this->assigneeSearch = $this->assigneeLabelFor($assigneeId);
        $this->assigneePickerOpen = false;
        $this->resetValidation('assigneeId');
    }

    public function selectTask(int $taskId): void
    {
        $this->taskId = $taskId;
        $this->taskSearch = $this->taskLabelFor($taskId);
        $this->taskPickerOpen = false;
        $this->resetValidation('taskId');
        $this->syncDefaultsFromTask();
    }

    public function assign(TaskAssignmentService $assignments): void
    {
        $user = auth()->user();

        abort_unless($user, 403);

        $this->validate([
            'assigneeId' => ['required', 'integer', 'exists:users,id'],
            'taskCategoryId' => ['required', 'integer', 'exists:task_categories,id'],
            'taskId' => ['required', 'integer', 'exists:tasks,id'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'status' => ['required', 'in:to_do,in_progress,waiting,overdue,completed,cancelled'],
            'dueDate' => ['nullable', 'date'],
            'additionalNotes' => ['nullable', 'string', 'max:5000'],
            'relatedPerson' => ['nullable', 'string', 'max:120'],
            'relatedModule' => ['nullable', 'string', 'max:60'],
        ]);

        if (! $assignments->viewerCanAssignTo($user, (int) $this->assigneeId)) {
            $this->addError('assigneeId', 'You do not have permission to assign tasks to this member.');

            return;
        }

        $assignments->assign($user, [
            'assignee_id' => $this->assigneeId,
            'task_category_id' => $this->taskCategoryId,
            'task_id' => $this->taskId,
            'priority' => $this->priority,
            'status' => $this->status,
            'due_date' => $this->dueDate ?: null,
            'additional_notes' => $this->additionalNotes ?: null,
            'related_person' => $this->relatedPerson ?: null,
            'related_module' => $this->relatedModule ?: null,
        ]);

        $this->dispatch('task-assigned');
        $this->close();
        session()->flash('task_assignment_status', 'assigned');
    }

    public function render(
        TaskAssignmentService $assignments,
        TaskCategoryService $taskCategories,
    ): View {
        $viewer = auth()->user();
        $assigneeNeedle = trim($this->assigneeSearch);
        $taskNeedle = trim($this->taskSearch);

        return view('livewire.tasks.assign-task-modal', [
            'assigneeOptions' => $viewer && strlen($assigneeNeedle) >= self::SEARCH_MIN_LENGTH
                ? $assignments->searchAssignableUsersFor($viewer, $assigneeNeedle)
                : collect(),
            'taskOptions' => strlen($taskNeedle) >= self::SEARCH_MIN_LENGTH
                ? $assignments->searchLibraryTasksForCategory($this->taskCategoryId, $taskNeedle)
                : collect(),
            'categories' => $taskCategories->activeCategories(),
            'priorities' => [
                'low' => 'Low',
                'medium' => 'Medium',
                'high' => 'High',
                'urgent' => 'Urgent',
            ],
            'statuses' => [
                'to_do' => 'To Do',
                'in_progress' => 'In Progress',
                'waiting' => 'Waiting',
                'overdue' => 'Overdue',
            ],
        ]);
    }

    private function resetForm(): void
    {
        $user = auth()->user();

        $this->assigneeId = $user?->id;
        $this->assigneeSearch = $this->assigneeId ? $this->assigneeLabelFor($this->assigneeId) : '';
        $this->assigneePickerOpen = false;
        $this->taskCategoryId = null;
        $this->taskId = null;
        $this->taskSearch = '';
        $this->taskPickerOpen = false;
        $this->priority = 'medium';
        $this->status = 'to_do';
        $this->dueDate = now()->addDays(3)->toDateString();
        $this->additionalNotes = '';
        $this->relatedPerson = '';
        $this->relatedModule = '';
        $this->selectedTaskDescription = null;
        $this->resetValidation();
    }

    private function syncDefaultsFromTask(): void
    {
        if (! $this->taskId) {
            $this->selectedTaskDescription = null;

            return;
        }

        $task = Task::query()->find($this->taskId);

        if (! $task) {
            $this->selectedTaskDescription = null;

            return;
        }

        $this->selectedTaskDescription = $task->description;
        $this->priority = in_array($task->default_priority, ['low', 'medium', 'high', 'urgent'], true)
            ? $task->default_priority
            : 'medium';

        if (! filled($this->relatedModule) && filled($task->related_module)) {
            $this->relatedModule = $task->related_module;
        }
    }

    private function assigneeLabelFor(int $assigneeId): string
    {
        $assignee = User::query()->find($assigneeId);

        if (! $assignee) {
            return '';
        }

        return "{$assignee->name} ({$assignee->email})";
    }

    private function taskLabelFor(int $taskId): string
    {
        return Task::query()->whereKey($taskId)->value('title') ?? '';
    }
}
