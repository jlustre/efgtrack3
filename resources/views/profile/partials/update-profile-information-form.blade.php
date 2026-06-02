<section>
    <header>
        <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Profile Details</p>
        <h2 class="mt-1 text-xl font-semibold text-[#0B1F3A]">
            {{ __('Member Information') }}
        </h2>

        <p class="mt-2 text-sm text-slate-600">
            Keep your contact, licensing, and short member bio current for your sponsor, mentor, and leadership team.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

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
                <x-input-label for="license_number" :value="__('License Number')" />
                <x-text-input id="license_number" name="license_number" type="text" class="mt-1 block w-full" :value="old('license_number', $user->profile?->license_number)" />
                <x-input-error class="mt-2" :messages="$errors->get('license_number')" />
            </div>

            <div>
                <x-input-label for="efg_associate_id" :value="__('EFG Associate ID')" />
                <x-text-input id="efg_associate_id" name="efg_associate_id" type="text" class="mt-1 block w-full" :value="old('efg_associate_id', $user->profile?->efg_associate_id)" />
                <x-input-error class="mt-2" :messages="$errors->get('efg_associate_id')" />
            </div>

            <div>
                <x-input-label for="city" :value="__('City')" />
                <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $user->profile?->city)" />
                <x-input-error class="mt-2" :messages="$errors->get('city')" />
            </div>

            <div>
                <x-input-label for="province" :value="__('Province / State')" />
                <x-text-input id="province" name="province" type="text" class="mt-1 block w-full" :value="old('province', $user->profile?->province)" />
                <x-input-error class="mt-2" :messages="$errors->get('province')" />
            </div>
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

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save Profile') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
