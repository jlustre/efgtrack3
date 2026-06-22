<?php

namespace Database\Seeders;

use App\Models\CfmEffectiveness\CfmFeedbackQuestion;
use App\Models\CfmEffectiveness\CfmRecognitionBadge;
use App\Models\CfmEffectiveness\CfmReviewCycle;
use Illuminate\Database\Seeder;

class CfmEffectivenessSeeder extends Seeder
{
    public function run(): void
    {
        $cycles = [
            ['code' => '30_day', 'name' => '30-Day Review', 'trigger_type' => '30_day', 'days_after_assignment' => 30, 'sort_order' => 10],
            ['code' => '60_day', 'name' => '60-Day Review', 'trigger_type' => '60_day', 'days_after_assignment' => 60, 'sort_order' => 20],
            ['code' => '90_day', 'name' => '90-Day Review', 'trigger_type' => '90_day', 'days_after_assignment' => 90, 'sort_order' => 30],
            ['code' => 'fap_completion', 'name' => 'FAP Completion', 'trigger_type' => 'fap_completion', 'days_after_assignment' => null, 'sort_order' => 40],
            ['code' => 'licensing_completion', 'name' => 'Licensing Completion', 'trigger_type' => 'licensing_completion', 'days_after_assignment' => null, 'sort_order' => 50],
            ['code' => 'promotion', 'name' => 'Promotion Achievement', 'trigger_type' => 'promotion', 'days_after_assignment' => null, 'sort_order' => 60],
            ['code' => 'ao_requested', 'name' => 'AO Requested Review', 'trigger_type' => 'ao_requested', 'days_after_assignment' => null, 'sort_order' => 70],
        ];

        foreach ($cycles as $cycle) {
            CfmReviewCycle::query()->updateOrCreate(['code' => $cycle['code']], $cycle);
        }

        $questions = [
            ['key' => 'explained_expectations', 'question' => 'Explained expectations clearly', 'sort_order' => 10],
            ['key' => 'responded_when_needed', 'question' => 'Responded when I needed help', 'sort_order' => 20],
            ['key' => 'followed_up_consistently', 'question' => 'Followed up consistently', 'sort_order' => 30],
            ['key' => 'useful_coaching', 'question' => 'Provided useful coaching', 'sort_order' => 40],
            ['key' => 'understood_efgtrack', 'question' => 'Helped me understand EFGTrack', 'sort_order' => 50],
            ['key' => 'helped_fap', 'question' => 'Helped me complete FAP', 'sort_order' => 60],
            ['key' => 'encouraged_progress', 'question' => 'Encouraged my progress', 'sort_order' => 70],
            ['key' => 'held_accountable', 'question' => 'Held me accountable professionally', 'sort_order' => 80],
            ['key' => 'constructive_feedback', 'question' => 'Provided constructive feedback', 'sort_order' => 90],
            ['key' => 'developed_confidence', 'question' => 'Helped me develop confidence', 'sort_order' => 100],
            ['key' => 'availability', 'question' => 'Made themselves available', 'sort_order' => 110],
            ['key' => 'demonstrated_leadership', 'question' => 'Demonstrated leadership', 'sort_order' => 120],
        ];

        foreach ($questions as $question) {
            CfmFeedbackQuestion::query()->updateOrCreate(['key' => $question['key']], $question);
        }

        $badges = [
            ['code' => 'mentor_of_month', 'name' => 'Mentor of the Month', 'description' => 'Top overall effectiveness for the month.', 'criteria_key' => 'overall_effectiveness', 'sort_order' => 10],
            ['code' => 'fap_champion', 'name' => 'FAP Champion', 'description' => 'Highest FAP completion rate among CFMs.', 'criteria_key' => 'fap_completion_rate', 'sort_order' => 20],
            ['code' => 'licensing_champion', 'name' => 'Licensing Champion', 'description' => 'Highest licensing completion rate.', 'criteria_key' => 'licensing_completion_rate', 'sort_order' => 30],
            ['code' => 'retention_champion', 'name' => 'Retention Champion', 'description' => 'Best trainee retention outcomes.', 'criteria_key' => 'retention_rate', 'sort_order' => 40],
            ['code' => 'leadership_builder', 'name' => 'Leadership Builder', 'description' => 'Strong promotion development results.', 'criteria_key' => 'promotion_development_score', 'sort_order' => 50],
            ['code' => 'fast_track_mentor', 'name' => 'Fast Track Mentor', 'description' => 'Second-highest overall effectiveness for the month.', 'criteria_key' => 'overall_effectiveness', 'sort_order' => 60],
            ['code' => 'top_coach', 'name' => 'Top Coach', 'description' => 'Excellent coaching activity and engagement.', 'criteria_key' => 'coaching_activity_score', 'sort_order' => 70],
            ['code' => 'rising_mentor', 'name' => 'Rising Mentor', 'description' => 'Most improved effectiveness score month-over-month.', 'criteria_key' => 'overall_effectiveness', 'sort_order' => 80],
        ];

        foreach ($badges as $badge) {
            CfmRecognitionBadge::query()->updateOrCreate(['code' => $badge['code']], $badge);
        }
    }
}
