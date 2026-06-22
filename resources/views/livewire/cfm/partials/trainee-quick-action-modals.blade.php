@if ($traineeQuickActionModal && $trainee360)
    @php($p = $trainee360['profile'])
    @php($progress = $trainee360['progress'])

    <div
        class="fixed inset-0 z-[60] flex items-center justify-center overflow-auto bg-slate-900/50 p-4 backdrop-blur-sm"
        wire:click="closeTraineeQuickActionModal"
        wire:keydown.escape="closeTraineeQuickActionModal"
    >
        <div class="max-h-[90vh] w-full overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-xl {{ $traineeQuickActionModal === 'profile' ? 'max-w-2xl' : 'max-w-lg' }}" wire:click.stop>
            <div class="sticky top-0 z-10 flex items-start justify-between gap-4 border-b border-slate-100 bg-white px-6 py-4">
                <div>
                    @switch($traineeQuickActionModal)
                        @case('message')
                            <h3 class="text-xl font-semibold text-[#0B1F3A]">Send Message</h3>
                            <p class="mt-1 text-sm text-slate-500">Message {{ $p['name'] }} through EFGTrack messaging</p>
                            @break
                        @case('meeting')
                            <h3 class="text-xl font-semibold text-[#0B1F3A]">Schedule Meeting</h3>
                            <p class="mt-1 text-sm text-slate-500">Log a coaching session with {{ $p['name'] }}</p>
                            @break
                        @case('task')
                            <h3 class="text-xl font-semibold text-[#0B1F3A]">Create Task</h3>
                            <p class="mt-1 text-sm text-slate-500">Assign a coaching task to {{ $p['name'] }}</p>
                            @break
                        @case('profile')
                            <h3 class="text-xl font-semibold text-[#0B1F3A]">Trainee Profile</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $p['rank'] }} · {{ $p['rank_name'] }}</p>
                            @break
                    @endswitch
                </div>
                <button type="button" wire:click="closeTraineeQuickActionModal" class="text-2xl leading-none text-slate-400 hover:text-[#0B1F3A]">&times;</button>
            </div>

            <div class="px-6 py-5">
                @if ($traineeQuickActionModal === 'message')
                    <form wire:submit.prevent="sendQuickMessage" data-no-page-loader class="space-y-4">
                        <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-950">
                            This sends an in-app direct message — not email. The trainee will see it in their Message Center.
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Message</label>
                            <textarea
                                wire:model="quickMessageBody"
                                rows="5"
                                placeholder="Share coaching feedback, next steps, or a check-in question…"
                                class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]"
                            ></textarea>
                            @error('quickMessageBody') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" wire:click="closeTraineeQuickActionModal" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                            <button type="submit" wire:loading.attr="disabled" wire:target="sendQuickMessage" class="rounded-lg bg-[#C8A24A] px-4 py-2 text-sm font-bold text-[#0B1F3A] hover:bg-[#D8B75F] disabled:opacity-60">
                                <span wire:loading.remove wire:target="sendQuickMessage">Send Message</span>
                                <span wire:loading wire:target="sendQuickMessage">Sending…</span>
                            </button>
                        </div>
                    </form>
                @elseif ($traineeQuickActionModal === 'meeting')
                    <form wire:submit.prevent="createMeeting" data-no-page-loader class="space-y-4">
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Title</label>
                            <input type="text" wire:model="meetingTitle" class="mt-1 w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                            @error('meetingTitle') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Type</label>
                            <select wire:model="meetingType" class="mt-1 w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                                @foreach ($meetingTypes as $type)
                                    <option value="{{ $type }}">{{ str_replace('_', ' ', ucfirst($type)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Starts</label>
                            <input type="datetime-local" wire:model="meetingStartsAt" class="mt-1 w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                            @error('meetingStartsAt') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Ends (optional)</label>
                            <input type="datetime-local" wire:model="meetingEndsAt" class="mt-1 w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" wire:click="closeTraineeQuickActionModal" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                            <button type="submit" wire:loading.attr="disabled" wire:target="createMeeting" class="rounded-lg bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-[#C8A24A] hover:bg-[#102847]">Schedule Meeting</button>
                        </div>
                    </form>
                @elseif ($traineeQuickActionModal === 'task')
                    <form wire:submit.prevent="createTask" data-no-page-loader class="space-y-4">
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Title</label>
                            <input type="text" wire:model="taskTitle" class="mt-1 w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]" placeholder="Review licensing checklist this week">
                            @error('taskTitle') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Notes</label>
                            <textarea wire:model="taskNotes" rows="3" class="mt-1 w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]" placeholder="Optional coaching instructions…"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-semibold text-slate-600">Category</label>
                                <select wire:model="taskCategory" class="mt-1 w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                                    @foreach ($taskCategories as $category)
                                        <option value="{{ $category }}">{{ ucfirst($category) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-600">Priority</label>
                                <select wire:model="taskPriority" class="mt-1 w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                                    @foreach ($taskPriorities as $priority)
                                        <option value="{{ $priority }}">{{ ucfirst($priority) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Due date</label>
                            <input type="date" wire:model="taskDueDate" class="mt-1 w-full rounded-md border-slate-200 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" wire:click="closeTraineeQuickActionModal" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                            <button type="submit" wire:loading.attr="disabled" wire:target="createTask" class="rounded-lg bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-[#C8A24A] hover:bg-[#102847]">Create Task</button>
                        </div>
                    </form>
                @elseif ($traineeQuickActionModal === 'profile')
                    <div class="space-y-5">
                        <div class="flex items-start gap-4">
                            @if ($p['photo_url'])
                                <img src="{{ $p['photo_url'] }}" alt="" class="h-16 w-16 rounded-full object-cover ring-4 ring-[#FFF9EA]">
                            @else
                                <span class="flex h-16 w-16 items-center justify-center rounded-full bg-[#0B1F3A] text-lg font-bold text-[#C8A24A]">{{ $p['initials'] }}</span>
                            @endif
                            <div>
                                <h4 class="text-lg font-semibold text-[#0B1F3A]">{{ $p['name'] }}</h4>
                                <p class="text-sm text-slate-600">{{ $p['email'] }}</p>
                                <p class="mt-1 text-sm text-slate-600">{{ $p['phone'] }} · {{ $p['location'] }}</p>
                            </div>
                        </div>

                        <dl class="grid gap-3 sm:grid-cols-2 text-sm">
                            <div class="rounded-lg bg-slate-50 px-3 py-2"><dt class="text-xs uppercase text-slate-500">Sponsor</dt><dd class="mt-1 font-semibold text-[#0B1F3A]">{{ $p['sponsor'] }}</dd></div>
                            <div class="rounded-lg bg-slate-50 px-3 py-2"><dt class="text-xs uppercase text-slate-500">Agency Owner</dt><dd class="mt-1 font-semibold text-[#0B1F3A]">{{ $p['agency_owner'] }}</dd></div>
                            <div class="rounded-lg bg-slate-50 px-3 py-2"><dt class="text-xs uppercase text-slate-500">CFM</dt><dd class="mt-1 font-semibold text-[#0B1F3A]">{{ $p['cfm'] }}</dd></div>
                            <div class="rounded-lg bg-slate-50 px-3 py-2"><dt class="text-xs uppercase text-slate-500">Joined</dt><dd class="mt-1 font-semibold text-[#0B1F3A]">{{ $p['joined_at'] }}</dd></div>
                        </dl>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Progress snapshot</p>
                            <div class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-5">
                                @foreach (['onboarding' => 'Onboarding', 'licensing' => 'Licensing', 'fap' => 'FAP', 'training' => 'Training', 'rank' => 'Rank'] as $key => $label)
                                    <div class="rounded-lg border border-slate-200 bg-[#FFF9EA]/40 px-2 py-2 text-center">
                                        <p class="text-[0.6rem] font-semibold uppercase tracking-wide text-slate-500">{{ $label }}</p>
                                        <p class="mt-1 text-sm font-bold text-[#0B1F3A]">{{ $progress[$key] ?? 0 }}%</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex flex-wrap justify-end gap-2 border-t border-slate-100 pt-4">
                            <button type="button" wire:click="closeTraineeQuickActionModal" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Close</button>
                            <button type="button" wire:click="openTraineeQuickActionModal('message')" class="rounded-lg border border-[#C8A24A]/50 px-4 py-2 text-sm font-semibold text-[#8A6A1F] hover:bg-[#FFF9EA]">Send Message</button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif
