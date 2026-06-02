<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnboardingStep extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'sort_order',
        'responsible_parties',
        'notified_parties',
        'is_active',
        'is_required',
        'country',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_required' => 'boolean',
        ];
    }

    public function scopeApplicableToCountry(Builder $query, ?string $country): Builder
    {
        return $query->where(function (Builder $query) use ($country): void {
            $query->whereNull('country');

            if (filled($country)) {
                $query->orWhere('country', $country);
            }
        });
    }
}
