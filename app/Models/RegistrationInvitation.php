<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class RegistrationInvitation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'sponsor_id',
        'accepted_by',
        'code',
        'email',
        'role_name',
        'max_uses',
        'uses_count',
        'expires_at',
        'accepted_at',
        'last_emailed_at',
        'revoked_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
            'last_emailed_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sponsor_id');
    }

    public function acceptedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    public static function generateCode(): string
    {
        do {
            $code = Str::upper(Str::random(12));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    public function invitationUrl(): string
    {
        return route('register.invitation', $this->code);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->whereNull('revoked_at')
            ->whereNull('accepted_at')
            ->whereColumn('uses_count', '<', 'max_uses')
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeActiveForEmail(Builder $query, string $email): Builder
    {
        return $query
            ->active()
            ->where('email', strtolower($email));
    }

    public function isAvailable(): bool
    {
        return $this->revoked_at === null
            && $this->accepted_at === null
            && ($this->expires_at === null || $this->expires_at->isFuture())
            && $this->uses_count < $this->max_uses
            && (bool) $this->sponsor?->is_active;
    }
}
