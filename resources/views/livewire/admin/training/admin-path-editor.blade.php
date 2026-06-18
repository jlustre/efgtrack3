<div class="space-y-6">
    @if (session('admin_training_status') === 'path-saved')
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">Learning path saved.</div>
    @endif

    <div class="rounded-xl border border-[#C8A24A]/30 bg-gradient-to-br from-[#0B1F3A] via-[#132F55] to-[#0B1F3A] p-6 text-white shadow-lg">
        <a href="{{ route('admin.training.paths.index') }}" class="text-sm font-semibold text-[#C8A24A] transition hover:text-[#D8B75F]">&larr; All paths</a>
        <h1 class="mt-2 text-3xl font-semibold">{{ $path->name }}</h1>
        <p class="mt-2 text-sm text-slate-300">{{ $path->code }} · {{ count($moduleRows) }} courses</p>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-[#0B1F3A]">Path settings</h2>
            <form wire:submit="savePath" class="mt-4 space-y-4">
                <div>
                    <label class="text-sm font-semibold text-[#0B1F3A]">Name</label>
                    <input type="text" wire:model="name" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#0B1F3A]">Code</label>
                    <input type="text" wire:model="code" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#0B1F3A]">Description</label>
                    <textarea wire:model="description" rows="4" class="mt-1 w-full rounded-lg border-slate-300 text-sm"></textarea>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-semibold text-[#0B1F3A]">Audience</label>
                        <select wire:model="audience" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                            <option value="">General</option>
                            @foreach ($audiences as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-[#0B1F3A]">Sort order</label>
                        <input type="number" min="0" wire:model="sortOrder" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    </div>
                </div>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="isActive" class="rounded border-slate-300 text-[#C8A24A]"> Active path</label>
                <button type="submit" class="inline-flex rounded-md bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-white hover:bg-[#132F55]">Save path</button>
            </form>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-[#0B1F3A]">Path courses</h2>
            <div class="mt-4 flex flex-wrap items-end gap-2">
                <div class="min-w-[12rem] flex-1">
                    <label class="text-sm font-semibold text-[#0B1F3A]">Add published course</label>
                    <select wire:model="attachModuleId" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                        <option value="">Select course</option>
                        @foreach ($availableModules as $module)
                            <option value="{{ $module->id }}">{{ $module->title }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="button" wire:click="addModuleRow" class="inline-flex rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-[#0B1F3A] hover:bg-slate-50">Add</button>
            </div>

            <div class="mt-4 space-y-3">
                @forelse ($moduleRows as $index => $row)
                    @php $module = $moduleLookup->get($row['module_id']); @endphp
                    <div class="rounded-lg border border-slate-100 bg-slate-50 px-3 py-3 text-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-[#0B1F3A]">{{ $module?->title ?? 'Course #'.$row['module_id'] }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $module?->slug }}</p>
                            </div>
                            <button type="button" wire:click="removeModuleRow({{ $index }})" class="text-xs font-semibold text-red-700 underline">Remove</button>
                        </div>
                        <div class="mt-3 grid gap-3 sm:grid-cols-2">
                            <div>
                                <label class="text-xs font-semibold uppercase text-slate-500">Sort order</label>
                                <input type="number" min="0" wire:model.live="moduleRows.{{ $index }}.sort_order" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                            </div>
                            <div class="flex items-end">
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" wire:model.live="moduleRows.{{ $index }}.is_required" class="rounded border-slate-300 text-[#C8A24A]">
                                    Required
                                </label>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-600">Add published courses to this learning path.</p>
                @endforelse
            </div>
            <p class="mt-4 text-xs text-slate-500">Save path settings to persist course order and requirements.</p>
        </div>
    </div>
</div>
