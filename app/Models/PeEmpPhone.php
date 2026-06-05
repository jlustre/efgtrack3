<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PeEmpPhone extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'pe_employee_id',
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

    public function peEmployee(): BelongsTo
    {
        return $this->belongsTo(PeEmployee::class);
    }
}
