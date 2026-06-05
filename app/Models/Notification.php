<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;

class Notification extends DatabaseNotification
{
    use SoftDeletes;

    protected $fillable = [
        'id',
        'notification_type_id',
        'trigger_id',
        'sender_type',
        'sender_user_id',
        'recipients',
        'notification_template',
        'action_link',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
    ];

    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'notification_type_id' => 'integer',
            'trigger_id' => 'integer',
            'sender_user_id' => 'integer',
            'recipients' => 'array',
            'notification_template' => 'array',
            'action_link' => 'array',
        ]);
    }

    public function notificationType(): BelongsTo
    {
        return $this->belongsTo(NotificationType::class);
    }

    public function trigger(): BelongsTo
    {
        return $this->belongsTo(NotificationTrigger::class, 'trigger_id');
    }

    public function senderUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    protected static function booted(): void
    {
        static::creating(function (Notification $notification): void {
            if (blank($notification->id)) {
                $notification->id = (string) Str::uuid();
            }
        });
    }
}
