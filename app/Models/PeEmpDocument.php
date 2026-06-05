<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PeEmpDocument extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'pe_employee_id',
        'document_type',
        'file_path',
        'original_filename',
        'mime_type',
        'status',
        'uploaded_at',
        'expires_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function peEmployee(): BelongsTo
    {
        return $this->belongsTo(PeEmployee::class);
    }
}
