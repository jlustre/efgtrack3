<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PeEmpTaxData extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'pe_employee_id',
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

    public function peEmployee(): BelongsTo
    {
        return $this->belongsTo(PeEmployee::class);
    }
}
