<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class BpEmployee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'pe_employee_id',
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
        'hire_date',
        'hired_at',
        'hired_by',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'hire_date' => 'date',
            'hired_at' => 'datetime',
            'is_efg_active_associate' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function peEmployee(): BelongsTo
    {
        return $this->belongsTo(PeEmployee::class);
    }

    public function hiredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hired_by');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(BpEmpAddress::class);
    }

    public function phones(): HasMany
    {
        return $this->hasMany(BpEmpPhone::class);
    }

    public function jobData(): HasOne
    {
        return $this->hasOne(BpJobData::class);
    }

    public function taxData(): HasOne
    {
        return $this->hasOne(BpEmpTaxData::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(BpEmpDocument::class);
    }

    public function credentials(): HasMany
    {
        return $this->hasMany(BpEmpCredential::class);
    }
}
