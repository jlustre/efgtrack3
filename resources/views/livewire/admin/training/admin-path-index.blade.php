<div class="space-y-6">
    <div class="rounded-xl border border-[#C8A24A]/30 bg-gradient-to-br from-[#0B1F3A] via-[#132F55] to-[#0B1F3A] p-6 text-white shadow-lg">
        <a href="{{ route('admin.training.index') }}" class="text-sm font-semibold text-[#C8A24A] transition hover:text-[#D8B75F]">&larr; Training Studio</a>
        <h1 class="mt-2 text-3xl font-semibold">Learning Path Builder</h1>
        <p class="mt-2 text-sm text-slate-200">Create structured development journeys from published courses.</p>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-semibold text-[#0B1F3A]">Learning paths</h2>
            <button type="button" wire:click="$toggle('showCreate')" class="inline-flex rounded-md bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A] hover:bg-[#D8B75F]">
                {{ $showCreate ? 'Close form' : 'New path' }}
            </button>
        </div>

        @if ($showCreate)
            <form wire:submit="createPath" class="mt-5 grid gap-4 border-t border-slate-100 pt-5 lg:grid-cols-2">
                <div>
                    <label class="text-sm font-semibold text-[#0B1F3A]">Name</label>
                    <input type="text" wire:model="name" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#0B1F3A]">Code (optional)</label>
                    <input type="text" wire:model="code" class="mt-1 w-full rounded-lg border-slate-300 text-sm" placeholder="auto-generated if blank">
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#0B1F3A]">Audience</label>
                    <select wire:model="audience" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                        @foreach ($audiences as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <label class="text-sm font-semibold text-[#0B1F3A]">Description</label>
                    <textarea wire:model="description" rows="3" class="mt-1 w-full rounded-lg border-slate-300 text-sm"></textarea>
                </div>
                <div class="lg:col-span-2">
                    <button type="submit" class="inline-flex rounded-md bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-white hover:bg-[#132F55]">Create path</button>
                </div>
            </form>
        @endif

        <div class="mt-5 space-y-3">
            @forelse ($paths as $path)
                <a href="{{ route('admin.training.paths.show', $path) }}" class="flex items-center justify-between rounded-lg border border-slate-100 bg-slate-50/80 px-4 py-3 transition hover:border-[#C8A24A]/40 hover:bg-[#FFF9EA]">
                    <div>
                        <p class="font-semibold text-[#0B1F3A]">{{ $path->name }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $path->code }} · {{ $path->modules_count }} courses</p>
                    </div>
                    <span @class([
                        'rounded-full px-2.5 py-1 text-[0.65rem] font-bold uppercase',
                        'bg-emerald-100 text-emerald-800' => $path->is_active,
                        'bg-slate-200 text-slate-600' => ! $path->is_active,
                    ])>{{ $path->is_active ? 'Active' : 'Inactive' }}</span>
                </a>
            @empty
                <p class="text-sm text-slate-600">No learning paths yet.</p>
            @endforelse
        </div>
    </div>
</div>
