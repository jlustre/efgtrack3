<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BpEmpTaxData extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'bp_employee_id',
        'pe_tax_data_id',
        'tax_id_type',
        'tax_id_last_four',
        'filing_status',
        'exemptions',
        'additional_withholding',
        'w4_signed_at',
    ];

    protected function casts(): array
    {
        return [
            'w4_signed_at' => 'datetime',
            'additional_withholding' => 'decimal:2',
        ];
    }

    public function bpEmployee(): BelongsTo
    {
        return $this->belongsTo(BpEmployee::class);
    }
}
