<div>
    <div
        @class([
            'fixed inset-0 z-50 flex items-center justify-center p-4',
            'hidden' => ! $show,
        ])
        role="dialog"
        aria-modal="true"
        aria-labelledby="assign-task-modal-title"
        @if (! $show) aria-hidden="true" @endif
    >
        <div class="absolute inset-0 bg-[#0B1F3A]/60" wire:click="close"></div>

        <div class="relative z-10 flex max-h-[92vh] w-full max-w-2xl flex-col overflow-hidden rounded-lg border border-[#C8A24A]/40 bg-white shadow-xl">
            <div class="border-b border-slate-200 bg-[#0B1F3A] px-6 py-4 text-white">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-[#C8A24A]">Task Assignment</p>
                        <h3 id="assign-task-modal-title" class="mt-1 text-lg font-semibold">Assign a task</h3>
                        <p class="mt-1 text-sm text-slate-300">Choose a team member, task template, and due date.</p>
                    </div>
                    <button type="button" wire:click="close" class="text-2xl leading-none text-slate-300 hover:text-white">&times;</button>
                </div>
            </div>

            <form wire:submit="assign" class="flex min-h-0 flex-1 flex-col">
                <div class="min-h-0 flex-1 space-y-4 overflow-y-auto p-6">
                    <div
                        class="relative"
                        x-data
                        x-on:click.outside="$wire.set('assigneePickerOpen', false)"
                    >
                        <label for="assign-task-assignee" class="text-sm font-semibold text-[#0B1F3A]">Assign to</label>
                        <input
                            id="assign-task-assignee"
                            type="search"
                            wire:model.live.debounce.300ms="assigneeSearch"
                            wire:focus="openAssigneePicker"
                            autocomplete="off"
                            placeholder="Search by name or email (min. 3 characters)"
                            class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]"
                        >
                        @if (strlen(trim($assigneeSearch)) > 0 && strlen(trim($assigneeSearch)) < 3)
                            <p class="mt-1 text-xs text-slate-500">Type at least 3 characters to search members.</p>
                        @endif
                        @if ($assigneePickerOpen && strlen(trim($assigneeSearch)) >= 3)
                            @if ($assigneeOptions->isNotEmpty())
                                <ul
                                    class="absolute z-20 mt-1 max-h-52 w-full overflow-y-auto rounded-lg border border-slate-300 bg-white py-1 shadow-lg"
                                    role="listbox"
                                >
                                    @foreach ($assigneeOptions as $assignee)
                                        <li role="option" wire:key="assign-task-assignee-{{ $assignee->id }}">
                                            <button
                                                type="button"
                                                wire:click="selectAssignee({{ $assignee->id }})"
                                                class="flex w-full flex-col px-3 py-2 text-left text-sm text-[#0B1F3A] hover:bg-[#FFF9EA]"
                                            >
                                                <span class="font-semibold">{{ $assignee->name }}</span>
                                                <span class="text-xs text-slate-500">{{ $assignee->email }}</span>
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="mt-1 rounded-lg border border-dashed border-slate-300 bg-slate-50 px-3 py-2 text-xs text-slate-500">
                                    No matching members found.
                                </p>
                            @endif
                        @endif
                        @error('assigneeId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="assign-task-category" class="text-sm font-semibold text-[#0B1F3A]">Category</label>
                            <select
                                id="assign-task-category"
                                wire:model.live="taskCategoryId"
                                class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]"
                            >
                                <option value="">Select category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('taskCategoryId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div
                            class="relative"
                            x-data
                            x-on:click.outside="$wire.set('taskPickerOpen', false)"
                        >
                            <label for="assign-task-template" class="text-sm font-semibold text-[#0B1F3A]">Task</label>
                            <input
                                id="assign-task-template"
                                type="search"
                                wire:model.live.debounce.300ms="taskSearch"
                                wire:focus="openTaskPicker"
                                autocomplete="off"
                                placeholder="{{ $taskCategoryId ? 'Search tasks by name (min. 3 characters)' : 'Select a category first' }}"
                                @disabled(! $taskCategoryId)
                                class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A] disabled:cursor-not-allowed disabled:bg-slate-100"
                            >
                            @if ($taskCategoryId && strlen(trim($taskSearch)) > 0 && strlen(trim($taskSearch)) < 3)
                                <p class="mt-1 text-xs text-slate-500">Type at least 3 characters to search tasks.</p>
                            @endif
                            @if ($taskPickerOpen && $taskCategoryId && strlen(trim($taskSearch)) >= 3)
                                @if ($taskOptions->isNotEmpty())
                                    <ul
                                        class="absolute z-20 mt-1 max-h-52 w-full overflow-y-auto rounded-lg border border-slate-300 bg-white py-1 shadow-lg"
                                        role="listbox"
                                    >
                                        @foreach ($taskOptions as $libraryTask)
                                            <li role="option" wire:key="assign-task-template-{{ $libraryTask->id }}">
                                                <button
                                                    type="button"
                                                    wire:click="selectTask({{ $libraryTask->id }})"
                                                    class="flex w-full px-3 py-2 text-left text-sm font-semibold text-[#0B1F3A] hover:bg-[#FFF9EA]"
                                                >
                                                    {{ $libraryTask->title }}
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="mt-1 rounded-lg border border-dashed border-slate-300 bg-slate-50 px-3 py-2 text-xs text-slate-500">
                                        No matching tasks found in this category.
                                    </p>
                                @endif
                            @endif
                            @error('taskId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    @if (filled($selectedTaskDescription))
                        <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Task description</p>
                            <p class="mt-1">{{ $selectedTaskDescription }}</p>
                        </div>
                    @endif

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="assign-task-priority" class="text-sm font-semibold text-[#0B1F3A]">Priority</label>
                            <select
                                id="assign-task-priority"
                                wire:model="priority"
                                class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]"
                            >
                                @foreach ($priorities as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('priority') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="assign-task-status" class="text-sm font-semibold text-[#0B1F3A]">Status</label>
                            <select
                                id="assign-task-status"
                                wire:model="status"
                                class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]"
                            >
                                @foreach ($statuses as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="assign-task-due-date" class="text-sm font-semibold text-[#0B1F3A]">Due date</label>
                            <input
                                id="assign-task-due-date"
                                type="date"
                                wire:model="dueDate"
                                class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]"
                            >
                            @error('dueDate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="assign-task-related-person" class="text-sm font-semibold text-[#0B1F3A]">Related person</label>
                            <input
                                id="assign-task-related-person"
                                type="text"
                                wire:model="relatedPerson"
                                placeholder="Prospect, client, or member name"
                                class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]"
                            >
                            @error('relatedPerson') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="assign-task-related-module" class="text-sm font-semibold text-[#0B1F3A]">Related module</label>
                        <input
                            id="assign-task-related-module"
                            type="text"
                            wire:model="relatedModule"
                            placeholder="e.g. Prospects, Training, FNA"
                            class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]"
                        >
                        @error('relatedModule') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="assign-task-notes" class="text-sm font-semibold text-[#0B1F3A]">Additional notes</label>
                        <textarea
                            id="assign-task-notes"
                            wire:model="additionalNotes"
                            rows="4"
                            placeholder="Optional context or instructions for the assignee"
                            class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]"
                        ></textarea>
                        @error('additionalNotes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-between gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                    <p class="text-xs text-slate-500">Assigned tasks appear in the member&apos;s task queue and task manager.</p>
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            wire:click="close"
                            class="inline-flex rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-lg bg-[#C8A24A] px-4 py-2 text-xs font-semibold text-[#0B1F3A] transition hover:bg-[#D8B75F]"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove wire:target="assign">Assign task</span>
                            <span wire:loading wire:target="assign">Assigning…</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
