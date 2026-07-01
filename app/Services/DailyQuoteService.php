<?php

namespace App\Services;

use App\Models\DailyQuote;
use Carbon\CarbonInterface;

class DailyQuoteService
{
    /**
     * @return array{quote: string, author: string|null}|null
     */
    public function forDate(?CarbonInterface $date = null): ?array
    {
        $quotes = DailyQuote::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['quote', 'author']);

        if ($quotes->isEmpty()) {
            return null;
        }

        $index = ($date ?? now())->dayOfYear % $quotes->count();
        $selected = $quotes->get($index);

        return [
            'quote' => $selected->quote,
            'author' => $selected->author,
        ];
    }
}
