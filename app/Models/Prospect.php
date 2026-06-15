<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prospect extends Model
{
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'next_follow_up_at' => 'datetime',
            'last_contacted_at' => 'datetime',
            'appointment_at' => 'datetime',
            'conversion_at' => 'datetime',
            'archived_at' => 'datetime',
            'is_client' => 'boolean',
            'is_archived' => 'boolean',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(ProspectSource::class, 'prospect_source_id');
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class, 'pipeline_stage_id');
    }

    public function types(): BelongsToMany
    {
        return $this->belongsToMany(ProspectType::class, 'prospect_type_prospect')->withTimestamps();
    }

    public function interests(): BelongsToMany
    {
        return $this->belongsToMany(ProspectInterest::class, 'prospect_interest_prospect')->withTimestamps();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ProspectTag::class, 'prospect_tag_pivot')->withTimestamps();
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ProspectNote::class);
    }

    public function communications(): HasMany
    {
        return $this->hasMany(ProspectCommunication::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ProspectActivity::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(ProspectAppointment::class);
    }

    public function followups(): HasMany
    {
        return $this->hasMany(ProspectFollowUp::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(ProspectFile::class);
    }

    public function shares(): HasMany
    {
        return $this->hasMany(ProspectShare::class);
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(ProspectAccessLog::class);
    }

    public function conversions(): HasMany
    {
        return $this->hasMany(ProspectConversion::class);
    }
}
