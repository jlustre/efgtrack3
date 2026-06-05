<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PeEmpCredential extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'pe_employee_id',
        'credential_type',
        'credential_number',
        'issuing_authority',
        'jurisdiction_country_id',
        'jurisdiction_state_id',
        'issued_at',
        'expires_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'expires_at' => 'date',
        ];
    }

    public function peEmployee(): BelongsTo
    {
        return $this->belongsTo(PeEmployee::class);
    }
}
