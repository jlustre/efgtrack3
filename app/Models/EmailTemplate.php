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
