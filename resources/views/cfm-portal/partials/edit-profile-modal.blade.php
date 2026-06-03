@if ($portal['canEditProfile'])
    @php
        $locationOptions = $portal['locationOptions'];
        $editForm = $portal['editForm'];
        $timezoneOptions = $locationOptions['timezones'];
        $currentTimezone = $editForm['timezone'] ?? '';
        $currentProvince = $editForm['province'] ?? '';
        $currentCountry = $editForm['country'] ?? '';
        $selectedLicensed = $editForm['licensed_jurisdictions'] ?? [];
        $provinceOptions = $locationOptions['provincesByCountry'][$currentCountry] ?? [];
        $provinceIsLegacy = filled($currentProvince) && ! array_key_exists($currentProvince, $provinceOptions) && ! in_array($currentProvince, $provinceOptions, true);
        $timezoneIsLegacy = filled($currentTimezone) && ! array_key_exists($currentTimezone, $timezoneOptions);
        $profileFeedback = session('profile_feedback');
        $inputClass = 'mt-1 w-full bg-gray-800 border rounded-xl px-4 py-2 text-gray-200 focus:border-amber-500 focus:outline-none';
        $inputErrorClass = 'border-red-500/60';
        $inputOkClass = 'border-gray-700';
    @endphp

    <div
        x-show="showEditProfileModal"
        x-cloak
        class="fixed inset-0 z-[60] overflow-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
        @keydown.escape.window="showEditProfileModal = false"
    >
        <div class="bg-gray-900 border border-gray-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl max-h-[90vh] overflow-y-auto" @click.stop>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-xl font-bold text-white">Edit CFM Profile</h3>
                    <p class="text-xs text-gray-500 mt-1">Update your contact details and mentor information</p>
                </div>
                <button type="button" @click="showEditProfileModal = false" class="text-gray-400 hover:text-white text-2xl leading-none" :disabled="profileSaving">&times;</button>
            </div>

            <form
                method="POST"
                action="{{ route('cfm.portal.profile.update') }}"
                class="space-y-3"
                @submit="submitProfileForm()"
            >
                @csrf
                @method('PATCH')

                @if ($profileFeedback && ($profileFeedback['type'] ?? '') === 'error')
                    <div class="rounded-xl border border-red-500/30 bg-red-900/20 px-4 py-3 text-sm text-red-300" role="alert">
                        <p class="font-semibold text-red-200">Could not save profile</p>
                        <p class="mt-1">{{ $profileFeedback['message'] }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="rounded-xl border border-red-500/30 bg-red-900/20 px-4 py-3 text-sm text-red-300" role="alert">
                        <p class="font-semibold text-red-200">Please fix the following</p>
                        <ul class="mt-2 list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <label for="edit-phone" class="text-xs font-medium text-gray-400">Phone</label>
                    <input
                        id="edit-phone"
                        type="text"
                        name="phone"
                        value="{{ $editForm['phone'] }}"
                        class="{{ $inputClass }} {{ $errors->has('phone') ? $inputErrorClass : $inputOkClass }}"
                    >
                    @error('phone')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="edit-city" class="text-xs font-medium text-gray-400">City</label>
                    <input
                        id="edit-city"
                        type="text"
                        name="city"
                        value="{{ $editForm['city'] }}"
                        class="{{ $inputClass }} {{ $errors->has('city') ? $inputErrorClass : $inputOkClass }}"
                    >
                    @error('city')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="edit-country" class="text-xs font-medium text-gray-400">Country</label>
                    <select
                        id="edit-country"
                        name="country"
                        x-model="editCountry"
                        @change="onCountryChange()"
                        class="{{ $inputClass }} {{ $errors->has('country') ? $inputErrorClass : $inputOkClass }}"
                    >
                        <option value="">Select country</option>
                        @foreach ($locationOptions['countries'] as $country)
                            <option value="{{ $country }}" @selected($currentCountry === $country)>{{ $country }}</option>
                        @endforeach
                    </select>
                    @error('country')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="edit-province" class="text-xs font-medium text-gray-400">Province / State</label>
                    <select
                        id="edit-province"
                        name="province"
                        x-model="editProvince"
                        class="{{ $inputClass }} {{ $errors->has('province') ? $inputErrorClass : $inputOkClass }}"
                    >
                        <option value="">Select province / state</option>
                        @if ($provinceIsLegacy)
                            <option value="{{ $currentProvince }}" selected>{{ $currentProvince }}</option>
                        @endif
                        <template x-for="(label, value) in editProvinceOptions" :key="value">
                            <option :value="value" x-text="label"></option>
                        </template>
                    </select>
                    @error('province')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="edit-timezone" class="text-xs font-medium text-gray-400">Timezone</label>
                    <select
                        id="edit-timezone"
                        name="timezone"
                        class="{{ $inputClass }} {{ $errors->has('timezone') ? $inputErrorClass : $inputOkClass }}"
                    >
                        <option value="">Select timezone</option>
                        @if ($timezoneIsLegacy)
                            <option value="{{ $currentTimezone }}" selected>{{ $currentTimezone }}</option>
                        @endif
                        @foreach ($timezoneOptions as $timezoneValue => $timezoneLabel)
                            <option value="{{ $timezoneValue }}" @selected($currentTimezone === $timezoneValue)>{{ $timezoneLabel }}</option>
                        @endforeach
                    </select>
                    @error('timezone')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                @include('cfm.partials.licensed-jurisdictions-picker', [
                    'locationOptions' => $locationOptions,
                    'selectedKeys' => $selectedLicensed,
                ])

                <div>
                    <label for="edit-languages" class="text-xs font-medium text-gray-400">Languages</label>
                    <input
                        id="edit-languages"
                        type="text"
                        name="languages"
                        value="{{ $editForm['languages'] }}"
                        placeholder="English, Spanish"
                        class="{{ $inputClass }} {{ $errors->has('languages') ? $inputErrorClass : $inputOkClass }}"
                    >
                    <p class="text-xs text-gray-500 mt-1">Separate multiple languages with commas.</p>
                    @error('languages')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="edit-specialties" class="text-xs font-medium text-gray-400">Specialties</label>
                    <input
                        id="edit-specialties"
                        type="text"
                        name="specialties"
                        value="{{ $editForm['specialties'] }}"
                        placeholder="Field Apprenticeship, Licensing"
                        class="{{ $inputClass }} {{ $errors->has('specialties') ? $inputErrorClass : $inputOkClass }}"
                    >
                    <p class="text-xs text-gray-500 mt-1">Separate multiple specialties with commas.</p>
                    @error('specialties')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="edit-mentor-bio" class="text-xs font-medium text-gray-400">Mentor bio</label>
                    <textarea
                        id="edit-mentor-bio"
                        name="mentor_bio"
                        rows="4"
                        placeholder="Share your mentoring approach and experience..."
                        class="{{ $inputClass }} {{ $errors->has('mentor_bio') ? $inputErrorClass : $inputOkClass }} text-white placeholder-gray-500"
                    >{{ $editForm['mentor_bio'] }}</textarea>
                    @error('mentor_bio')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-2 text-sm text-gray-300">
                    <input type="hidden" name="manual_unavailable" value="0">
                    <input
                        type="checkbox"
                        name="manual_unavailable"
                        value="1"
                        @checked($editForm['manual_unavailable'])
                        class="rounded border-gray-600 bg-gray-800 text-amber-500"
                    >
                    Mark me as temporarily unavailable for new assignments
                </label>
                @error('manual_unavailable')
                    <p class="text-xs text-red-400">{{ $message }}</p>
                @enderror

                <div class="flex gap-3 pt-2">
                    <button
                        type="button"
                        @click="showEditProfileModal = false"
                        class="flex-1 border border-gray-700 py-2.5 rounded-xl text-gray-300 hover:bg-gray-800 transition disabled:opacity-50"
                        :disabled="profileSaving"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="flex-1 bg-amber-600 text-black font-bold py-2.5 rounded-xl hover:bg-amber-500 transition disabled:opacity-60 disabled:cursor-not-allowed"
                        :disabled="profileSaving"
                    >
                        <span x-show="! profileSaving">Save Profile</span>
                        <span x-show="profileSaving" x-cloak>Saving…</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
