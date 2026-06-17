<div class="rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-[#C8A24A]">Step {{ $step }} of 9</p>
                <h2 class="text-lg font-semibold text-[#0B1F3A]">
                    @switch($step)
                        @case(1) Select Goal Category @break
                        @case(2) Goal Name @break
                        @case(3) Target Value @break
                        @case(4) Measurement Type @break
                        @case(5) Deadline @break
                        @case(6) Milestones @break
                        @case(7) Accountability Partner @break
                        @case(8) Notifications @break
                        @default Review & Create @break
                    @endswitch
                </h2>
            </div>
            <div class="rounded-full border border-[#C8A24A]/40 bg-[#FFF9EA] px-3 py-1 text-sm font-semibold text-[#8A6A1F]">
                SMART {{ $smartScore }}%
            </div>
        </div>
        <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-slate-200">
            <div class="h-1.5 rounded-full bg-[#C8A24A] transition-all" style="width: {{ round(($step / 9) * 100) }}%"></div>
        </div>
    </div>

    <div class="grid gap-6 p-6 lg:grid-cols-[minmax(0,1fr)_240px]">
        <div class="space-y-5">
            @if ($step === 1)
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach ($categories as $category)
                        <button
                            type="button"
                            wire:click="selectCategory({{ $category->id }})"
                            wire:key="goal-category-{{ $category->id }}"
                            @class([
                                'rounded-xl border p-4 text-left transition',
                                'border-[#C8A24A] bg-[#FFF9EA] ring-2 ring-[#C8A24A]/30' => (int) $goalCategoryId === $category->id,
                                'border-slate-200 hover:border-[#C8A24A]/50' => (int) $goalCategoryId !== $category->id,
                            ])
                        >
                            <p class="font-semibold text-[#0B1F3A]">{{ $category->name }}</p>
                            <p class="mt-1 text-xs text-slate-600">{{ $category->description }}</p>
                        </button>
                    @endforeach
                </div>
                @if ($templates->isNotEmpty())
                    <div>
                        <p class="text-sm font-semibold text-[#0B1F3A]">Quick templates</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($templates as $template)
                                <button type="button" wire:click="applyTemplate({{ $template->id }})" class="rounded-full border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-700 hover:border-[#C8A24A]">
                                    {{ $template->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
                <x-input-error :messages="$errors->get('goalCategoryId')" />
            @elseif ($step === 2)
                <div>
                    <x-input-label for="goal_name" value="Goal name" />
                    <x-text-input id="goal_name" wire:model.live.debounce.500ms="name" class="mt-1 block w-full" placeholder="e.g. Recruit 4 associates this month" />
                    <x-input-error :messages="$errors->get('name')" />
                </div>
                <div>
                    <x-input-label for="goal_description" value="Description" />
                    <textarea id="goal_description" wire:model.live.debounce.500ms="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]" placeholder="What does success look like?"></textarea>
                </div>
                <div>
                    <x-input-label for="hierarchy_level" value="Goal level" />
                    <select id="hierarchy_level" wire:model="hierarchyLevel" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                        @foreach ($hierarchyLevels as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                @if ($parentGoals->isNotEmpty())
                    <div>
                        <x-input-label for="parent_goal" value="Link to parent goal (optional)" />
                        <select id="parent_goal" wire:model="parentGoalId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                            <option value="">None — standalone goal</option>
                            @foreach ($parentGoals as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->name }} ({{ ucfirst($parent->hierarchy_level) }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            @elseif ($step === 3)
                <div>
                    <x-input-label for="target_value" value="Target value" />
                    <x-text-input id="target_value" type="number" step="0.01" wire:model="targetValue" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('targetValue')" />
                </div>
                @if (count($metrics) > 0)
                    <div>
                        <x-input-label for="metric_key" value="Automated KPI (optional)" />
                        <select id="metric_key" wire:model="metricKey" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                            <option value="">Manual tracking</option>
                            @foreach ($metrics as $key => $metric)
                                <option value="{{ $key }}">{{ $metric['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            @elseif ($step === 4)
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach ($measurementTypes as $key => $label)
                        <button type="button" wire:click="selectMeasurementType('{{ $key }}')" @class([
                            'rounded-lg border px-4 py-3 text-left text-sm font-semibold',
                            'border-[#C8A24A] bg-[#FFF9EA] text-[#0B1F3A]' => $measurementType === $key,
                            'border-slate-200 text-slate-700' => $measurementType !== $key,
                        ])>{{ $label }}</button>
                    @endforeach
                </div>
                <x-input-error :messages="$errors->get('measurementType')" />
            @elseif ($step === 5)
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <x-input-label for="starts_at" value="Start date" />
                        <x-text-input id="starts_at" type="date" wire:model="startsAt" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="deadline_at" value="Deadline" />
                        <x-text-input id="deadline_at" type="date" wire:model="deadlineAt" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('deadlineAt')" />
                    </div>
                </div>
            @elseif ($step === 6)
                <div class="space-y-3">
                    @foreach ($milestones as $index => $milestone)
                        <div wire:key="milestone-{{ $index }}" class="grid gap-2 rounded-lg border border-slate-200 p-3 md:grid-cols-[1fr_140px_100px_auto]">
                            <x-text-input wire:model="milestones.{{ $index }}.name" placeholder="Milestone name" />
                            <x-text-input type="date" wire:model="milestones.{{ $index }}.due_at" />
                            <x-text-input type="number" wire:model="milestones.{{ $index }}.target_value" placeholder="Target" />
                            <button type="button" wire:click="removeMilestone({{ $index }})" class="text-xs font-semibold text-red-600">Remove</button>
                        </div>
                    @endforeach
                    <button type="button" wire:click="addMilestone" class="text-sm font-semibold text-[#8A6A1F] hover:underline">+ Add milestone</button>
                </div>
            @elseif ($step === 7)
                <div>
                    <x-input-label for="accountability_partner" value="Accountability partner" />
                    <select id="accountability_partner" wire:model="accountabilityPartnerId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                        <option value="">None selected</option>
                        @foreach ($partners as $partner)
                            <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                        @endforeach
                    </select>
                </div>
            @elseif ($step === 8)
                <div class="space-y-3">
                    <label class="flex items-center gap-2 text-sm text-[#0B1F3A]"><input type="checkbox" wire:model="notifyEmail" class="rounded border-slate-300 text-[#C8A24A]"> Email progress updates</label>
                    <label class="flex items-center gap-2 text-sm text-[#0B1F3A]"><input type="checkbox" wire:model="notifyInApp" class="rounded border-slate-300 text-[#C8A24A]"> In-app notifications</label>
                    <label class="flex items-center gap-2 text-sm text-[#0B1F3A]"><input type="checkbox" wire:model="remindWeekly" class="rounded border-slate-300 text-[#C8A24A]"> Weekly check-in reminder</label>
                </div>
            @else
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-lg bg-slate-50 p-4"><dt class="text-xs uppercase text-slate-500">Category</dt><dd class="mt-1 font-semibold text-[#0B1F3A]">{{ $categories->firstWhere('id', $goalCategoryId)?->name }}</dd></div>
                    <div class="rounded-lg bg-slate-50 p-4"><dt class="text-xs uppercase text-slate-500">Goal</dt><dd class="mt-1 font-semibold text-[#0B1F3A]">{{ $name }}</dd></div>
                    <div class="rounded-lg bg-slate-50 p-4"><dt class="text-xs uppercase text-slate-500">Target</dt><dd class="mt-1 font-semibold text-[#0B1F3A]">{{ $targetValue }} ({{ $measurementTypes[$measurementType] ?? $measurementType }})</dd></div>
                    <div class="rounded-lg bg-slate-50 p-4"><dt class="text-xs uppercase text-slate-500">Deadline</dt><dd class="mt-1 font-semibold text-[#0B1F3A]">{{ $deadlineAt ?: '—' }}</dd></div>
                </dl>
            @endif

            <div class="flex flex-wrap gap-3 border-t border-slate-200 pt-4">
                @if ($step > 1)
                    <button type="button" wire:click="previousStep" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Back</button>
                @endif
                @if ($step < 9)
                    <button type="button" wire:click="nextStep" class="rounded-lg bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-white hover:bg-[#132F55]">Continue</button>
                @else
                    <button type="button" wire:click="save" class="rounded-lg bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A] hover:bg-[#D8B85F]">Create Goal</button>
                @endif
            </div>
        </div>

        <aside class="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-[#C8A24A]">SMART validation</p>
            <div class="mt-3 space-y-2">
                @foreach ($smartFeedback as $item)
                    <div class="flex items-start gap-2 text-sm">
                        <span @class([
                            'mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-xs font-bold',
                            'bg-emerald-100 text-emerald-700' => $item['passed'],
                            'bg-amber-100 text-amber-800' => ! $item['passed'],
                        ])>{{ $item['passed'] ? '✓' : '!' }}</span>
                        <div>
                            <p class="font-semibold text-[#0B1F3A]">{{ $item['label'] }}</p>
                            @if (! $item['passed'] && $item['suggestion'])
                                <p class="text-xs text-slate-600">{{ $item['suggestion'] }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </aside>
    </div>
</div>
