<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProspectFunnelSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAdditionalPipelineStages();

        $insuranceFunnelId = $this->upsertFunnel(
            key: 'insurance',
            name: 'Insurance Funnel',
            description: 'Default insurance sales pipeline from lead to client.',
            sortOrder: 10,
            isDefault: true,
        );

        $recruitingFunnelId = $this->upsertFunnel(
            key: 'recruiting',
            name: 'Recruiting Funnel',
            description: 'Associate recruiting pipeline from prospect to active associate.',
            sortOrder: 20,
        );

        $this->seedFunnelStages($insuranceFunnelId, [
            ['name' => 'New Lead', 'slug' => 'new-lead'],
            ['name' => 'Contact Attempted', 'slug' => 'contact-attempted'],
            ['name' => 'Contact Made', 'slug' => 'contacted'],
            ['name' => 'Discovery Call', 'slug' => 'discovery-call'],
            ['name' => 'Financial Review', 'slug' => 'financial-review'],
            ['name' => 'Solution Presented', 'slug' => 'solution-presented'],
            ['name' => 'Application Submitted', 'slug' => 'application-submitted', 'auto_task_template' => [
                'follow_up' => [
                    'followup_type' => 'underwriting_check',
                    'priority' => 'high',
                    'offset_days' => 3,
                    'notes' => 'Confirm underwriting documents received.',
                ],
            ]],
            ['name' => 'Underwriting', 'slug' => 'underwriting'],
            ['name' => 'Policy Issued', 'slug' => 'policy-issued'],
            ['name' => 'Client', 'slug' => 'became-client', 'is_terminal' => true],
            ['name' => 'Referral Partner', 'slug' => 'referral-partner', 'is_terminal' => true],
        ]);

        $this->seedFunnelStages($recruitingFunnelId, [
            ['name' => 'Prospect Added', 'slug' => 'new-lead'],
            ['name' => 'Invitation Sent', 'slug' => 'invitation-sent'],
            ['name' => 'Follow-Up', 'slug' => 'follow-up-needed'],
            ['name' => 'Presentation Scheduled', 'slug' => 'appointment-scheduled'],
            ['name' => 'Presentation Attended', 'slug' => 'presentation-completed', 'auto_task_template' => [
                'follow_up' => [
                    'followup_type' => 'post_presentation',
                    'priority' => 'high',
                    'offset_days' => 2,
                    'notes' => 'Schedule follow-up after presentation.',
                ],
            ]],
            ['name' => 'Opportunity Review', 'slug' => 'opportunity-review'],
            ['name' => 'Decision Pending', 'slug' => 'pending-approval'],
            ['name' => 'Registration Link Sent', 'slug' => 'registration-link-sent'],
            ['name' => 'Registered', 'slug' => 'registered'],
            ['name' => 'Licensing Started', 'slug' => 'application-started'],
            ['name' => 'Active Associate', 'slug' => 'became-associate', 'is_terminal' => true],
        ]);
    }

    private function seedAdditionalPipelineStages(): void
    {
        $stages = [
            'Contact Attempted',
            'Discovery Call',
            'Financial Review',
            'Solution Presented',
            'Underwriting',
            'Policy Issued',
            'Referral Partner',
            'Invitation Sent',
            'Opportunity Review',
            'Registration Link Sent',
            'Registered',
        ];

        $existingMax = (int) DB::table('pipeline_stages')->whereNull('user_id')->max('sort_order');

        foreach ($stages as $index => $name) {
            DB::table('pipeline_stages')->updateOrInsert(
                ['user_id' => null, 'slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'sort_order' => $existingMax + (($index + 1) * 10),
                    'is_terminal' => in_array($name, ['Referral Partner', 'Registered'], true),
                    'is_active' => true,
                    'deleted_at' => null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    private function upsertFunnel(string $key, string $name, string $description, int $sortOrder, bool $isDefault = false): int
    {
        DB::table('prospect_funnels')->updateOrInsert(
            ['key' => $key],
            [
                'user_id' => null,
                'name' => $name,
                'description' => $description,
                'is_default' => $isDefault,
                'is_active' => true,
                'sort_order' => $sortOrder,
                'deleted_at' => null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return (int) DB::table('prospect_funnels')->where('key', $key)->value('id');
    }

    /**
     * @param  list<array{name: string, slug: string, is_terminal?: bool, auto_task_template?: array<string, mixed>|null}>  $stages
     */
    private function seedFunnelStages(int $funnelId, array $stages): void
    {
        foreach ($stages as $index => $stage) {
            $pipelineStageId = DB::table('pipeline_stages')
                ->whereNull('user_id')
                ->where('slug', $stage['slug'])
                ->value('id');

            DB::table('prospect_funnel_stages')->updateOrInsert(
                ['prospect_funnel_id' => $funnelId, 'slug' => Str::slug($stage['name'])],
                [
                    'pipeline_stage_id' => $pipelineStageId,
                    'name' => $stage['name'],
                    'sort_order' => ($index + 1) * 10,
                    'conversion_weight' => max(0, 100 - ($index * 8)),
                    'is_terminal' => $stage['is_terminal'] ?? false,
                    'auto_task_template' => isset($stage['auto_task_template'])
                        ? json_encode($stage['auto_task_template'])
                        : null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
