<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalendarCategory extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function eventTypes(): HasMany
    {
        return $this->hasMany(CalendarEventType::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(CalendarEvent::class);
    }
}
