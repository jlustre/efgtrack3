<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BpJobData extends Model
{
    use SoftDeletes;

    protected $table = 'bp_job_data';

    protected $fillable = [
        'bp_employee_id',
        'pe_job_data_id',
        'rank_id',
        'team_id',
        'sponsor_id',
        'mentor_id',
        'job_title',
        'start_date',
        'department',
        'employment_type',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
        ];
    }

    public function bpEmployee(): BelongsTo
    {
        return $this->belongsTo(BpEmployee::class);
    }
}
