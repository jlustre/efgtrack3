<div class="space-y-6">
    @if (session('admin_training_status'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
            @switch(session('admin_training_status'))
                @case('course-saved') Course saved. @break
                @case('lesson-saved') Lesson saved. @break
                @case('lesson-deleted') Lesson removed. @break
            @endswitch
        </div>
    @endif

    <div class="rounded-xl border border-[#C8A24A]/30 bg-gradient-to-br from-[#0B1F3A] via-[#132F55] to-[#0B1F3A] p-6 text-white shadow-lg">
        <a href="{{ route('admin.training.courses.index') }}" class="text-sm font-semibold text-[#C8A24A] transition hover:text-[#D8B75F]">&larr; All courses</a>
        <h1 class="mt-2 text-3xl font-semibold">{{ $module->title }}</h1>
        <p class="mt-2 text-sm text-slate-300">{{ $module->slug }} · {{ $module->lessons->count() }} lessons</p>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-[#0B1F3A]">Course settings</h2>
            <form wire:submit="saveCourse" class="mt-4 space-y-4">
                <div>
                    <label class="text-sm font-semibold text-[#0B1F3A]">Category</label>
                    <select wire:model="trainingCategoryId" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#0B1F3A]">Title</label>
                    <input type="text" wire:model="title" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#0B1F3A]">Slug</label>
                    <input type="text" wire:model="slug" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#0B1F3A]">Description</label>
                    <textarea wire:model="description" rows="4" class="mt-1 w-full rounded-lg border-slate-300 text-sm"></textarea>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-semibold text-[#0B1F3A]">Course type</label>
                        <select wire:model="courseType" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                            @foreach ($courseTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-[#0B1F3A]">Difficulty</label>
                        <select wire:model="difficulty" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                            @foreach ($difficulties as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-semibold text-[#0B1F3A]">Duration (minutes)</label>
                        <input type="number" min="1" wire:model="durationMinutes" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-[#0B1F3A]">Sort order</label>
                        <input type="number" min="0" wire:model="sortOrder" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    </div>
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#0B1F3A]">Instructor</label>
                    <select wire:model="instructorId" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                        <option value="">None</option>
                        @foreach ($instructors as $instructor)
                            <option value="{{ $instructor->id }}">{{ $instructor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="isPublished" class="rounded border-slate-300 text-[#C8A24A]"> Published</label>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="isFeatured" class="rounded border-slate-300 text-[#C8A24A]"> Featured</label>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="sequentialRequired" class="rounded border-slate-300 text-[#C8A24A]"> Sequential lessons</label>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="dripEnabled" class="rounded border-slate-300 text-[#C8A24A]"> Drip schedule</label>
                </div>
                <button type="submit" class="inline-flex rounded-md bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-white hover:bg-[#132F55]">Save course</button>
            </form>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-[#0B1F3A]">{{ $editingLessonId ? 'Edit lesson' : 'Add lesson' }}</h2>
                <form wire:submit="saveLesson" class="mt-4 space-y-4">
                    <div>
                        <label class="text-sm font-semibold text-[#0B1F3A]">Title</label>
                        <input type="text" wire:model="lessonTitle" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                        @error('lessonTitle') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-semibold text-[#0B1F3A]">Type</label>
                            <select wire:model="lessonType" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                                @foreach ($lessonTypes as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-[#0B1F3A]">Sort order</label>
                            <input type="number" min="0" wire:model="lessonSortOrder" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-[#0B1F3A]">Content</label>
                        <textarea wire:model="lessonContent" rows="4" class="mt-1 w-full rounded-lg border-slate-300 text-sm"></textarea>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-[#0B1F3A]">Video URL</label>
                        <input type="text" wire:model="lessonVideoUrl" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    </div>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="lessonRequired" class="rounded border-slate-300 text-[#C8A24A]"> Required lesson</label>
                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex rounded-md bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A] hover:bg-[#D8B75F]">{{ $editingLessonId ? 'Update lesson' : 'Add lesson' }}</button>
                        @if ($editingLessonId)
                            <button type="button" wire:click="cancelLessonEdit" class="inline-flex rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Cancel</button>
                        @endif
                    </div>
                </form>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-[#0B1F3A]">Lessons</h2>
                <div class="mt-4 space-y-2">
                    @forelse ($module->lessons->sortBy('sort_order') as $lesson)
                        <div class="flex items-start justify-between gap-3 rounded-lg border border-slate-100 bg-slate-50 px-3 py-2 text-sm">
                            <div>
                                <p class="font-semibold text-[#0B1F3A]">{{ $lesson->title }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ str($lesson->lesson_type)->title() }} · Order {{ $lesson->sort_order }}</p>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" wire:click="editLesson({{ $lesson->id }})" class="text-xs font-semibold text-[#0B1F3A] underline">Edit</button>
                                <button type="button" wire:click="deleteLesson({{ $lesson->id }})" wire:confirm="Remove this lesson?" class="text-xs font-semibold text-red-700 underline">Delete</button>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-600">No lessons yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
