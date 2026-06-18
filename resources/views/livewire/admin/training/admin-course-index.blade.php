<div class="space-y-6">
    <div class="rounded-xl border border-[#C8A24A]/30 bg-gradient-to-br from-[#0B1F3A] via-[#132F55] to-[#0B1F3A] p-6 text-white shadow-lg">
        <a href="{{ route('admin.training.index') }}" class="text-sm font-semibold text-[#C8A24A] transition hover:text-[#D8B75F]">&larr; Training Studio</a>
        <h1 class="mt-2 text-3xl font-semibold">Course Builder</h1>
        <p class="mt-2 text-sm text-slate-200">Create and manage academy courses.</p>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-semibold text-[#0B1F3A]">Courses</h2>
            <button type="button" wire:click="$toggle('showCreate')" class="inline-flex rounded-md bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A] hover:bg-[#D8B75F]">
                {{ $showCreate ? 'Close form' : 'New course' }}
            </button>
        </div>

        @if ($showCreate)
            <form wire:submit="createCourse" class="mt-5 grid gap-4 border-t border-slate-100 pt-5 lg:grid-cols-2">
                <div>
                    <label class="text-sm font-semibold text-[#0B1F3A]">Category</label>
                    <select wire:model="trainingCategoryId" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                        <option value="">Select category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('trainingCategoryId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#0B1F3A]">Title</label>
                    <input type="text" wire:model="title" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="lg:col-span-2">
                    <label class="text-sm font-semibold text-[#0B1F3A]">Description</label>
                    <textarea wire:model="description" rows="3" class="mt-1 w-full rounded-lg border-slate-300 text-sm"></textarea>
                </div>
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
                <div class="flex items-center gap-2 lg:col-span-2">
                    <input type="checkbox" wire:model="isPublished" id="create-published" class="rounded border-slate-300 text-[#C8A24A]">
                    <label for="create-published" class="text-sm text-slate-700">Publish immediately</label>
                </div>
                <div class="lg:col-span-2">
                    <button type="submit" class="inline-flex rounded-md bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-white hover:bg-[#132F55]">Create course</button>
                </div>
            </form>
        @endif

        <div class="mt-5 space-y-3">
            @forelse ($courses as $course)
                <a href="{{ route('admin.training.courses.show', $course) }}" class="flex items-center justify-between rounded-lg border border-slate-100 bg-slate-50/80 px-4 py-3 transition hover:border-[#C8A24A]/40 hover:bg-[#FFF9EA]">
                    <div>
                        <p class="font-semibold text-[#0B1F3A]">{{ $course->title }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $course->category?->name }} · {{ $course->lessons_count }} lessons · {{ $course->slug }}</p>
                    </div>
                    <span @class([
                        'rounded-full px-2.5 py-1 text-[0.65rem] font-bold uppercase',
                        'bg-emerald-100 text-emerald-800' => $course->is_published,
                        'bg-amber-100 text-amber-800' => ! $course->is_published,
                    ])>{{ $course->is_published ? 'Published' : 'Draft' }}</span>
                </a>
            @empty
                <p class="text-sm text-slate-600">No courses yet. Create your first academy course.</p>
            @endforelse
        </div>
    </div>
</div>
