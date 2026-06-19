<div
    class="md:col-span-2 rounded-lg border border-slate-200 bg-slate-50 p-4"
    x-data="profilePhotoUpload(@js($user->profilePhotoUrl()), @js($user->initials()), {
        ajax: true,
        destroyUrl: @js(route('profile.photo.destroy')),
    })"
>
    <x-input-label for="completion_profile_photo" :value="__('Profile Photo')" />
    <p class="mt-1 text-xs text-slate-500">JPEG, PNG, or WebP up to 2 MB. Upload separately, then save the rest of your profile.</p>

    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-start">
        <div class="flex flex-col items-center gap-2">
            <div class="relative h-20 w-20 overflow-hidden rounded-full border border-[#C8A24A]/50 bg-[#0B1F3A]">
                <img
                    x-show="previewUrl && ! imageFailed"
                    x-cloak
                    :src="previewUrl"
                    alt="Profile photo preview"
                    class="h-20 w-20 object-cover"
                    x-on:error="onImageError()"
                >
                <span
                    x-show="! previewUrl || imageFailed"
                    x-cloak
                    x-text="initials"
                    class="flex h-20 w-20 items-center justify-center text-lg font-bold text-[#C8A24A]"
                ></span>
            </div>
            <p class="text-xs text-slate-500">Preview</p>
        </div>

        <div class="min-w-0 flex-1 space-y-3">
            <form
                method="post"
                action="{{ route('profile.photo.update') }}"
                enctype="multipart/form-data"
                class="space-y-3"
                x-on:submit.prevent="handlePhotoSubmit($event)"
            >
                @csrf
                <input type="hidden" name="redirect_to" value="dashboard">

                <input
                    id="completion_profile_photo"
                    name="photo"
                    type="file"
                    accept="image/jpeg,image/png,image/webp"
                    class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-md file:border-0 file:bg-[#0B1F3A] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-[#C8A24A] hover:file:bg-[#132F55]"
                    x-ref="photoInput"
                    x-on:change="onFileSelected($event)"
                >
                <x-input-error :messages="$errors->get('photo')" class="mt-2" />

                <div
                    x-show="feedback"
                    x-cloak
                    class="rounded-lg px-3 py-2 text-xs"
                    :class="feedbackType === 'success' ? 'border border-emerald-200 bg-emerald-50 text-emerald-800' : 'border border-red-200 bg-red-50 text-red-800'"
                    x-text="feedback"
                    :role="feedbackType === 'error' ? 'alert' : 'status'"
                    aria-live="polite"
                ></div>

                <div class="flex flex-wrap items-center gap-2">
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-md border border-[#0B1F3A] bg-[#0B1F3A] px-3 py-1.5 text-xs font-semibold uppercase tracking-wide text-white transition hover:bg-[#132F55] disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="uploading || removing"
                    >
                        <span x-show="! uploading">Upload Photo</span>
                        <span x-show="uploading" x-cloak>Uploading…</span>
                    </button>
                    <button
                        type="button"
                        class="text-xs font-medium text-slate-600 hover:text-[#0B1F3A]"
                        x-show="$refs.photoInput?.files?.length"
                        x-cloak
                        x-on:click="clearPreview()"
                    >
                        Clear selection
                    </button>
                </div>
            </form>

            <div x-show="hasStoredPhoto" x-cloak>
                <button
                    type="button"
                    class="text-xs font-semibold text-red-600 hover:text-red-700 disabled:cursor-not-allowed disabled:opacity-60"
                    x-on:click="removePhotoViaAjax()"
                    :disabled="uploading || removing"
                >
                    <span x-show="! removing">Remove Photo</span>
                    <span x-show="removing" x-cloak>Removing…</span>
                </button>
            </div>
        </div>
    </div>
</div>
