<div
    x-data="profilePhotoUpload(@js($user->profilePhotoUrl()), @js($user->initials()))"
    class="rounded-lg border border-slate-200 bg-slate-50 p-4"
>
    <x-input-label for="completion_profile_photo" :value="__('Profile Photo')" />
    <p class="mt-1 text-xs text-slate-500">
        {{ __('Upload a square photo (JPEG, PNG, or WebP, up to 2 MB). It appears in the top bar, your profile header, and team views.') }}
    </p>

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
            <p class="text-xs text-slate-500">{{ __('Preview') }}</p>
        </div>

        <div class="min-w-0 flex-1 space-y-3">
            <input
                id="completion_profile_photo"
                name="photo"
                type="file"
                accept="image/jpeg,image/png,image/webp"
                class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-md file:border-0 file:bg-[#0B1F3A] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-[#C8A24A] hover:file:bg-[#132F55]"
                x-ref="photoInput"
                x-on:change="onFileSelected($event)"
            >
            <button
                type="button"
                class="text-sm font-medium text-slate-600 hover:text-[#0B1F3A]"
                x-show="$refs.photoInput?.files?.length"
                x-cloak
                x-on:click="clearPreview()"
            >
                {{ __('Clear selection') }}
            </button>
        </div>
    </div>

    <x-input-error :messages="$errors->get('photo')" class="mt-2" />
</div>
