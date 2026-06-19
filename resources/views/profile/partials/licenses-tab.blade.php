@php
    $isOwnProfile = $isOwnProfile ?? true;
    $locationOptions = $profileContext['locationOptions'];
    $selectedLicenses = old(
        'insurance_licenses',
        $user->profile?->insurance_licenses ?? []
    );
    $licenseLabels = \App\Support\LocationOptions::labelsForJurisdictionKeys(
        is_array($selectedLicenses) ? $selectedLicenses : []
    );
    $licensesFeedback = session('licenses_feedback');
@endphp

<section>
    <div class="mb-5 flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 class="text-lg font-semibold text-[#0B1F3A]">Insurance Licenses</h2>
            <p class="mt-1 max-w-2xl text-sm text-slate-600">
                @if ($isOwnProfile)
                    Select every country and state or province where you hold an active life insurance license.
                @else
                    Jurisdictions where {{ $user->name }} is licensed to sell life insurance.
                @endif
            </p>
        </div>
        @if ($isOwnProfile && $licenseLabels !== [])
            <span class="rounded-full bg-[#FFF9EA] px-3 py-1 text-xs font-semibold text-[#8A6A1F]">
                {{ count($licenseLabels) }} {{ str('license')->plural(count($licenseLabels)) }}
            </span>
        @endif
    </div>

    @if ($licensesFeedback)
        <div
            id="member-licenses-feedback"
            class="mb-5 rounded-lg border px-4 py-3 text-sm {{ $licensesFeedback['type'] === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-red-200 bg-red-50 text-red-800' }}"
            role="alert"
        >
            <p class="font-semibold {{ $licensesFeedback['type'] === 'success' ? 'text-emerald-900' : 'text-red-900' }}">
                {{ $licensesFeedback['type'] === 'success' ? 'Licenses saved' : 'Could not save licenses' }}
            </p>
            <p class="mt-1">{{ $licensesFeedback['message'] }}</p>
        </div>
    @elseif ($errors->has('insurance_licenses') || $errors->has('insurance_licenses.*'))
        <div id="member-licenses-feedback" class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
            <p class="font-semibold text-red-900">Could not save licenses</p>
            <ul class="mt-2 list-disc list-inside space-y-1">
                @foreach ($errors->get('insurance_licenses') as $error)
                    <li>{{ $error }}</li>
                @endforeach
                @foreach ($errors->get('insurance_licenses.*') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($isOwnProfile)
        <form method="post" action="{{ route('profile.licenses.update') }}" class="space-y-6" @submit="licensesSaving = true">
            @csrf
            @method('patch')

            @include('cfm.partials.licensed-jurisdictions-picker', [
                'locationOptions' => $locationOptions,
                'selectedKeys' => is_array($selectedLicenses) ? $selectedLicenses : [],
                'inputName' => 'insurance_licenses',
                'title' => 'Licensed jurisdictions',
                'description' => 'Check each state or province where you are licensed. You can select multiple regions across different countries.',
                'sectionClass' => 'rounded-lg border border-slate-200 bg-slate-50/80 p-4',
                'labelClass' => 'text-sm font-semibold text-[#0B1F3A]',
            ])

            @if ($licenseLabels !== [])
                <div class="rounded-lg border border-[#C8A24A]/30 bg-[#FFF9EA]/60 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#8A6A1F]">Currently saved</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($licenseLabels as $label)
                            <span class="rounded-full border border-[#C8A24A]/40 bg-white px-3 py-1 text-xs font-semibold text-[#0B1F3A]">{{ $label }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="flex flex-wrap items-center gap-4">
                <button
                    type="submit"
                    class="inline-flex items-center rounded-md border border-transparent bg-[#0B1F3A] px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-[#132F55] focus:outline-none focus:ring-2 focus:ring-[#C8A24A] focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                    :disabled="licensesSaving"
                >
                    <span x-show="! licensesSaving">Save Licenses</span>
                    <span x-show="licensesSaving" x-cloak>Saving…</span>
                </button>
            </div>
        </form>
    @else
        @if ($licenseLabels === [])
            <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-600">
                No insurance licenses recorded for this member.
            </div>
        @else
            <div class="flex flex-wrap gap-2">
                @foreach ($licenseLabels as $label)
                    <span class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-sm font-semibold text-[#0B1F3A] shadow-sm">{{ $label }}</span>
                @endforeach
            </div>
        @endif
    @endif
</section>
