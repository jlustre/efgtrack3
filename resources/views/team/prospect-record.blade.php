<x-app-layout>
    @php($isEdit = $mode === 'edit')

    <section class="space-y-6">
        <div class="overflow-hidden rounded-lg border border-slate-400 bg-gradient-to-br from-white via-slate-50 to-[#FFF9EA] shadow-sm">
            <div class="flex flex-col gap-4 bg-[#0B1F3A] px-6 py-6 text-white md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Prospect Management</p>
                    <h1 class="mt-2 text-2xl font-semibold">{{ $isEdit ? 'Edit Prospect' : 'Prospect Profile' }}</h1>
                    <p class="mt-2 text-sm text-slate-200">{{ $prospect->first_name }} {{ $prospect->last_name }}</p>
                </div>
                <div class="flex gap-2">
                    @unless ($isEdit)
                        <a href="{{ route('team.prospects.records.edit', $prospect) }}" class="rounded-lg border border-[#C8A24A] bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A]">Edit</a>
                    @endunless
                    <a href="{{ route('team.prospects') }}" class="rounded-lg border border-white/30 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">Back</a>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('team.prospects.records.update', $prospect) }}" class="rounded-lg border border-slate-400 bg-gradient-to-br from-white via-[#F8FAFC] to-[#FFF9EA] p-6 shadow-sm">
            @csrf
            @method('PATCH')

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">First Name</span>
                    <input name="first_name" value="{{ old('first_name', $prospect->first_name) }}" @disabled(! $isEdit) class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Last Name</span>
                    <input name="last_name" value="{{ old('last_name', $prospect->last_name) }}" @disabled(! $isEdit) class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Email</span>
                    <input name="email" type="email" value="{{ old('email', $prospect->email) }}" @disabled(! $isEdit) class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Phone</span>
                    <input name="phone" value="{{ old('phone', $prospect->phone) }}" @disabled(! $isEdit) class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">City</span>
                    <input name="city" value="{{ old('city', $prospect->city) }}" @disabled(! $isEdit) class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Status</span>
                    <select name="status" @disabled(! $isEdit) class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
                        @foreach (['active', 'archived', 'inactive'] as $status)
                            <option value="{{ $status }}" @selected(old('status', $prospect->status) === $status)>{{ str($status)->title() }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Pipeline Stage</span>
                    <select name="pipeline_stage_id" @disabled(! $isEdit) class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
                        <option value="">No Stage</option>
                        @foreach ($pipelineStages as $stage)
                            <option value="{{ $stage->id }}" @selected((string) old('pipeline_stage_id', $prospect->pipeline_stage_id) === (string) $stage->id)>{{ $stage->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Source</span>
                    <select name="prospect_source_id" @disabled(! $isEdit) class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
                        <option value="">No Source</option>
                        @foreach ($sources as $source)
                            <option value="{{ $source->id }}" @selected((string) old('prospect_source_id', $prospect->prospect_source_id) === (string) $source->id)>{{ $source->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Interest</span>
                    <select name="interest_level" @disabled(! $isEdit) class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
                        @foreach (['cold', 'warm', 'hot'] as $interest)
                            <option value="{{ $interest }}" @selected(old('interest_level', $prospect->interest_level) === $interest)>{{ str($interest)->title() }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Priority</span>
                    <select name="priority" @disabled(! $isEdit) class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
                        @foreach (['low', 'medium', 'high', 'urgent'] as $priority)
                            <option value="{{ $priority }}" @selected(old('priority', $prospect->priority) === $priority)>{{ str($priority)->title() }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Next Follow-Up</span>
                    <input name="next_follow_up_at" type="datetime-local" value="{{ old('next_follow_up_at', $prospect->next_follow_up_at?->format('Y-m-d\TH:i')) }}" @disabled(! $isEdit) class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
                </label>
            </div>

            <label class="mt-4 block">
                <span class="text-sm font-semibold text-slate-700">Notes Summary</span>
                <textarea name="notes_summary" rows="4" @disabled(! $isEdit) class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">{{ old('notes_summary', $prospect->notes_summary) }}</textarea>
            </label>

            @if ($isEdit)
                <div class="mt-5 flex justify-end">
                    <button type="submit" class="rounded-lg bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#12345B]">Save Changes</button>
                </div>
            @endif
        </form>
    </section>
</x-app-layout>
