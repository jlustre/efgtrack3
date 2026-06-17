<x-guest-layout>
    @php
        $timezones = $locationOptions['timezones'];
        $currentCountry = old('country');
        $currentProvince = old('province');
        $provinceOptions = $locationOptions['provincesByCountry'][$currentCountry] ?? [];
        $provinceIsLegacy = filled($currentProvince) && ! array_key_exists($currentProvince, $provinceOptions) && ! in_array($currentProvince, $provinceOptions, true);
    @endphp

    <div class="min-h-screen bg-[radial-gradient(circle_at_10%_20%,#111111,#000000)] px-4 py-8 text-slate-100 sm:px-6 lg:px-8">
        <div class="mx-auto flex min-h-[calc(100vh-4rem)] max-w-7xl items-center justify-center">
            <div class="w-full overflow-hidden rounded-[2rem] border border-[#D4AF37]/25 bg-black/80 shadow-[0_25px_45px_-12px_rgba(0,0,0,0.8),0_0_18px_rgba(212,175,55,0.22)]">
                <div class="grid bg-[#0b0b0c] lg:grid-cols-[0.95fr_1.1fr]">
                    <aside class="border-b border-[#D4AF37]/25 bg-gradient-to-br from-black to-[#101012] p-6 sm:p-8 lg:border-b-0 lg:border-r">
                        <div>
                            <a href="/" class="inline-block bg-gradient-to-br from-[#F5E7B2] via-[#D4AF37] to-[#B8860B] bg-clip-text text-3xl font-extrabold tracking-normal text-transparent">
                                EFG<span class="font-black">Track</span>.com
                            </a>
                            <div class="mt-2 border-l-2 border-[#D4AF37] pl-3 text-xs font-semibold uppercase tracking-[0.16em] text-[#D4AF37]">
                                Financial Intelligence &middot; Insurance Team Portal
                            </div>
                        </div>

                        <div class="mt-8 overflow-hidden rounded-3xl border border-[#D4AF37]/20 bg-[#070707] shadow-[0_18px_34px_-24px_rgba(212,175,55,0.55)]">
                            <img
                                src="{{ asset('images/authimage.jpg') }}"
                                alt="EFGTrack registration"
                                class="h-64 w-full object-cover object-top sm:h-80 lg:h-[22rem]"
                                style="-webkit-mask-image: linear-gradient(to bottom, black 0%, black 62%, rgba(0,0,0,0.3) 100%); mask-image: linear-gradient(to bottom, black 0%, black 62%, rgba(0,0,0,0.3) 100%);"
                            >
                        </div>

                        <div class="mt-8">
                            <h1 class="max-w-lg bg-gradient-to-r from-white to-[#E5C56A] bg-clip-text text-3xl font-bold leading-tight text-transparent sm:text-4xl">
                                Create Your EFGTrack Account
                            </h1>
                            <div class="mt-4 h-0.5 w-16 bg-gradient-to-r from-[#D4AF37] to-transparent"></div>
                            <p class="mt-5 max-w-md border-l-2 border-[#D4AF37]/50 pl-4 text-base leading-7 text-slate-300">
                                Start your onboarding, licensing, training, and field apprenticeship journey with your sponsor already verified.
                            </p>

                            <div class="mt-8 space-y-4 text-sm text-slate-200">
                                <div class="flex items-center gap-3">
                                    <span class="h-2 w-2 rounded-full bg-[#D4AF37] shadow-[0_0_8px_#D4AF37]"></span>
                                    Premium associate dashboard and training path
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="h-2 w-2 rounded-full bg-[#D4AF37] shadow-[0_0_8px_#D4AF37]"></span>
                                    Mentor-led field apprenticeship tracking
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="h-2 w-2 rounded-full bg-[#D4AF37] shadow-[0_0_8px_#D4AF37]"></span>
                                    Licensing, rank advancement, and team resources
                                </div>
                            </div>

                            <div class="mt-8 rounded-2xl border border-[#D4AF37]/25 bg-[#070707] p-5 shadow-[0_12px_28px_-20px_rgba(212,175,55,0.45)]" role="note">
                                <div class="text-xs font-semibold uppercase tracking-[0.14em] text-[#D4AF37]">Important Before Registering</div>
                                <p class="mt-3 text-sm leading-6 text-slate-300">
                                    You must already be registered with Experior Financial Group before completing EFGTrack registration. If you have not finished your Experior enrollment, ask your sponsor how to proceed. Your Experior sponsor must be the same person as your EFGTrack sponsor&mdash;<span class="font-semibold text-white">{{ $invitation->sponsor->name }}</span>. If this is not the person who invited you, stop here and ask the correct sponsor to send their invitation link.
                                </p>
                            </div>

                        </div>
                    </aside>

                    <main class="bg-[#0a0a0c] p-6 sm:p-8">
                        <div class="mb-8">
                            <h2 class="text-2xl font-semibold text-white sm:text-3xl">Agent Registration</h2>
                            <p class="mt-2 text-sm text-slate-400">Secure invitation access &middot; EFG associate verification required</p>
                        </div>

                        <form
                            method="POST"
                            action="{{ route('register') }}"
                            class="space-y-5"
                            x-data="{
                                editCountry: @js($currentCountry ?? ''),
                                editProvince: @js($currentProvince ?? ''),
                                editProvinces: @js($locationOptions['provincesByCountry']),
                                get editProvinceOptions() {
                                    return this.editProvinces[this.editCountry] || {};
                                },
                                onCountryChange() {
                                    const options = this.editProvinceOptions;
                                    if (this.editProvince && ! Object.prototype.hasOwnProperty.call(options, this.editProvince)) {
                                        this.editProvince = '';
                                    }
                                },
                            }"
                        >
                            @csrf

                            <input type="hidden" name="registration_code" value="{{ old('registration_code', $invitation->code) }}">

                            <div>
                                <label for="registration_code_display" class="block text-xs font-bold uppercase tracking-wide text-[#D4AF37]">Registration Code</label>
                                <input id="registration_code_display" type="text" value="{{ $invitation->code }}" disabled class="mt-2 block w-full rounded-2xl border border-[#2a2a2e] bg-[#131316] px-4 py-3 text-sm text-slate-200 shadow-sm">
                                <p class="mt-2 text-xs text-slate-400">Enter the portal only through the secure code provided by your sponsor or mentor.</p>
                                <x-input-error :messages="$errors->get('registration_code')" class="mt-2" />
                            </div>

                            <div>
                                <label for="sponsor_display" class="block text-xs font-bold uppercase tracking-wide text-[#D4AF37]">Sponsor</label>
                                <input id="sponsor_display" type="text" value="{{ $invitation->sponsor->name }}" disabled class="mt-2 block w-full rounded-2xl border border-[#2a2a2e] bg-[#131316] px-4 py-3 text-sm text-slate-200 shadow-sm">
                            </div>

                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="first_name" class="block text-xs font-bold uppercase tracking-wide text-[#D4AF37]">First Name</label>
                                    <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" required autofocus autocomplete="given-name" placeholder="James" class="mt-2 block w-full rounded-2xl border border-[#2a2a2e] bg-[#131316] px-4 py-3 text-sm text-slate-100 placeholder:text-slate-600 focus:border-[#D4AF37] focus:ring-[#D4AF37]">
                                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                                </div>

                                <div>
                                    <label for="last_name" class="block text-xs font-bold uppercase tracking-wide text-[#D4AF37]">Last Name</label>
                                    <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}" required autocomplete="family-name" placeholder="Carter" class="mt-2 block w-full rounded-2xl border border-[#2a2a2e] bg-[#131316] px-4 py-3 text-sm text-slate-100 placeholder:text-slate-600 focus:border-[#D4AF37] focus:ring-[#D4AF37]">
                                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                                </div>
                            </div>

                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="email" class="block text-xs font-bold uppercase tracking-wide text-[#D4AF37]">Email</label>
                                    <input id="email" name="email" type="email" value="{{ old('email', $invitation->email) }}" required autocomplete="username" placeholder="new.member@example.com" class="mt-2 block w-full rounded-2xl border border-[#2a2a2e] bg-[#131316] px-4 py-3 text-sm text-slate-100 placeholder:text-slate-600 focus:border-[#D4AF37] focus:ring-[#D4AF37]">
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                <div>
                                    <label for="efg_associate_id" class="block text-xs font-bold uppercase tracking-wide text-[#D4AF37]">EFG Associate ID</label>
                                    <input id="efg_associate_id" name="efg_associate_id" type="text" value="{{ old('efg_associate_id') }}" required autocomplete="off" placeholder="EFG-1001" class="mt-2 block w-full rounded-2xl border border-[#2a2a2e] bg-[#131316] px-4 py-3 text-sm text-slate-100 placeholder:text-slate-600 focus:border-[#D4AF37] focus:ring-[#D4AF37]">
                                    <p class="mt-2 text-xs text-slate-400">Only active Experior Financial Group associates may complete registration.</p>
                                    <x-input-error :messages="$errors->get('efg_associate_id')" class="mt-2" />
                                </div>
                            </div>

                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="password" class="block text-xs font-bold uppercase tracking-wide text-[#D4AF37]">Password</label>
                                    <input id="password" name="password" type="password" required autocomplete="new-password" placeholder="Create password" class="mt-2 block w-full rounded-2xl border border-[#2a2a2e] bg-[#131316] px-4 py-3 text-sm text-slate-100 placeholder:text-slate-600 focus:border-[#D4AF37] focus:ring-[#D4AF37]">
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wide text-[#D4AF37]">Confirm Password</label>
                                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" placeholder="Confirm password" class="mt-2 block w-full rounded-2xl border border-[#2a2a2e] bg-[#131316] px-4 py-3 text-sm text-slate-100 placeholder:text-slate-600 focus:border-[#D4AF37] focus:ring-[#D4AF37]">
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>

                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="country" class="block text-xs font-bold uppercase tracking-wide text-[#D4AF37]">Country</label>
                                    <select id="country" name="country" required x-model="editCountry" @change="onCountryChange()" class="mt-2 block w-full rounded-2xl border border-[#2a2a2e] bg-[#131316] px-4 py-3 text-sm text-slate-100 focus:border-[#D4AF37] focus:ring-[#D4AF37]">
                                        <option value="" disabled @selected(! old('country'))>Select jurisdiction</option>
                                        @foreach (['Canada', 'United States', 'Philippines', 'Mexico'] as $country)
                                            <option value="{{ $country }}" @selected(old('country') === $country)>{{ $country }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('country')" class="mt-2" />
                                </div>

                                <div>
                                    <label for="timezone" class="block text-xs font-bold uppercase tracking-wide text-[#D4AF37]">Timezone</label>
                                    <select id="timezone" name="timezone" required class="mt-2 block w-full rounded-2xl border border-[#2a2a2e] bg-[#131316] px-4 py-3 text-sm text-slate-100 focus:border-[#D4AF37] focus:ring-[#D4AF37]">
                                        <option value="" disabled @selected(! old('timezone'))>Select your local timezone</option>
                                        @foreach ($timezones as $timezoneValue => $timezoneLabel)
                                            <option value="{{ is_numeric($timezoneValue) ? $timezoneLabel : $timezoneValue }}" @selected(old('timezone') === (is_numeric($timezoneValue) ? $timezoneLabel : $timezoneValue))>{{ $timezoneLabel }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('timezone')" class="mt-2" />
                                </div>
                            </div>

                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="province" class="block text-xs font-bold uppercase tracking-wide text-[#D4AF37]">State / Province</label>
                                    <select
                                        id="province"
                                        name="province"
                                        x-model="editProvince"
                                        required
                                        class="mt-2 block w-full rounded-2xl border border-[#2a2a2e] bg-[#131316] px-4 py-3 text-sm text-slate-100 focus:border-[#D4AF37] focus:ring-[#D4AF37]"
                                    >
                                        <option value="" disabled @selected(! $currentProvince)>Select state / province</option>
                                        @if ($provinceIsLegacy)
                                            <option value="{{ $currentProvince }}" selected>{{ $currentProvince }}</option>
                                        @endif
                                        <template x-for="(label, value) in editProvinceOptions" :key="value">
                                            <option :value="value" x-text="label"></option>
                                        </template>
                                    </select>
                                    <x-input-error :messages="$errors->get('province')" class="mt-2" />
                                </div>

                                <div>
                                    <label for="city" class="block text-xs font-bold uppercase tracking-wide text-[#D4AF37]">City</label>
                                    <input id="city" name="city" type="text" value="{{ old('city') }}" required autocomplete="address-level2" placeholder="Vancouver / Manila / Mexico City" class="mt-2 block w-full rounded-2xl border border-[#2a2a2e] bg-[#131316] px-4 py-3 text-sm text-slate-100 placeholder:text-slate-600 focus:border-[#D4AF37] focus:ring-[#D4AF37]">
                                    <x-input-error :messages="$errors->get('city')" class="mt-2" />
                                </div>
                            </div>

                            <div class="space-y-3 rounded-2xl border border-[#D4AF37]/20 bg-[#131316] p-5">
                                <label class="flex gap-3 text-sm leading-6 text-slate-300">
                                    <input type="checkbox" name="sponsor_confirmed" value="1" class="mt-1 rounded border-[#D4AF37]/40 bg-[#0a0a0c] text-[#D4AF37] focus:ring-[#D4AF37]" required @checked(old('sponsor_confirmed'))>
                                    <span>I confirm that {{ $invitation->sponsor->name }} is the person sponsoring my EFGTrack registration.</span>
                                </label>
                                <x-input-error :messages="$errors->get('sponsor_confirmed')" class="mt-2" />

                                <label class="flex gap-3 text-sm leading-6 text-slate-300">
                                    <input type="checkbox" name="active_associate_confirmed" value="1" class="mt-1 rounded border-[#D4AF37]/40 bg-[#0a0a0c] text-[#D4AF37] focus:ring-[#D4AF37]" required @checked(old('active_associate_confirmed'))>
                                    <span>I confirm that I am an active associate of Experior Financial Group.</span>
                                </label>
                                <x-input-error :messages="$errors->get('active_associate_confirmed')" class="mt-2" />
                            </div>

                            <button type="submit" class="w-full rounded-full bg-gradient-to-r from-[#B8860B] via-[#D4AF37] to-[#F3D572] px-5 py-3 text-sm font-extrabold uppercase tracking-wider text-black shadow-lg transition hover:-translate-y-0.5 hover:shadow-[#D4AF37]/20">
                                Create Account
                            </button>

                            <div class="border-t border-[#D4AF37]/20 pt-5 text-center text-sm text-slate-400">
                                Already have an account?
                                <a href="{{ route('login') }}" class="font-semibold text-[#D4AF37] hover:text-[#F3D572] hover:underline">Sign in</a>
                            </div>
                        </form>
                    </main>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
