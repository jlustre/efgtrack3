<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FieldApprenticeshipProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programId = DB::table('apprenticeship_programs')->updateOrInsert(
            ['name' => 'Field Apprenticeship Program'],
            [
                'description' => 'Structured FAP checklist for new members to learn the field process with sponsor and CFM guidance.',
                'is_active' => true,
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $programId = DB::table('apprenticeship_programs')
            ->where('name', 'Field Apprenticeship Program')
            ->value('id');

        $steps = [
            [
                'title' => 'FAP Orientation With Sponsor And CFM',
                'description' => 'Review the purpose of FAP, expected timeline, meeting cadence, apprentice responsibilities, and approval requirements.',
            ],
            [
                'title' => 'Complete Field Readiness Review',
                'description' => 'Confirm professional profile, communication standards, calendar availability, technology setup, and readiness for supervised field activity.',
            ],
            [
                'title' => 'Observe First Client Conversation',
                'description' => 'Attend or observe a qualified client conversation led by the sponsor or CFM and record learning notes.',
            ],
            [
                'title' => 'Practice Needs Analysis Conversation',
                'description' => 'Role-play the discovery and needs analysis conversation with the CFM or sponsor.',
            ],
            [
                'title' => 'Review Product And Solution Positioning',
                'description' => 'Review core solution categories, suitability mindset, client-first language, and when to escalate questions.',
            ],
            [
                'title' => 'Complete Compliance And Documentation Walkthrough',
                'description' => 'Review documentation expectations, disclosure standards, privacy awareness, and compliant follow-up practices.',
            ],
            [
                'title' => 'Attend Team Training Or Field Huddle',
                'description' => 'Attend a live team training, huddle, or webinar and capture action items.',
            ],
            [
                'title' => 'Prepare First Prospect List',
                'description' => 'Build an initial prospect list, segment warm market contacts, and review outreach language with mentor support.',
            ],
            [
                'title' => 'Complete Supervised Outreach Session',
                'description' => 'Complete an outreach or appointment-setting session with sponsor or CFM coaching.',
            ],
            [
                'title' => 'Co-Host A Client Appointment',
                'description' => 'Participate in a client appointment with the CFM or sponsor and complete a post-meeting debrief.',
            ],
            [
                'title' => 'Complete Follow-Up And Service Review',
                'description' => 'Practice post-meeting follow-up, client service expectations, next-step communication, and CRM or tracking updates.',
            ],
            [
                'title' => 'Review Licensing And Field Activity Alignment',
                'description' => 'Confirm what field activities are appropriate based on the apprentice licensing status and local requirements.',
            ],
            [
                'title' => 'Submit FAP Completion Review',
                'description' => 'Submit completion notes for CFM and agency owner review, including readiness, strengths, and development needs.',
            ],
            [
                'title' => 'Receive FAP Approval',
                'description' => 'Agency owner or authorized reviewer approves completion and confirms next growth path.',
            ],
        ];

        $responsibleParties = [
            'FAP Orientation With Sponsor And CFM' => 'Self, SP, CFM',
            'Complete Field Readiness Review' => 'Self, SP, CFM',
            'Observe First Client Conversation' => 'Self, SP, CFM',
            'Practice Needs Analysis Conversation' => 'Self, SP, CFM',
            'Review Product And Solution Positioning' => 'Self, SP, CFM',
            'Complete Compliance And Documentation Walkthrough' => 'Self, SP, CFM',
            'Attend Team Training Or Field Huddle' => 'Self, SP, TL',
            'Prepare First Prospect List' => 'Self, SP, CFM',
            'Complete Supervised Outreach Session' => 'Self, SP, CFM',
            'Co-Host A Client Appointment' => 'Self, SP, CFM',
            'Complete Follow-Up And Service Review' => 'Self, SP, CFM',
            'Review Licensing And Field Activity Alignment' => 'Self, SP, CFM, AO',
            'Submit FAP Completion Review' => 'Self, SP, CFM',
            'Receive FAP Approval' => 'SP, AO, CFM',
        ];

        $notifiedParties = [
            'FAP Orientation With Sponsor And CFM' => 'SP, CFM',
            'Complete Field Readiness Review' => 'SP, CFM',
            'Observe First Client Conversation' => 'SP, CFM',
            'Practice Needs Analysis Conversation' => 'SP, CFM',
            'Review Product And Solution Positioning' => 'SP, CFM',
            'Complete Compliance And Documentation Walkthrough' => 'SP, CFM',
            'Attend Team Training Or Field Huddle' => 'SP, TL',
            'Prepare First Prospect List' => 'SP, CFM',
            'Complete Supervised Outreach Session' => 'SP, CFM',
            'Co-Host A Client Appointment' => 'SP, CFM',
            'Complete Follow-Up And Service Review' => 'SP, CFM',
            'Review Licensing And Field Activity Alignment' => 'SP, CFM, AO',
            'Submit FAP Completion Review' => 'SP, CFM, AO',
            'Receive FAP Approval' => 'SP, CFM, AO',
        ];

        foreach ($steps as $index => $step) {
            DB::table('apprenticeship_steps')->updateOrInsert(
                [
                    'apprenticeship_program_id' => $programId,
                    'title' => $step['title'],
                ],
                [
                    'description' => $step['description'],
                    'sort_order' => ($index + 1) * 10,
                    'responsible_parties' => $responsibleParties[$step['title']] ?? 'Self',
                    'notified_parties' => $notifiedParties[$step['title']] ?? null,
                    'is_active' => true,
                    'deleted_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
