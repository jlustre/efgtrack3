<?php

namespace App\Http\Controllers;

use App\Models\Prospect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProspectManagementController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $ownProspects = Prospect::query()->where('owner_id', $user->id);
        $activeProspects = (clone $ownProspects)->where('status', 'active')->where('is_archived', false);
        $sharedProspectIds = DB::table('prospect_shares')
            ->where('shared_with', $user->id)
            ->where('status', 'active')
            ->whereNull('revoked_at')
            ->where(function ($query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->pluck('prospect_id');

        $pipelineSummary = DB::table('pipeline_stages')
            ->leftJoin('prospects', function ($join) use ($user): void {
                $join->on('prospects.pipeline_stage_id', '=', 'pipeline_stages.id')
                    ->where('prospects.owner_id', '=', $user->id)
                    ->where('prospects.status', '=', 'active')
                    ->where('prospects.is_archived', '=', false)
                    ->whereNull('prospects.deleted_at');
            })
            ->whereNull('pipeline_stages.user_id')
            ->where('pipeline_stages.is_active', true)
            ->select([
                'pipeline_stages.id',
                'pipeline_stages.name',
                'pipeline_stages.slug',
                'pipeline_stages.sort_order',
                DB::raw('COUNT(prospects.id) as prospect_count'),
            ])
            ->groupBy('pipeline_stages.id', 'pipeline_stages.name', 'pipeline_stages.slug', 'pipeline_stages.sort_order')
            ->orderBy('pipeline_stages.sort_order')
            ->get();

        $followUpsDueToday = DB::table('prospect_followups')
            ->join('prospects', 'prospects.id', '=', 'prospect_followups.prospect_id')
            ->where('prospect_followups.assigned_user_id', $user->id)
            ->whereIn('prospect_followups.status', ['pending', 'overdue'])
            ->whereDate('prospect_followups.due_at', '<=', now()->toDateString())
            ->whereNull('prospect_followups.deleted_at')
            ->whereNull('prospects.deleted_at')
            ->orderBy('prospect_followups.due_at')
            ->limit(8)
            ->get([
                'prospect_followups.id',
                'prospect_followups.followup_type',
                'prospect_followups.priority',
                'prospect_followups.status',
                'prospect_followups.due_at',
                'prospects.first_name',
                'prospects.last_name',
                'prospects.interest_level',
            ]);

        $upcomingAppointments = DB::table('prospect_appointments')
            ->join('prospects', 'prospects.id', '=', 'prospect_appointments.prospect_id')
            ->leftJoin('appointment_types', 'appointment_types.id', '=', 'prospect_appointments.appointment_type_id')
            ->leftJoin('users as helpers', 'helpers.id', '=', 'prospect_appointments.assigned_helper_id')
            ->where('prospect_appointments.owner_id', $user->id)
            ->where('prospect_appointments.status', 'scheduled')
            ->where('prospect_appointments.scheduled_at', '>=', now())
            ->whereNull('prospect_appointments.deleted_at')
            ->whereNull('prospects.deleted_at')
            ->orderBy('prospect_appointments.scheduled_at')
            ->limit(6)
            ->get([
                'prospect_appointments.id',
                'prospect_appointments.scheduled_at',
                'prospect_appointments.purpose',
                'prospect_appointments.location_or_link',
                'appointment_types.name as appointment_type',
                'helpers.name as helper_name',
                'prospects.first_name',
                'prospects.last_name',
            ]);

        $hotProspects = (clone $activeProspects)
            ->with(['source:id,name', 'stage:id,name'])
            ->where('interest_level', 'hot')
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END")
            ->orderBy('next_follow_up_at')
            ->limit(6)
            ->get();

        $recentCommunications = DB::table('prospect_communications')
            ->join('prospects', 'prospects.id', '=', 'prospect_communications.prospect_id')
            ->leftJoin('communication_types', 'communication_types.id', '=', 'prospect_communications.communication_type_id')
            ->where('prospects.owner_id', $user->id)
            ->whereNull('prospect_communications.deleted_at')
            ->whereNull('prospects.deleted_at')
            ->orderByDesc('prospect_communications.contacted_at')
            ->limit(6)
            ->get([
                'prospect_communications.id',
                'prospect_communications.direction',
                'prospect_communications.outcome',
                'prospect_communications.next_action',
                'prospect_communications.contacted_at',
                'communication_types.name as communication_type',
                'prospects.first_name',
                'prospects.last_name',
            ]);

        $recentlyContactedProspects = (clone $activeProspects)
            ->whereNotNull('last_contacted_at')
            ->orderByDesc('last_contacted_at')
            ->limit(5)
            ->get(['id', 'first_name', 'last_name', 'last_contacted_at', 'next_follow_up_at', 'interest_level']);

        $prospectsTable = (clone $activeProspects)
            ->with(['source:id,name', 'stage:id,name'])
            ->latest()
            ->limit(12)
            ->get();

        $followUpsTable = DB::table('prospect_followups')
            ->join('prospects', 'prospects.id', '=', 'prospect_followups.prospect_id')
            ->where('prospect_followups.assigned_user_id', $user->id)
            ->whereNull('prospect_followups.deleted_at')
            ->whereNull('prospects.deleted_at')
            ->orderBy('prospect_followups.due_at')
            ->limit(12)
            ->get([
                'prospect_followups.id',
                'prospect_followups.followup_type',
                'prospect_followups.priority',
                'prospect_followups.status',
                'prospect_followups.due_at',
                'prospect_followups.notes',
                'prospects.first_name',
                'prospects.last_name',
                'prospects.email',
                'prospects.phone',
            ]);

        $appointmentsTable = DB::table('prospect_appointments')
            ->join('prospects', 'prospects.id', '=', 'prospect_appointments.prospect_id')
            ->leftJoin('appointment_types', 'appointment_types.id', '=', 'prospect_appointments.appointment_type_id')
            ->leftJoin('users as helpers', 'helpers.id', '=', 'prospect_appointments.assigned_helper_id')
            ->where('prospect_appointments.owner_id', $user->id)
            ->whereNull('prospect_appointments.deleted_at')
            ->whereNull('prospects.deleted_at')
            ->orderByDesc('prospect_appointments.scheduled_at')
            ->limit(12)
            ->get([
                'prospect_appointments.id',
                'prospect_appointments.scheduled_at',
                'prospect_appointments.purpose',
                'prospect_appointments.status',
                'prospect_appointments.location_or_link',
                'appointment_types.name as appointment_type',
                'helpers.name as helper_name',
                'prospects.first_name',
                'prospects.last_name',
            ]);

        $sharedWithMe = DB::table('prospect_shares')
            ->join('prospects', 'prospects.id', '=', 'prospect_shares.prospect_id')
            ->join('users as owners', 'owners.id', '=', 'prospect_shares.granted_by')
            ->leftJoin('prospect_share_permissions', 'prospect_share_permissions.id', '=', 'prospect_shares.prospect_share_permission_id')
            ->where('prospect_shares.shared_with', $user->id)
            ->where('prospect_shares.status', 'active')
            ->whereNull('prospect_shares.revoked_at')
            ->whereNull('prospect_shares.deleted_at')
            ->whereNull('prospects.deleted_at')
            ->where(function ($query): void {
                $query->whereNull('prospect_shares.expires_at')->orWhere('prospect_shares.expires_at', '>', now());
            })
            ->orderByDesc('prospect_shares.granted_at')
            ->limit(5)
            ->get([
                'prospect_shares.id',
                'prospect_shares.granted_at',
                'prospect_shares.expires_at',
                'prospects.first_name',
                'prospects.last_name',
                'owners.name as owner_name',
                'prospect_share_permissions.name as permission_name',
            ]);

        $sharedByMe = DB::table('prospect_shares')
            ->join('prospects', 'prospects.id', '=', 'prospect_shares.prospect_id')
            ->join('users as collaborators', 'collaborators.id', '=', 'prospect_shares.shared_with')
            ->leftJoin('prospect_share_permissions', 'prospect_share_permissions.id', '=', 'prospect_shares.prospect_share_permission_id')
            ->where('prospect_shares.granted_by', $user->id)
            ->where('prospect_shares.status', 'active')
            ->whereNull('prospect_shares.revoked_at')
            ->whereNull('prospect_shares.deleted_at')
            ->whereNull('prospects.deleted_at')
            ->orderByDesc('prospect_shares.granted_at')
            ->limit(5)
            ->get([
                'prospect_shares.id',
                'prospect_shares.granted_at',
                'prospect_shares.expires_at',
                'prospects.first_name',
                'prospects.last_name',
                'collaborators.name as collaborator_name',
                'prospect_share_permissions.name as permission_name',
            ]);

        $recentImport = DB::table('prospect_imports')
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->first();

        $importsTable = DB::table('prospect_imports')
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $sourcePerformance = DB::table('prospect_sources')
            ->leftJoin('prospects', function ($join) use ($user): void {
                $join->on('prospects.prospect_source_id', '=', 'prospect_sources.id')
                    ->where('prospects.owner_id', '=', $user->id)
                    ->whereNull('prospects.deleted_at');
            })
            ->where('prospect_sources.is_active', true)
            ->select(['prospect_sources.name', DB::raw('COUNT(prospects.id) as prospect_count')])
            ->groupBy('prospect_sources.id', 'prospect_sources.name')
            ->orderByDesc('prospect_count')
            ->limit(5)
            ->get();

        $totalProspects = (clone $ownProspects)->count();
        $convertedProspects = (clone $ownProspects)->whereNotNull('converted_to')->count();
        $allProspects = Prospect::query()
            ->with(['source:id,name', 'stage:id,name'])
            ->where('owner_id', $user->id)
            ->when($request->filled('prospect_search'), function ($query) use ($request): void {
                $search = trim((string) $request->string('prospect_search'));

                $query->where(function ($query) use ($search): void {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('prospect_status'), fn ($query) => $query->where('status', $request->string('prospect_status')))
            ->when($request->filled('prospect_stage'), fn ($query) => $query->where('pipeline_stage_id', $request->integer('prospect_stage')))
            ->when($request->filled('prospect_source'), fn ($query) => $query->where('prospect_source_id', $request->integer('prospect_source')))
            ->when($request->filled('prospect_interest'), fn ($query) => $query->where('interest_level', $request->string('prospect_interest')))
            ->orderByDesc('updated_at')
            ->paginate(10, ['*'], 'prospects_page')
            ->withQueryString();

        return view('team.prospects', [
            'stats' => [
                'total' => $totalProspects,
                'hot' => (clone $activeProspects)->where('interest_level', 'hot')->count(),
                'followups_due' => DB::table('prospect_followups')
                    ->where('assigned_user_id', $user->id)
                    ->whereIn('status', ['pending', 'overdue'])
                    ->whereDate('due_at', '<=', now()->toDateString())
                    ->whereNull('deleted_at')
                    ->count(),
                'shared_with_me' => $sharedProspectIds->count(),
                'appointments' => DB::table('prospect_appointments')
                    ->where('owner_id', $user->id)
                    ->where('status', 'scheduled')
                    ->where('scheduled_at', '>=', now())
                    ->count(),
                'pipeline_stages' => DB::table('pipeline_stages')->whereNull('user_id')->where('is_active', true)->count(),
                'converted' => $convertedProspects,
                'conversion_rate' => $totalProspects > 0 ? round(($convertedProspects / $totalProspects) * 100) : 0,
                'shared_by_me' => DB::table('prospect_shares')->where('granted_by', $user->id)->where('status', 'active')->whereNull('revoked_at')->whereNull('deleted_at')->count(),
            ],
            'pipelineStages' => DB::table('pipeline_stages')->whereNull('user_id')->where('is_active', true)->orderBy('sort_order')->get(),
            'pipelineSummary' => $pipelineSummary,
            'allProspects' => $allProspects,
            'prospectStatuses' => (clone $ownProspects)->select('status')->distinct()->orderBy('status')->pluck('status'),
            'prospectSources' => DB::table('prospect_sources')->where('is_active', true)->orderBy('sort_order')->get(),
            'prospectTypes' => DB::table('prospect_types')->where('is_active', true)->orderBy('sort_order')->limit(8)->get(),
            'interests' => DB::table('prospect_interests')->where('is_active', true)->orderBy('sort_order')->limit(8)->get(),
            'followUpsDueToday' => $followUpsDueToday,
            'upcomingAppointments' => $upcomingAppointments,
            'hotProspects' => $hotProspects,
            'recentCommunications' => $recentCommunications,
            'recentlyContactedProspects' => $recentlyContactedProspects,
            'prospectsTable' => $prospectsTable,
            'followUpsTable' => $followUpsTable,
            'appointmentsTable' => $appointmentsTable,
            'sharedWithMe' => $sharedWithMe,
            'sharedByMe' => $sharedByMe,
            'recentImport' => $recentImport,
            'importsTable' => $importsTable,
            'sourcePerformance' => $sourcePerformance,
        ]);
    }

    public function placeholder(Request $request, string $screen): View
    {
        abort_unless(in_array($screen, [
            'create',
            'pipeline',
            'follow-ups',
            'appointments',
            'shared-with-me',
            'shared-by-me',
            'access-manager',
            'import',
            'settings',
        ], true), 404);

        return view('team.prospect-screen', [
            'screen' => str($screen)->replace('-', ' ')->title(),
            'screenKey' => $screen,
            ...$this->screenData($request),
        ]);
    }

    public function show(Prospect $prospect): View
    {
        $this->authorize('view', $prospect);

        return view('team.prospect-record', [
            'mode' => 'show',
            'prospect' => $prospect->load(['source:id,name', 'stage:id,name']),
            ...$this->prospectFormOptions(),
        ]);
    }

    public function edit(Prospect $prospect): View
    {
        $this->authorize('update', $prospect);

        return view('team.prospect-record', [
            'mode' => 'edit',
            'prospect' => $prospect->load(['source:id,name', 'stage:id,name']),
            ...$this->prospectFormOptions(),
        ]);
    }

    public function update(Request $request, Prospect $prospect): RedirectResponse
    {
        $this->authorize('update', $prospect);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:60'],
            'city' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:60'],
            'interest_level' => ['required', 'in:cold,warm,hot'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'pipeline_stage_id' => ['nullable', 'exists:pipeline_stages,id'],
            'prospect_source_id' => ['nullable', 'exists:prospect_sources,id'],
            'next_follow_up_at' => ['nullable', 'date'],
            'notes_summary' => ['nullable', 'string'],
        ]);

        $prospect->update($validated);

        return redirect()
            ->route('team.prospects.records.show', $prospect)
            ->with('status', 'Prospect updated.');
    }

    public function archive(Prospect $prospect): RedirectResponse
    {
        $this->authorize('update', $prospect);

        $prospect->update([
            'status' => 'archived',
            'is_archived' => true,
            'archived_at' => now(),
        ]);

        return redirect()
            ->route('team.prospects')
            ->with('status', 'Prospect archived.');
    }

    public function destroy(Prospect $prospect): RedirectResponse
    {
        $this->authorize('delete', $prospect);

        $prospect->delete();

        return redirect()
            ->route('team.prospects')
            ->with('status', 'Prospect deleted.');
    }

    private function prospectFormOptions(): array
    {
        return [
            'pipelineStages' => DB::table('pipeline_stages')->whereNull('user_id')->where('is_active', true)->orderBy('sort_order')->get(),
            'sources' => DB::table('prospect_sources')->where('is_active', true)->orderBy('sort_order')->get(),
        ];
    }

    private function screenData(Request $request): array
    {
        $user = $request->user();

        return [
            'pipelineStages' => DB::table('pipeline_stages')->whereNull('user_id')->where('is_active', true)->orderBy('sort_order')->get(),
            'sources' => DB::table('prospect_sources')->where('is_active', true)->orderBy('sort_order')->get(),
            'types' => DB::table('prospect_types')->where('is_active', true)->orderBy('sort_order')->get(),
            'interests' => DB::table('prospect_interests')->where('is_active', true)->orderBy('sort_order')->get(),
            'prospects' => Prospect::query()
                ->with(['source:id,name', 'stage:id,name'])
                ->where('owner_id', $user->id)
                ->whereNull('deleted_at')
                ->latest()
                ->limit(25)
                ->get(),
            'followUps' => DB::table('prospect_followups')
                ->join('prospects', 'prospects.id', '=', 'prospect_followups.prospect_id')
                ->where('prospect_followups.assigned_user_id', $user->id)
                ->whereNull('prospect_followups.deleted_at')
                ->whereNull('prospects.deleted_at')
                ->orderBy('prospect_followups.due_at')
                ->limit(25)
                ->get([
                    'prospect_followups.id',
                    'prospect_followups.followup_type',
                    'prospect_followups.priority',
                    'prospect_followups.status',
                    'prospect_followups.due_at',
                    'prospect_followups.notes',
                    'prospects.first_name',
                    'prospects.last_name',
                    'prospects.email',
                    'prospects.phone',
                ]),
            'appointments' => DB::table('prospect_appointments')
                ->join('prospects', 'prospects.id', '=', 'prospect_appointments.prospect_id')
                ->leftJoin('appointment_types', 'appointment_types.id', '=', 'prospect_appointments.appointment_type_id')
                ->leftJoin('users as helpers', 'helpers.id', '=', 'prospect_appointments.assigned_helper_id')
                ->where('prospect_appointments.owner_id', $user->id)
                ->whereNull('prospect_appointments.deleted_at')
                ->whereNull('prospects.deleted_at')
                ->orderByDesc('prospect_appointments.scheduled_at')
                ->limit(25)
                ->get([
                    'prospect_appointments.id',
                    'prospect_appointments.scheduled_at',
                    'prospect_appointments.purpose',
                    'prospect_appointments.status',
                    'prospect_appointments.location_or_link',
                    'appointment_types.name as appointment_type',
                    'helpers.name as helper_name',
                    'prospects.first_name',
                    'prospects.last_name',
                ]),
            'shares' => DB::table('prospect_shares')
                ->join('prospects', 'prospects.id', '=', 'prospect_shares.prospect_id')
                ->join('users as collaborators', 'collaborators.id', '=', 'prospect_shares.shared_with')
                ->leftJoin('prospect_share_permissions', 'prospect_share_permissions.id', '=', 'prospect_shares.prospect_share_permission_id')
                ->where('prospect_shares.granted_by', $user->id)
                ->whereNull('prospect_shares.deleted_at')
                ->whereNull('prospects.deleted_at')
                ->orderByDesc('prospect_shares.granted_at')
                ->limit(25)
                ->get([
                    'prospect_shares.id',
                    'prospect_shares.status',
                    'prospect_shares.granted_at',
                    'prospect_shares.expires_at',
                    'prospects.first_name',
                    'prospects.last_name',
                    'collaborators.name as collaborator_name',
                    'prospect_share_permissions.name as permission_name',
                ]),
            'sharedWithMe' => DB::table('prospect_shares')
                ->join('prospects', 'prospects.id', '=', 'prospect_shares.prospect_id')
                ->join('users as owners', 'owners.id', '=', 'prospect_shares.granted_by')
                ->leftJoin('prospect_share_permissions', 'prospect_share_permissions.id', '=', 'prospect_shares.prospect_share_permission_id')
                ->where('prospect_shares.shared_with', $user->id)
                ->where('prospect_shares.status', 'active')
                ->whereNull('prospect_shares.revoked_at')
                ->whereNull('prospect_shares.deleted_at')
                ->whereNull('prospects.deleted_at')
                ->where(function ($query): void {
                    $query->whereNull('prospect_shares.expires_at')->orWhere('prospect_shares.expires_at', '>', now());
                })
                ->orderByDesc('prospect_shares.granted_at')
                ->limit(25)
                ->get([
                    'prospect_shares.id',
                    'prospect_shares.granted_at',
                    'prospect_shares.expires_at',
                    'prospects.first_name',
                    'prospects.last_name',
                    'owners.name as owner_name',
                    'prospect_share_permissions.name as permission_name',
                ]),
            'imports' => DB::table('prospect_imports')
                ->where('user_id', $user->id)
                ->whereNull('deleted_at')
                ->orderByDesc('created_at')
                ->limit(25)
                ->get(),
        ];
    }
}
