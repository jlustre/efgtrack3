<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BpEmpPhone extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'bp_employee_id',
        'pe_phone_id',
        'type',
        'phone_number',
        'extension',
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
