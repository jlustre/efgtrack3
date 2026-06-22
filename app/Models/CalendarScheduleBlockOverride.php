<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalendarScheduleBlockOverride extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'block_date' => 'date',
            'is_all_day' => 'boolean',
            'is_blocked' => 'boolean',
            'is_shared' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function typeLabel(): string
    {
        return CalendarScheduleBlock::TYPES[$this->block_type]['label'] ?? ucfirst($this->block_type);
    }

    public function typeColor(): string
    {
        return CalendarScheduleBlock::TYPES[$this->block_type]['color'] ?? '#94A3B8';
    }

    public function displayLabel(): string
    {
        return $this->label ?: $this->typeLabel();
    }
}
