<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApprenticeshipStep extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function bookingEventTypes(): HasMany
    {
        return $this->hasMany(BookingEventType::class, 'linked_apprenticeship_step_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'related_apprenticeship_step_id');
    }
}
