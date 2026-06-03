@php
    $profileFeedback = session('profile_feedback');
@endphp

@if ($profileFeedback)
    <div
        id="cfm-portal-profile-feedback"
        class="mb-6 rounded-xl border px-4 py-3 text-sm {{ $profileFeedback['type'] === 'success' ? 'border-green-500/30 bg-green-900/20 text-green-300' : 'border-red-500/30 bg-red-900/20 text-red-300' }}"
        role="alert"
    >
        <p class="font-semibold {{ $profileFeedback['type'] === 'success' ? 'text-green-200' : 'text-red-200' }}">
            {{ $profileFeedback['type'] === 'success' ? 'Profile saved' : 'Could not save profile' }}
        </p>
        <p class="mt-1">{{ $profileFeedback['message'] }}</p>
    </div>
@endif

@if ($errors->any() && ! $profileFeedback)
    <div id="cfm-portal-profile-feedback" class="mb-6 rounded-xl border border-red-500/30 bg-red-900/20 px-4 py-3 text-sm text-red-300" role="alert">
        <p class="font-semibold text-red-200">Could not save profile</p>
        <ul class="mt-2 list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
