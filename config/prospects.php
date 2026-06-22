<?php

return [
    'visibility_presets' => [
        'private' => 'Private (owner only)',
        'cfm' => 'Shared with CFM',
        'sponsor' => 'Shared with Sponsor',
        'manager' => 'Shared with Manager',
        'team' => 'Shared with Team',
        'user' => 'Shared with User',
    ],

    'conversion_types' => [
        'associate' => 'Associate',
        'client' => 'Client',
        'inactive' => 'Inactive',
    ],

    'funnel_types' => [
        'insurance' => 'Insurance Prospect',
        'recruiting' => 'Recruiting Prospect',
        'both' => 'Insurance & Recruiting',
    ],

    'fna_statuses' => [
        'not_started' => 'Not Started',
        'scheduled' => 'Scheduled',
        'completed' => 'Completed',
        'declined' => 'Declined',
    ],

    'activity_types' => [
        'phone_call' => 'Phone Call',
        'text_message' => 'Text Message',
        'email' => 'Email',
        'zoom_meeting' => 'Zoom Meeting',
        'in_person_meeting' => 'In-Person Meeting',
        'presentation' => 'Presentation',
        'follow_up' => 'Follow-Up',
        'policy_review' => 'Policy Review',
        'recruitment_meeting' => 'Recruitment Meeting',
        'financial_review' => 'Financial Review',
        'referral_request' => 'Referral Request',
    ],

    'stage_automations' => [
        'application-submitted' => [
            'followup_type' => 'underwriting_check',
            'priority' => 'high',
            'offset_days' => 3,
            'notes' => 'Confirm underwriting documents received.',
        ],
        'presentation-completed' => [
            'followup_type' => 'post_presentation',
            'priority' => 'high',
            'offset_days' => 2,
            'notes' => 'Schedule follow-up after presentation.',
        ],
    ],

    'goal_metrics' => [
        'contacts' => 'Contacts',
        'appointments' => 'Appointments',
        'presentations' => 'Presentations',
        'applications' => 'Applications',
        'recruits' => 'Recruits',
        'new_prospects' => 'New Prospects',
    ],

    'contacted_outcomes' => [
        'Connected',
        'Booked appointment',
        'Contact made',
        'Spoke with prospect',
        'Registered',
    ],

    'activity_log_summary_metrics' => [
        'phone_calls_attempted' => [
            'label' => 'Phone Calls Attempted',
            'short_label' => 'Calls Attempted',
            'description' => 'Outbound phone call attempts logged on your prospects.',
            'accent' => 'navy',
        ],
        'contacted' => [
            'label' => 'Contacted',
            'short_label' => 'Contacted',
            'description' => 'Successful contacts where the prospect was reached.',
            'accent' => 'gold',
        ],
        'invitation_success' => [
            'label' => 'Invitation Success',
            'short_label' => 'Invitations',
            'description' => 'Prospects who reached invitation-sent or registration milestones.',
            'accent' => 'cyan',
        ],
        'presentations' => [
            'label' => 'Presentations',
            'short_label' => 'Presentations',
            'description' => 'Online or in-person presentations and meetings held.',
            'accent' => 'violet',
        ],
        'fna_filled' => [
            'label' => 'FNA Filled',
            'short_label' => 'FNA Filled',
            'description' => 'Financial needs analyses completed or marked complete.',
            'accent' => 'emerald',
        ],
        'became_client' => [
            'label' => 'Became Client',
            'short_label' => 'Clients',
            'description' => 'Prospects converted to clients.',
            'accent' => 'amber',
        ],
        'became_associate' => [
            'label' => 'Became Associate',
            'short_label' => 'Associates',
            'description' => 'Prospects converted to active associates.',
            'accent' => 'slate',
        ],
    ],

    'analytics_chart_colors' => [
        'primary' => '#C8A24A',
        'secondary' => '#0B1F3A',
        'accent' => '#8A6A1F',
        'track' => '#E2E8F0',
    ],

    'follow_up_engine_rules' => [
        'no_contact_7d' => [
            'title' => 'No contact in 7+ days',
            'followup_type' => 'no_contact_7d',
            'priority' => 'medium',
            'days_threshold' => 7,
            'create_task' => true,
            'task_title' => 'Reach out to inactive prospect',
            'notes' => 'Prospect has had no contact for over 7 days.',
        ],
        'hot_inactive_3d' => [
            'title' => 'Hot prospect inactive 3+ days',
            'followup_type' => 'hot_inactive_3d',
            'priority' => 'high',
            'days_threshold' => 3,
            'create_task' => false,
            'notes' => 'Hot prospect has had no recent activity.',
        ],
        'presentation_no_followup' => [
            'title' => 'Presentation with no follow-up',
            'followup_type' => 'presentation_no_followup',
            'priority' => 'high',
            'days_threshold' => 2,
            'stage_slug' => 'presentation-completed',
            'create_task' => true,
            'task_title' => 'Schedule post-presentation follow-up',
            'notes' => 'Presentation attended but no follow-up communication logged.',
        ],
        'registration_incomplete' => [
            'title' => 'Registration link not completed',
            'followup_type' => 'registration_incomplete',
            'priority' => 'high',
            'days_threshold' => 5,
            'stage_slug' => 'registration-link-sent',
            'create_task' => true,
            'task_title' => 'Follow up on registration',
            'notes' => 'Prospect has not completed registration after link was sent.',
        ],
        'application_stalled' => [
            'title' => 'Application stalled',
            'followup_type' => 'application_stalled',
            'priority' => 'urgent',
            'days_threshold' => 14,
            'stage_slug' => 'application-submitted',
            'create_task' => true,
            'task_title' => 'Review stalled application',
            'notes' => 'Application submitted but no progress for 14+ days.',
        ],
    ],

    'ai_coach_rules' => [
        'presentation_no_followup' => [
            'title' => 'Presentation with no follow-up',
            'priority' => 'high',
            'days_threshold' => 2,
            'stage_slug' => 'presentation-completed',
            'message' => 'Presentation attended but no follow-up logged recently.',
            'suggested_action' => 'schedule_followup',
            'suggested_due_offset_days' => 0,
        ],
        'hot_inactive_3d' => [
            'title' => 'Hot prospect inactive',
            'priority' => 'high',
            'days_threshold' => 3,
            'message' => 'Hot prospect has had no recent activity — prioritize contact today.',
            'suggested_action' => 'log_call',
            'suggested_due_offset_days' => 0,
        ],
        'no_contact_7d' => [
            'title' => 'No contact in 7+ days',
            'priority' => 'medium',
            'days_threshold' => 7,
            'message' => 'Prospect has had no contact for over 7 days.',
            'suggested_action' => 'log_call',
            'suggested_due_offset_days' => 0,
        ],
        'registration_incomplete' => [
            'title' => 'Registration link not completed',
            'priority' => 'high',
            'days_threshold' => 5,
            'stage_slug' => 'registration-link-sent',
            'message' => 'Prospect has not completed registration after link was sent.',
            'suggested_action' => 'escalate',
            'suggested_due_offset_days' => 1,
        ],
        'application_stalled' => [
            'title' => 'Application stalled',
            'priority' => 'high',
            'days_threshold' => 14,
            'stage_slug' => 'application-submitted',
            'message' => 'Application submitted but no progress for 14+ days.',
            'suggested_action' => 'escalate',
            'suggested_due_offset_days' => 0,
        ],
        'overdue_followup' => [
            'title' => 'Overdue follow-up',
            'priority' => 'high',
            'message' => 'A scheduled follow-up is overdue — act today.',
            'suggested_action' => 'act_today',
            'suggested_due_offset_days' => 0,
        ],
    ],

    'import_columns' => [
        'first_name',
        'last_name',
        'email',
        'phone',
        'city',
        'source',
        'funnel_type',
    ],

    'export_columns' => [
        'id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'city',
        'state',
        'funnel_type',
        'stage',
        'interest_level',
        'source',
        'status',
        'created_at',
    ],
];
