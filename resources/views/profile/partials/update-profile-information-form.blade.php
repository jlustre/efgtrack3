@php
    $isOwnProfile = $isOwnProfile ?? true;
    $readonly = $profileContext['readonly'];
    $canViewSensitive = $profileContext['canViewSensitive'] ?? true;
    $locationOptions = $profileContext['locationOptions'];
    $contactTimes = $locationOptions['contactTimes'];
    $currentContactTime = old('best_contact_time', $user->profile?->best_contact_time ?? '');
    $contactTimeIsLegacy = filled($currentContactTime) && ! array_key_exists($currentContactTime, $contactTimes);
@endphp

<section>
    <p class="text-sm text-slate-600">
        @if ($isOwnProfile)
            Update your editable contact and licensing details. Team and login information below is managed by the system.
        @else
            Contact, licensing, and team information for this member. Editable fields are shown read-only.
        @endif
    </p>

    <dl class="mt-5 grid gap-4 rounded-lg border border-slate-200 bg-slate-50 p-4 sm:grid-cols-2 lg:grid-cols-3">
        <div>
            <dt class="text-xs font-semibold uppercase text-slate-500">Sponsor</dt>
            <dd class="mt-1 text-sm font-semibold text-[#0B1F3A]">{{ $readonly['sponsor'] }}</dd>
        </div>
        <div>
            <dt class="text-xs font-semibold uppercase text-slate-500">Agency Owner</dt>
            <dd class="mt-1 text-sm font-semibold text-[#0B1F3A]">{{ $readonly['agencyOwner'] }}</dd>
        </div>
        <div>
            <dt class="text-xs font-semibold uppercase text-slate-500">CFM / Mentor</dt>
            <dd class="mt-1 text-sm font-semibold text-[#0B1F3A]">{{ $readonly['mentor'] }}</dd>
        </div>
        <div>
            <dt class="text-xs font-semibold uppercase text-slate-500">Team</dt>
            <dd class="mt-1 text-sm font-semibold text-[#0B1F3A]">{{ $readonly['team'] }}</dd>
        </div>
        <div>
            <dt class="text-xs font-semibold uppercase text-slate-500">Rank</dt>
            <dd class="mt-1 text-sm font-semibold text-[#0B1F3A]">{{ $readonly['rank'] }}</dd>
        </div>
        <div>
            <dt class="text-xs font-semibold uppercase text-slate-500">Role</dt>
            <dd class="mt-1 text-sm font-semibold text-[#0B1F3A]">{{ $readonly['role'] }}</dd>
        </div>
        <div>
            <dt class="text-xs font-semibold uppercase text-slate-500">Joined</dt>
            <dd class="mt-1 text-sm font-semibold text-[#0B1F3A]">{{ $readonly['joinedAt'] }}</dd>
        </div>
        <div>
            <dt class="text-xs font-semibold uppercase text-slate-500">Last Login</dt>
            <dd class="mt-1 text-sm font-semibold text-[#0B1F3A]">{{ $readonly['lastLoginAt'] }}</dd>
        </div>
        @if ($canViewSensitive)
        <div>
            <dt class="text-xs font-semibold uppercase text-slate-500">Last IP</dt>
            <dd class="mt-1 text-sm font-semibold text-[#0B1F3A]">{{ $readonly['lastLoginIp'] }}</dd>
        </div>
        @endif
    </dl>

    @if (! $isOwnProfile)
        <dl class="mt-6 grid gap-4 rounded-lg border border-slate-200 bg-white p-4 sm:grid-cols-2">
            <div>
                <dt class="text-xs font-semibold uppercase text-slate-500">Name</dt>
                <dd class="mt-1 text-sm font-semibold text-[#0B1F3A]">{{ $user->name }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-slate-500">Email</dt>
                <dd class="mt-1 text-sm font-semibold text-[#0B1F3A]">{{ $user->email }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-slate-500">Phone</dt>
                <dd class="mt-1 text-sm font-semibold text-[#0B1F3A]">{{ $user->profile?->phone ?? 'Not added' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-slate-500">Best Contact Time</dt>
                <dd class="mt-1 text-sm font-semibold text-[#0B1F3A]">{{ $user->profile?->best_contact_time ?? 'Not added' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-slate-500">License Number</dt>
                <dd class="mt-1 text-sm font-semibold text-[#0B1F3A]">{{ $user->profile?->license_number ?? 'Not added' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-slate-500">EFG Associate ID</dt>
                <dd class="mt-1 text-sm font-semibold text-[#0B1F3A]">{{ $user->profile?->efg_associate_id ?? 'Not added' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-slate-500">City</dt>
                <dd class="mt-1 text-sm font-semibold text-[#0B1F3A]">{{ $user->profile?->city ?? 'Not added' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-slate-500">Country</dt>
                <dd class="mt-1 text-sm font-semibold text-[#0B1F3A]">{{ $user->profile?->country ?? 'Not added' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-slate-500">Province / State</dt>
                <dd class="mt-1 text-sm font-semibold text-[#0B1F3A]">{{ $user->profile?->province ?? 'Not added' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-slate-500">Timezone</dt>
                <dd class="mt-1 text-sm font-semibold text-[#0B1F3A]">{{ $user->profile?->timezone ?? 'Not added' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-xs font-semibold uppercase text-slate-500">Member Bio</dt>
                <dd class="mt-1 text-sm text-[#0B1F3A]">{{ $user->profile?->bio ?? 'Not added' }}</dd>
            </div>
        </dl>
    @else
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    @php
        $profileFeedback = session('profile_feedback');
    @endphp

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" @submit="submitProfileForm()">
        @csrf
        @method('patch')

        @if ($profileFeedback && ($profileFeedback['type'] ?? '') === 'error')
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                <p class="font-semibold text-red-900">Could not save profile</p>
                <p class="mt-1">{{ $profileFeedback['message'] }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                <p class="font-semibold text-red-900">Please fix the following</p>
                <ul class="mt-2 list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>

            <div>
                <x-input-label for="phone" :value="__('Phone')" />
                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->profile?->phone)" autocomplete="tel" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>

            <div>
                <x-input-label for="best_contact_time" value="Best Contact Time" />
                <select id="best_contact_time" name="best_contact_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                    <option value="">Select best time to reach you</option>
                    @if ($contactTimeIsLegacy)
                        <option value="{{ $currentContactTime }}" selected>{{ $currentContactTime }}</option>
                    @endif
                    @foreach ($contactTimes as $value => $label)
                        <option value="{{ $value }}" @selected($currentContactTime === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('best_contact_time')" />
            </div>

            <div>
                <x-input-label for="license_number" :value="__('License Number')" />
                <x-text-input id="license_number" name="license_number" type="text" class="mt-1 block w-full" :value="old('license_number', $user->profile?->license_number)" />
                <x-input-error class="mt-2" :messages="$errors->get('license_number')" />
            </div>

            <div>
<<<<<<< HEAD
=======
                <x-input-label for="efg_associate_id" :value="__('EFG Associate ID')" />
                <x-text-input id="efg_associate_id" name="efg_associate_id" type="text" class="mt-1 block w-full" :value="old('efg_associate_id', $user->profile?->efg_associate_id)" />
                <x-input-error class="mt-2" :messages="$errors->get('efg_associate_id')" />
            </div>

            <div>
                <x-input-label for="efg_invite_link" :value="__('EFG Invite Link')" />
                <x-text-input id="efg_invite_link" name="efg_invite_link" type="url" class="mt-1 block w-full" :value="old('efg_invite_link', $user->profile?->efg_invite_link)" placeholder="https://..." />
                <x-input-error class="mt-2" :messages="$errors->get('efg_invite_link')" />
            </div>

            <div>
>>>>>>> 2ae99211b388cde4b56062c1cfbbc9ca81c523b0
                <x-input-label for="city" :value="__('City')" />
                <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $user->profile?->city)" />
                <x-input-error class="mt-2" :messages="$errors->get('city')" />
            </div>

            @include('partials.profile-location-selects', [
                'locationOptions' => $locationOptions,
                'countryId' => $user->profile?->country_id,
                'stateProvinceId' => $user->profile?->state_province_id,
                'timezoneId' => $user->profile?->timezone_id,
            ])
        </div>

        <div>
            <x-input-label for="bio" :value="__('Member Bio')" />
            <textarea
                id="bio"
                name="bio"
                rows="4"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]"
                placeholder="Share your goals, specialty, or mentorship focus."
            >{{ old('bio', $user->profile?->bio) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div>
                <p class="mt-2 text-sm text-gray-800">
                    {{ __('Your email address is unverified.') }}

                    <button form="send-verification" class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#C8A24A] focus:ring-offset-2">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 text-sm font-medium text-green-600">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            </div>
        @endif

        <div class="flex flex-wrap items-center gap-4">
            <button
                type="submit"
                class="inline-flex items-center rounded-md border border-transparent bg-[#0B1F3A] px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-[#132F55] focus:bg-[#132F55] focus:outline-none focus:ring-2 focus:ring-[#C8A24A] focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                :disabled="profileSaving"
            >
                <span x-show="! profileSaving">Save Profile</span>
                <span x-show="profileSaving" x-cloak>Saving…</span>
            </button>
        </div>
    </form>
    @endif
</section>
