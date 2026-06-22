@php
    $isOwnProfile = $isOwnProfile ?? true;
    $formAction = $isOwnProfile
        ? route('profile.production.store')
        : route('team.member.production.store', $user);
@endphp

@if (session('production_feedback'))
    <div
        id="member-production-feedback"
        class="mb-4 rounded-lg border px-4 py-3 text-sm {{ session('production_feedback.type') === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-red-200 bg-red-50 text-red-800' }}"
        role="alert"
    >
        <p class="font-semibold">{{ session('production_feedback.type') === 'success' ? 'Production saved' : 'Could not save production' }}</p>
        <p class="mt-1">{{ session('production_feedback.message') }}</p>
    </div>
@endif

@if ($errors->any() && collect($errors->keys())->intersect(['description', 'policy_reference', 'annual_premium', 'posted_at'])->isNotEmpty())
    <div id="member-production-feedback" class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
        <p class="font-semibold">Could not save production</p>
        <ul class="mt-1 list-inside list-disc">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ $formAction }}" class="mb-6 rounded-lg border border-[#C8A24A]/30 bg-[#FFF9EA]/40 p-4">
    @csrf

    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h3 class="text-sm font-semibold text-[#0B1F3A]">
                @if ($isOwnProfile)
                    Record production
                @else
                    Record production for {{ $user->name }}
                @endif
            </h3>
            <p class="mt-1 text-xs text-slate-600">
                @if ($isOwnProfile)
                    Add a new annual premium entry to your production history.
                @else
                    Add a new annual premium entry on behalf of this member.
                @endif
            </p>
        </div>
    </div>

    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="sm:col-span-2">
            <label for="production_description" class="block text-xs font-semibold uppercase text-slate-500">Description</label>
            <input
                id="production_description"
                name="description"
                type="text"
                value="{{ old('description') }}"
                required
                maxlength="255"
                placeholder="Policy or case description"
                class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-[#0B1F3A] shadow-sm focus:border-[#C8A24A] focus:outline-none focus:ring-1 focus:ring-[#C8A24A]"
            />
        </div>

        <div>
            <label for="production_policy_reference" class="block text-xs font-semibold uppercase text-slate-500">Policy reference</label>
            <input
                id="production_policy_reference"
                name="policy_reference"
                type="text"
                value="{{ old('policy_reference') }}"
                maxlength="255"
                placeholder="Optional"
                class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-[#0B1F3A] shadow-sm focus:border-[#C8A24A] focus:outline-none focus:ring-1 focus:ring-[#C8A24A]"
            />
        </div>

        <div>
            <label for="production_annual_premium" class="block text-xs font-semibold uppercase text-slate-500">Annual premium</label>
            <div class="relative mt-1">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-slate-500">$</span>
                <input
                    id="production_annual_premium"
                    name="annual_premium"
                    type="number"
                    step="0.01"
                    min="0.01"
                    value="{{ old('annual_premium') }}"
                    required
                    placeholder="0.00"
                    class="block w-full rounded-lg border border-slate-300 py-2 pl-7 pr-3 text-sm text-[#0B1F3A] shadow-sm focus:border-[#C8A24A] focus:outline-none focus:ring-1 focus:ring-[#C8A24A]"
                />
            </div>
        </div>

        <div>
            <label for="production_posted_at" class="block text-xs font-semibold uppercase text-slate-500">Posted date</label>
            <input
                id="production_posted_at"
                name="posted_at"
                type="date"
                value="{{ old('posted_at', now()->toDateString()) }}"
                max="{{ now()->toDateString() }}"
                class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-[#0B1F3A] shadow-sm focus:border-[#C8A24A] focus:outline-none focus:ring-1 focus:ring-[#C8A24A]"
            />
        </div>
    </div>

    <div class="mt-4 flex justify-end">
        <button
            type="submit"
            class="inline-flex items-center rounded-lg bg-[#C8A24A] px-4 py-2 text-sm font-bold text-[#0B1F3A] transition hover:bg-[#D8B85F]"
        >
            Add production entry
        </button>
    </div>
</form>
