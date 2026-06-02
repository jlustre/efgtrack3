<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OnboardingStepSeeder extends Seeder
{
    public function run(): void
    {
        $steps = [
            [
                'title' => 'Complete Member Profile',
                'description' => 'Add contact details, location, phone number, time zone, EFG Associate ID, and basic profile information.',
                'is_required' => true,
                'country' => null,
            ],
            [
                'title' => 'Confirm Sponsor And Team Placement',
                'description' => 'Verify the sponsoring member, inherited team, starting FA rank, and first point of contact.',
                'is_required' => true,
                'country' => null,
            ],
            [
                'title' => 'Watch EFGTrack Welcome Orientation',
                'description' => 'Review the portal purpose, dashboard, trackers, resources, communications, and team visibility areas.',
                'is_required' => true,
                'country' => null,
            ],
            [
                'title' => 'Join Primary Team Communication Channels',
                'description' => 'Join the team chat, announcement channel, email list, calendar, and recurring Zoom meeting links.',
                'is_required' => true,
                'country' => null,
            ],
            [
                'title' => 'Schedule Sponsor Welcome Call',
                'description' => 'Book the first welcome call with the sponsor to review expectations, goals, availability, and next steps.',
                'is_required' => true,
                'country' => null,
            ],
            [
                'title' => 'Receive Certified Field Mentor Assignment',
                'description' => 'Confirm the assigned Certified Field Mentor who will guide the Field Apprenticeship Program.',
                'is_required' => true,
                'country' => null,
            ],
            [
                'title' => 'Complete Business Standards Review',
                'description' => 'Review conduct expectations, client-first standards, compliance awareness, and professional communication guidelines.',
                'is_required' => true,
                'country' => null,
            ],
            [
                'title' => 'Review Licensing Roadmap',
                'description' => 'Understand licensing requirements, province or state expectations, study timeline, exam steps, and submission workflow.',
                'is_required' => true,
                'country' => null,
            ],
            [
                'title' => 'Start Licensing Tracker',
                'description' => 'Open the licensing tracker, review required steps, and update the initial licensing status.',
                'is_required' => true,
                'country' => null,
            ],
            [
                'title' => 'Complete Starter Training Module',
                'description' => 'Finish the first training module covering Experior basics, field expectations, products, and client conversations.',
                'is_required' => true,
                'country' => null,
            ],
            [
                'title' => 'Review Resource Library',
                'description' => 'Open Documents, Videos, Recorded Webinars, and Zoom Links to understand where key support materials live.',
                'is_required' => false,
                'country' => null,
            ],
            [
                'title' => 'Begin Field Apprenticeship Program',
                'description' => 'Meet with the assigned CFM and review apprenticeship steps, mentor sessions, expectations, and approval workflow.',
                'is_required' => true,
                'country' => null,
            ],
            [
                'title' => 'Set First 30-Day Activity Goals',
                'description' => 'Define initial prospecting, training, licensing, mentorship, and team meeting goals with the sponsor or leader.',
                'is_required' => true,
                'country' => null,
            ],
            [
                'title' => 'Review Rank Advancement Path',
                'description' => 'Review FA through EP rank path, rank requirements, recognition milestones, and leadership development expectations.',
                'is_required' => false,
                'country' => null,
            ],
            [
                'title' => 'Attend First Team Event Or Training',
                'description' => 'Attend a live team event, webinar, huddle, or training session and record any follow-up tasks.',
                'is_required' => true,
                'country' => null,
            ],
            [
                'title' => 'Canada: Review Provincial Licensing Path',
                'description' => 'Confirm the applicable provincial licensing path, LLQP study expectations, provincial regulator, and exam scheduling requirements.',
                'is_required' => true,
                'country' => 'Canada',
            ],
            [
                'title' => 'United States: Review State Licensing Path',
                'description' => 'Confirm the applicable state licensing path, pre-licensing education requirements, state exam process, and carrier appointment expectations.',
                'is_required' => true,
                'country' => 'United States',
            ],
            [
                'title' => 'Philippines: Confirm Local Business Readiness',
                'description' => 'Review local market expectations, team communication rhythm, client conversation standards, and country-specific resource links.',
                'is_required' => true,
                'country' => 'Philippines',
            ],
            [
                'title' => 'Mexico: Confirm Local Business Readiness',
                'description' => 'Review local market expectations, team communication rhythm, client conversation standards, and country-specific resource links.',
                'is_required' => true,
                'country' => 'Mexico',
            ],
        ];

        $responsibleParties = [
            'Complete Member Profile' => 'Self',
            'Confirm Sponsor And Team Placement' => 'SP, AO, TL',
            'Watch EFGTrack Welcome Orientation' => 'Self, SP',
            'Join Primary Team Communication Channels' => 'Self, SP',
            'Schedule Sponsor Welcome Call' => 'Self, SP',
            'Receive Certified Field Mentor Assignment' => 'SP, AO',
            'Complete Business Standards Review' => 'Self, SP, TL',
            'Review Licensing Roadmap' => 'Self, SP, CFM',
            'Start Licensing Tracker' => 'Self, SP, CFM',
            'Complete Starter Training Module' => 'Self, SP, TR',
            'Review Resource Library' => 'Self, SP',
            'Begin Field Apprenticeship Program' => 'Self, SP, CFM',
            'Set First 30-Day Activity Goals' => 'Self, SP, TL',
            'Review Rank Advancement Path' => 'Self, SP, TL',
            'Attend First Team Event Or Training' => 'Self, SP, TL',
            'Canada: Review Provincial Licensing Path' => 'Self, SP, CFM',
            'United States: Review State Licensing Path' => 'Self, SP, CFM',
            'Philippines: Confirm Local Business Readiness' => 'Self, SP, TL',
            'Mexico: Confirm Local Business Readiness' => 'Self, SP, TL',
        ];

        $notifiedParties = [
            'Complete Member Profile' => 'SP',
            'Confirm Sponsor And Team Placement' => 'SP, AO, TL',
            'Watch EFGTrack Welcome Orientation' => 'SP',
            'Join Primary Team Communication Channels' => 'SP, TL',
            'Schedule Sponsor Welcome Call' => 'SP',
            'Receive Certified Field Mentor Assignment' => 'SP, CFM, AO',
            'Complete Business Standards Review' => 'SP, TL',
            'Review Licensing Roadmap' => 'SP, CFM',
            'Start Licensing Tracker' => 'SP, CFM',
            'Complete Starter Training Module' => 'SP, TR',
            'Review Resource Library' => 'SP',
            'Begin Field Apprenticeship Program' => 'SP, CFM',
            'Set First 30-Day Activity Goals' => 'SP, TL',
            'Review Rank Advancement Path' => 'SP, TL',
            'Attend First Team Event Or Training' => 'SP, TL',
            'Canada: Review Provincial Licensing Path' => 'SP, CFM',
            'United States: Review State Licensing Path' => 'SP, CFM',
            'Philippines: Confirm Local Business Readiness' => 'SP, TL',
            'Mexico: Confirm Local Business Readiness' => 'SP, TL',
        ];

        foreach ($steps as $index => $step) {
            DB::table('onboarding_steps')->updateOrInsert(
                ['title' => $step['title']],
                [
                    'description' => $step['description'],
                    'sort_order' => ($index + 1) * 10,
                    'responsible_parties' => $responsibleParties[$step['title']] ?? 'Self',
                    'notified_parties' => $notifiedParties[$step['title']] ?? null,
                    'is_active' => true,
                    'is_required' => $step['is_required'],
                    'country' => $step['country'],
                    'deleted_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
