<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserTask extends Model
{
    public const CATEGORIES = [
        'Prospect Follow-Up',
        'Licensing',
        'Training',
        'CFM Mentorship',
        'Field Apprenticeship',
        'Rank Advancement',
        'Team Meeting',
        'Resource Review',
        'Personal',
        'Admin',
    ];

    protected $fillable = [
        'assigned_to_user_id',
        'created_by_user_id',
        'title',
        'description',
        'priority',
        'status',
        'category',
        'related_module',
        'related_person',
        'due_date',
        'progress',
        'reminder',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'completed_at' => 'datetime',
            'progress' => 'integer',
        ];
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(UserTaskChecklistItem::class)->orderBy('sort_order');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(UserTaskComment::class)->latest();
    }

    public function scopeOpenForUser($query, User $user)
    {
        return $query
            ->where('assigned_to_user_id', $user->id)
            ->whereNotIn('status', ['completed', 'cancelled']);
    }

    public function displayPriority(): string
    {
        return match ($this->priority) {
            'urgent' => 'Urgent',
            'high' => 'High',
            'low' => 'Low',
            default => 'Medium',
        };
    }

    public function displayStatus(): string
    {
        return match ($this->status) {
            'in_progress' => 'In Progress',
            'to_do' => 'To Do',
            'waiting' => 'Waiting',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'overdue' => 'Overdue',
            default => str($this->status)->replace('_', ' ')->title()->toString(),
        };
    }
}
