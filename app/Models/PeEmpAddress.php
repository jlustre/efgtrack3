<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PeEmpAddress extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'pe_employee_id',
        'type',
        'address_line_1',
        'address_line_2',
        'city',
        'country_id',
        'state_province_id',
        'postal_code',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function peEmployee(): BelongsTo
    {
        return $this->belongsTo(PeEmployee::class);
    }
}
