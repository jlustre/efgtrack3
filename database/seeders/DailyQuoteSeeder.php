<?php

namespace Database\Seeders;

use App\Models\DailyQuote;
use Illuminate\Database\Seeder;

class DailyQuoteSeeder extends Seeder
{
    public function run(): void
    {
        $quotes = [
            ['quote' => 'Leadership is not about being in charge. It is about taking care of those in your charge.', 'author' => 'Simon Sinek'],
            ['quote' => 'Success is the sum of small efforts, repeated day in and day out.', 'author' => 'Robert Collier'],
            ['quote' => 'The best way to predict the future is to create it.', 'author' => 'Peter Drucker'],
            ['quote' => 'Discipline is the bridge between goals and accomplishment.', 'author' => 'Jim Rohn'],
            ['quote' => 'Your network is your net worth, but your service is your legacy.', 'author' => 'EFG Leadership'],
            ['quote' => 'Consistency beats intensity when you are building a lasting career.', 'author' => 'EFG Leadership'],
            ['quote' => 'Every conversation with a prospect is an opportunity to serve with integrity.', 'author' => 'EFG Leadership'],
            ['quote' => 'Mentorship multiplies growth — invest in others and your impact compounds.', 'author' => 'EFG Leadership'],
            ['quote' => 'Small daily improvements lead to staggering long-term results.', 'author' => 'Robin Sharma'],
            ['quote' => 'Excellence is not an act, but a habit.', 'author' => 'Aristotle'],
            ['quote' => 'The only way to do great work is to love what you do.', 'author' => 'Steve Jobs'],
            ['quote' => 'Opportunities don\'t happen. You create them.', 'author' => 'Chris Grosser'],
        ];

        foreach ($quotes as $index => $entry) {
            DailyQuote::query()->updateOrCreate(
                ['quote' => $entry['quote']],
                [
                    'author' => $entry['author'],
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ],
            );
        }
    }
}
