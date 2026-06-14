<?php

namespace App\Models;

use App\Services\ProfilePhotoService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'phone',
        'city',
        'country_id',
        'state_province_id',
        'timezone_id',
        'best_contact_time',
        'license_number',
        'efg_associate_id',
        'efg_invite_link',
        'is_efg_active_associate',
        'recruited_at',
        'bio',
        'profile_photo_path',
    ];

    protected function casts(): array
    {
        return [
            'country_id' => 'integer',
            'state_province_id' => 'integer',
            'timezone_id' => 'integer',
            'recruited_at' => 'date',
            'is_efg_active_associate' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function countryRecord(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function stateProvince(): BelongsTo
    {
        return $this->belongsTo(StateProvince::class, 'state_province_id');
    }

    public function timezoneRecord(): BelongsTo
    {
        return $this->belongsTo(Timezone::class, 'timezone_id');
    }

    protected function country(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->countryRecord?->name);
    }

    protected function province(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->stateProvince?->name);
    }

    protected function timezone(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->timezoneRecord?->code ?? $this->timezoneRecord?->name);
    }

    protected static function booted(): void
    {
        static::deleting(function (Profile $profile): void {
            app(ProfilePhotoService::class)->deleteStoredFile($profile->profile_photo_path);
        });
    }
}
