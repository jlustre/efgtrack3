<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BpEmpAddress extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'bp_employee_id',
        'pe_address_id',
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

    public function bpEmployee(): BelongsTo
    {
        return $this->belongsTo(BpEmployee::class);
    }
}
