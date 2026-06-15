<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $fillable = [
        'name',
        'location',
        'phone',
        'domain',
        'address_line_1',
        'address_line_2',
        'city',
        'state_province',
        'postal_code',
        'country',
        'description',
        'leadership',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'leadership' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function domainUrl(): string
    {
        $domain = trim($this->domain);

        if ($domain === '') {
            return '';
        }

        if (str_starts_with($domain, 'http://') || str_starts_with($domain, 'https://')) {
            return $domain;
        }

        return 'https://'.$domain;
    }

    public function formattedAddress(): ?string
    {
        $parts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->city,
            $this->state_province,
            $this->postal_code,
            $this->country,
        ], fn (?string $value) => filled($value));

        return $parts === [] ? null : implode(', ', $parts);
    }
}
