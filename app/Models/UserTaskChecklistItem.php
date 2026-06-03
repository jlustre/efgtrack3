<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTaskChecklistItem extends Model
{
    protected $fillable = [
        'user_task_id',
        'text',
        'is_done',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_done' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(UserTask::class, 'user_task_id');
    }
}
