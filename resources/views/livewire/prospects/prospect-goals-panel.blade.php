<div @class([
    'rounded-lg border border-[#C8A24A]/30 bg-white shadow-sm',
    'p-4' => $compact,
    'p-6' => ! $compact,
])>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 @class([
                'font-semibold text-[#0B1F3A]',
                'text-base' => $compact,
                'text-lg' => ! $compact,
            ])>Period Goals</h2>
            <p class="mt-1 text-sm text-slate-600">{{ $periodLabel }}</p>
        </div>
        @if (! $compact)
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    wire:click="$set('periodType', 'weekly')"
                    @class([
                        'rounded-lg px-3 py-1.5 text-xs font-bold uppercase tracking-wide transition',
                        'bg-[#0B1F3A] text-[#C8A24A]' => $periodType === 'weekly',
                        'border border-slate-300 text-slate-600 hover:border-[#C8A24A]' => $periodType !== 'weekly',
                    ])
                >Weekly</button>
                <button
                    type="button"
                    wire:click="$set('periodType', 'monthly')"
                    @class([
                        'rounded-lg px-3 py-1.5 text-xs font-bold uppercase tracking-wide transition',
                        'bg-[#0B1F3A] text-[#C8A24A]' => $periodType === 'monthly',
                        'border border-slate-300 text-slate-600 hover:border-[#C8A24A]' => $periodType !== 'monthly',
                    ])
                >Monthly</button>
            </div>
        @endif
    </div>

    @if ($compact)
        <div class="mt-3 flex items-center gap-2">
            <button type="button" wire:click="$set('periodType', 'weekly')" @class(['text-xs font-semibold', 'text-[#8A6A1F]' => $periodType === 'weekly', 'text-slate-500' => $periodType !== 'weekly'])>Weekly</button>
            <span class="text-slate-300">|</span>
            <button type="button" wire:click="$set('periodType', 'monthly')" @class(['text-xs font-semibold', 'text-[#8A6A1F]' => $periodType === 'monthly', 'text-slate-500' => $periodType !== 'monthly'])>Monthly</button>
            <a href="{{ route('team.prospects.analytics') }}" class="ml-auto text-xs font-semibold text-[#8A6A1F] hover:text-[#0B1F3A]">Full analytics</a>
        </div>
    @endif

    <div class="mt-4 space-y-4">
        @forelse ($goals as $goal)
            @php($percent = $goal->progressPercent())
            <div wire:key="goal-{{ $goal->id }}" class="rounded-lg border border-slate-200 bg-[#FFF9EA]/40 p-4">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <p class="text-sm font-semibold text-[#0B1F3A]">{{ $metricLabels[$goal->metric_key] ?? $goal->metric_key }}</p>
                        <p class="mt-1 text-xs text-slate-600">{{ $goal->actual_value }} / {{ $goal->target_value }}</p>
                    </div>
                    @if (! $compact)
                        <div class="flex gap-1">
                            <button type="button" wire:click="editGoal({{ $goal->id }})" class="text-xs font-semibold text-[#8A6A1F] hover:text-[#0B1F3A]">Edit</button>
                            <button type="button" wire:click="deleteGoal({{ $goal->id }})" wire:confirm="Delete this goal?" class="text-xs font-semibold text-red-600 hover:text-red-800">Delete</button>
                        </div>
                    @endif
                </div>
                <div class="mt-3 h-2.5 rounded-full bg-slate-200">
                    <div class="h-2.5 rounded-full bg-gradient-to-r from-[#0B1F3A] to-[#C8A24A]" style="width: {{ $percent }}%"></div>
                </div>
                <p class="mt-1 text-right text-xs font-bold text-[#8A6A1F]">{{ $percent }}%</p>
            </div>
        @empty
            <p class="text-sm text-slate-500">No goals set for this period.</p>
        @endforelse
    </div>

    @if (! $compact)
        @if ($showForm)
            <form wire:submit="saveGoal" class="mt-5 space-y-4 rounded-lg border border-[#C8A24A]/40 bg-[#FFF9EA]/60 p-4">
                <label class="block text-sm font-semibold text-[#0B1F3A]">
                    Metric
                    <select wire:model="metricKey" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                        @foreach ($metricLabels as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block text-sm font-semibold text-[#0B1F3A]">
                    Target
                    <input type="number" wire:model="targetValue" min="1" max="9999" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                </label>
                @error('metricKey') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                @error('targetValue') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                <div class="flex gap-2">
                    <button type="submit" class="rounded-lg bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A]">
                        {{ $editingGoalId ? 'Update Goal' : 'Save Goal' }}
                    </button>
                    <button type="button" wire:click="resetForm" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600">Cancel</button>
                </div>
            </form>
        @else
            <button type="button" wire:click="openCreateForm" class="mt-4 w-full rounded-lg border border-dashed border-[#C8A24A] px-4 py-3 text-sm font-semibold text-[#8A6A1F] hover:bg-[#FFF9EA]">
                + Add Goal
            </button>
        @endif
    @endif
</div>
