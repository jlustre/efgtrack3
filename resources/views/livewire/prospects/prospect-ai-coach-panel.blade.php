<div>
    @if ($recommendations->isNotEmpty())
        <div class="rounded-lg border border-[#C8A24A]/40 bg-[#FFF9EA] p-4 shadow-sm">
            <div class="flex items-center justify-between gap-2">
                <h3 class="text-sm font-semibold text-[#0B1F3A]">AI Coach Suggestions</h3>
                <a href="{{ route('team.prospects.ai-coach') }}" class="text-xs font-semibold text-[#8A6A1F] hover:text-[#0B1F3A]">View all</a>
            </div>
            <ul class="mt-3 space-y-2">
                @foreach ($recommendations as $item)
                    <li class="rounded-lg border border-slate-200 bg-white p-3 text-sm">
                        <p class="font-semibold text-[#0B1F3A]">{{ $item['message'] }}</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <button
                                type="button"
                                wire:click="$dispatch('open-log-activity-modal', { prospectId: '{{ $prospect->id }}', activityType: 'phone_call' })"
                                class="rounded border border-[#C8A24A] bg-[#FFF4CF] px-2 py-1 text-[10px] font-semibold text-[#0B1F3A]"
                            >
                                Log Call
                            </button>
                            <a href="{{ route('team.prospects.appointments') }}" class="rounded border border-slate-200 px-2 py-1 text-[10px] font-semibold text-slate-600">
                                Schedule
                            </a>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
