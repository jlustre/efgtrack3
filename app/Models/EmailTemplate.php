<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'key',
        'name',
        'subject',
        'body',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function renderSubject(array $tokens): string
    {
        return $this->render($this->subject, $tokens);
    }

    public function renderBody(array $tokens): string
    {
        $body = $this->body;

        if (! $this->containsHtmlTags($body)) {
            return nl2br($this->render($body, $tokens));
        }

        return $this->render($body, $tokens);
    }

    private function render(string $content, array $tokens): string
    {
        foreach ($tokens as $token => $value) {
            $escaped = e($value);

            $content = str_replace('{{ '.$token.' }}', $escaped, $content);
            $content = str_replace('{{'.$token.'}}', $escaped, $content);
        }

        return $content;
    }

    private function containsHtmlTags(string $content): bool
    {
        return $content !== strip_tags($content);
    }
}
