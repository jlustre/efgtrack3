<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class PeEmployee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'date_of_birth',
        'license_number',
        'efg_associate_id',
        'efg_invite_link',
        'is_efg_active_associate',
        'bio',
        'profile_photo_path',
        'best_contact_time',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'is_efg_active_associate' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(PeEmpAddress::class);
    }

    public function phones(): HasMany
    {
        return $this->hasMany(PeEmpPhone::class);
    }

    public function jobData(): HasOne
    {
        return $this->hasOne(PeJobData::class);
    }

    public function taxData(): HasOne
    {
        return $this->hasOne(PeEmpTaxData::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PeEmpDocument::class);
    }

    public function credentials(): HasMany
    {
        return $this->hasMany(PeEmpCredential::class);
    }

    public function bpEmployee(): HasOne
    {
        return $this->hasOne(BpEmployee::class);
    }
}
