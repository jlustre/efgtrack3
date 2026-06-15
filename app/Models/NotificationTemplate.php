<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationTemplate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'notification_trigger_id',
        'name',
        'subject',
        'body',
        'channels',
        'placeholders',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'notification_trigger_id' => 'integer',
            'channels' => 'array',
            'placeholders' => 'array',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function trigger(): BelongsTo
    {
        return $this->belongsTo(NotificationTrigger::class, 'notification_trigger_id');
    }

    public function snapshot(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'subject' => $this->subject,
            'body' => $this->body,
            'channels' => $this->channels,
            'placeholders' => $this->placeholders,
        ];
    }

    public function renderSubject(array $tokens): string
    {
        return $this->render($this->subject, $tokens);
    }

    public function renderBody(array $tokens): string
    {
        return $this->render($this->body, $tokens);
    }

    private function render(string $content, array $tokens): string
    {
        foreach ($tokens as $token => $value) {
            $content = str_replace('{{ '.$token.' }}', $value, $content);
            $content = str_replace('{{'.$token.'}}', $value, $content);
        }

        return $content;
    }
}
