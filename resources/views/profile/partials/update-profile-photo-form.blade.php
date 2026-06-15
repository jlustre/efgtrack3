<section x-data="profilePhotoUpload(@js($user->profilePhotoUrl()), @js($user->initials()))">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Profile Photo') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Upload a square photo (JPEG, PNG, or WebP, up to 2 MB). It appears in the top bar, your profile header, and team views.') }}
        </p>
    </header>

    <div class="mt-6 flex flex-col gap-6 sm:flex-row sm:items-start">
        <div class="flex flex-col items-center gap-2">
            <div class="relative h-24 w-24 overflow-hidden rounded-full border border-[#C8A24A]/50 bg-[#0B1F3A]">
                <img
                    x-show="previewUrl && ! imageFailed"
                    x-cloak
                    :src="previewUrl"
                    alt="Profile photo preview"
                    class="h-24 w-24 object-cover"
                    x-on:error="onImageError()"
                >
                <span
                    x-show="! previewUrl || imageFailed"
                    x-cloak
                    x-text="initials"
                    class="flex h-24 w-24 items-center justify-center text-xl font-bold text-[#C8A24A]"
                ></span>
            </div>
            <p class="text-xs text-slate-500">Preview</p>
        </div>

        <div class="min-w-0 flex-1 space-y-4">
            <form
                method="post"
                action="{{ route('profile.photo.update') }}"
                enctype="multipart/form-data"
                class="space-y-4"
            >
                @csrf

                <div>
                    <x-input-label for="profile_photo" :value="__('Choose Photo')" />
                    <input
                        id="profile_photo"
                        name="photo"
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        class="mt-1 block w-full text-sm text-slate-600 file:mr-4 file:rounded-md file:border-0 file:bg-[#0B1F3A] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-[#C8A24A] hover:file:bg-[#132F55]"
                        x-ref="photoInput"
                        x-on:change="onFileSelected($event)"
                    >
                    <x-input-error :messages="$errors->getBag('profilePhoto')->get('photo')" class="mt-2" />
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <x-primary-button>{{ __('Upload Photo') }}</x-primary-button>
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
            </form>

            @if ($user->profile?->profile_photo_path)
                <form
                    method="post"
                    action="{{ route('profile.photo.destroy') }}"
                    onsubmit="return confirm('Remove your profile photo?');"
                >
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-700">
                        {{ __('Remove Photo') }}
                    </button>
                </form>
            @endif
        </div>
    </div>
</section>
