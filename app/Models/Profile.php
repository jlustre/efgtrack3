<?php

namespace App\Models;

use App\Services\ProfileLocationService;
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

    public function getCountryAttribute(): ?string
    {
        if ($this->country_id === null) {
            return null;
        }

        return $this->relationLoaded('countryRecord')
            ? $this->countryRecord?->name
            : $this->countryRecord()->value('name');
    }

    public function getProvinceAttribute(): ?string
    {
        if ($this->state_province_id === null) {
            return null;
        }

        return $this->relationLoaded('stateProvince')
            ? $this->stateProvince?->name
            : $this->stateProvince()->value('name');
    }

    public function getTimezoneAttribute(): ?string
    {
        if ($this->timezone_id === null) {
            return null;
        }

        $record = $this->relationLoaded('timezoneRecord')
            ? $this->timezoneRecord
            : $this->timezoneRecord()->first(['code', 'name']);

        return $record?->code ?? $record?->name;
    }

    public function fill(array $attributes)
    {
        $attributes = app(ProfileLocationService::class)->mapAttributesForStorage(
            $attributes,
            $this->country_id,
        );

        return parent::fill($attributes);
    }

    protected static function booted(): void
    {
        static::deleting(function (Profile $profile): void {
            app(ProfilePhotoService::class)->deleteStoredFile($profile->profile_photo_path);
        });
    }
}
