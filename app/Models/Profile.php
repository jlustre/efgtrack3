<?php

namespace App\Models;

use App\Services\ProfilePhotoService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'phone',
        'province',
        'city',
        'country',
        'timezone',
        'best_contact_time',
        'license_number',
        'efg_associate_id',
        'is_efg_active_associate',
        'recruited_at',
        'bio',
        'profile_photo_path',
    ];

    protected function casts(): array
    {
        return [
            'recruited_at' => 'date',
            'is_efg_active_associate' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (Profile $profile): void {
            app(ProfilePhotoService::class)->deleteStoredFile($profile->profile_photo_path);
        });
    }
}
