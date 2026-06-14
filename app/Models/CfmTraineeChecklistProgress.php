<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CfmTraineeChecklistProgress extends Model
{
    protected $table = 'cfm_trainee_checklist_progress';

    protected $fillable = [
        'mentor_assignment_id',
        'cfm_trainee_checklist_item_id',
        'status',
        'completed_at',
        'completed_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(MentorAssignment::class, 'mentor_assignment_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(CfmTraineeChecklistItem::class, 'cfm_trainee_checklist_item_id');
    }

    public function completedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
