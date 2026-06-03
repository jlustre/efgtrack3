<?php

namespace App\Services;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfilePhotoService
{
    public const DISK = 'public';

    public function update(User $user, UploadedFile $file): Profile
    {
        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);
        $previousPath = $profile->profile_photo_path;

        $path = $file->store(
            'profile-photos/'.$user->id,
            self::DISK
        );

        $profile->forceFill(['profile_photo_path' => $path])->save();

        if ($previousPath && $previousPath !== $path) {
            $this->deleteStoredFile($previousPath);
        }

        return $profile->fresh();
    }

    public function delete(User $user): void
    {
        $profile = $user->profile;

        if (! $profile?->profile_photo_path) {
            return;
        }

        $this->deleteStoredFile($profile->profile_photo_path);

        $profile->forceFill(['profile_photo_path' => null])->save();
    }

    public function deleteStoredFile(?string $path): void
    {
        if (! $path) {
            return;
        }

        Storage::disk(self::DISK)->delete($path);

        $directory = Str::beforeLast($path, '/');
        if ($directory && $directory !== 'profile-photos' && empty(Storage::disk(self::DISK)->files($directory))) {
            Storage::disk(self::DISK)->deleteDirectory($directory);
        }
    }
}
