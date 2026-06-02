<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingModule extends Model
{
    protected $guarded = [];

    public function calendarEvents(): HasMany
    {
        return $this->hasMany(CalendarEvent::class, 'related_training_module_id');
    }
}
