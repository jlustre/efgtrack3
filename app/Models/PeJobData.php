<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PeJobData extends Model
{
    use SoftDeletes;

    protected $table = 'pe_job_data';

    protected $fillable = [
        'pe_employee_id',
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

    public function peEmployee(): BelongsTo
    {
        return $this->belongsTo(PeEmployee::class);
    }
}
