<?php

namespace Tests\Feature\Fna;

use App\Livewire\Fna\Client\FnaClientPortalGate;
use App\Livewire\Fna\Client\FnaClientPortalReturn;
use App\Livewire\Fna\Client\FnaClientPortalWizard;
use App\Livewire\Fna\FnaClientInvitePanel;
use App\Models\FnaClientInvite;
use App\Models\FnaRecord;
use App\Models\Prospect;
use App\Models\User;
use App\Services\Fna\FnaClientInviteService;
use App\Support\FnaClientPortalSession;
use Database\Seeders\ProspectFunnelSeeder;
use Database\Seeders\ProspectLookupSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class FnaClientPortalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RolePermissionSeeder::class,
            ProspectLookupSeeder::class,
            ProspectFunnelSeeder::class,
        ]);
    }

    protected function licensedAgent(): User
    {
        $user = User::factory()->create();
        $user->assignRole('associate');
        $user->profile()->create(['license_number' => 'LIC-12345']);

        return $user;
    }

    protected function unlicensedAgent(): User
    {
        $user = User::factory()->create();
        $user->assignRole('associate');
        $user->profile()->create([]);

        return $user;
    }

    protected function createProspect(User $owner): Prospect
    {
        $stageId = DB::table('pipeline_stages')->where('slug', 'new-lead')->value('id');

        return Prospect::create([
            'owner_id' => $owner->id,
            'first_name' => 'Portal',
            'last_name' => 'Prospect',
            'email' => 'portal.prospect@example.com',
            'phone' => '6045550100',
            'pipeline_stage_id' => $stageId,
            'fna_status' => 'not_started',
            'status' => 'active',
            'interest_level' => 'warm',
        ]);
    }

    public function test_licensed_agent_can_create_invite_for_owned_prospect(): void
    {
        $agent = $this->licensedAgent();
        $prospect = $this->createProspect($agent);

        Livewire::actingAs($agent)
            ->test(FnaClientInvitePanel::class, ['prospect' => $prospect])
            ->set('recipient_name', 'Portal Prospect')
            ->set('recipient_email', 'portal.prospect@example.com')
            ->set('recipient_phone', '6045550100')
            ->call('sendInvite')
            ->assertHasNoErrors()
            ->assertSet('createdSecurityCode', fn ($code) => strlen((string) $code) === 6);

        $this->assertDatabaseHas('fna_client_invites', [
            'sender_user_id' => $agent->id,
            'prospect_id' => $prospect->id,
            'recipient_name' => 'Portal Prospect',
        ]);

        $this->assertDatabaseHas('fna_records', [
            'owner_user_id' => $agent->id,
            'prospect_id' => $prospect->id,
            'is_client_portal' => true,
        ]);
    }

    public function test_unlicensed_agent_blocked_from_creating_invite(): void
    {
        $agent = $this->unlicensedAgent();
        $prospect = $this->createProspect($agent);

        Livewire::actingAs($agent)
            ->test(FnaClientInvitePanel::class, ['prospect' => $prospect])
            ->assertForbidden();
    }

    public function test_security_code_verification_works(): void
    {
        $agent = $this->licensedAgent();
        $prospect = $this->createProspect($agent);

        $result = app(FnaClientInviteService::class)->createInvite($agent, $prospect, [
            'recipient_name' => 'Portal Prospect',
        ]);

        $invite = $result['invite'];
        $code = $result['security_code'];

        Livewire::test(FnaClientPortalGate::class, ['token' => $invite->token])
            ->set('securityCode', '000000')
            ->call('verifySecurityCode')
            ->assertHasErrors(['securityCode']);

        Livewire::test(FnaClientPortalGate::class, ['token' => $invite->token])
            ->set('securityCode', $code)
            ->call('verifySecurityCode')
            ->assertHasNoErrors()
            ->assertSet('step', 2);
    }

    public function test_access_credentials_setup_and_return_login_work(): void
    {
        $agent = $this->licensedAgent();
        $prospect = $this->createProspect($agent);

        $result = app(FnaClientInviteService::class)->createInvite($agent, $prospect, [
            'recipient_name' => 'Portal Prospect',
            'recipient_email' => 'client@example.com',
            'recipient_phone' => '6045550100',
        ]);

        $invite = $result['invite'];
        $code = $result['security_code'];

        Livewire::test(FnaClientPortalGate::class, ['token' => $invite->token])
            ->set('securityCode', $code)
            ->call('verifySecurityCode')
            ->set('accessEmail', 'client@example.com')
            ->set('accessPhone', '604-555-0100')
            ->set('accessSsnLastFour', '1234')
            ->call('setupAccessCredentials')
            ->assertRedirect(route('fna.client.wizard', $invite->token));

        $invite->refresh();
        $this->assertNotNull($invite->access_credential_hash);

        Livewire::test(FnaClientPortalReturn::class)
            ->set('accessEmail', 'client@example.com')
            ->set('accessPhone', '6045550100')
            ->set('accessSsnLastFour', '1234')
            ->call('login')
            ->assertRedirect(route('fna.client.wizard', $invite->token));
    }

    public function test_autosave_updates_fna_record(): void
    {
        $agent = $this->licensedAgent();
        $prospect = $this->createProspect($agent);

        $result = app(FnaClientInviteService::class)->createInvite($agent, $prospect, [
            'recipient_name' => 'Portal Prospect',
        ]);

        $invite = $result['invite'];
        FnaClientPortalSession::markVerified($invite);

        Livewire::test(FnaClientPortalWizard::class, ['token' => $invite->token])
            ->set('client_name', 'Updated Portal Client')
            ->set('client_email', 'updated@example.com')
            ->call('autosave')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('fna_records', [
            'id' => $invite->fna_record_id,
            'client_name' => 'Updated Portal Client',
            'client_email' => 'updated@example.com',
        ]);

        $invite->refresh();
        $this->assertNotNull($invite->last_saved_at);
    }

    public function test_agent_can_view_client_progress_on_fna_show(): void
    {
        $agent = $this->licensedAgent();
        $prospect = $this->createProspect($agent);

        $result = app(FnaClientInviteService::class)->createInvite($agent, $prospect, [
            'recipient_name' => 'Portal Prospect',
        ]);

        $invite = $result['invite'];
        $invite->update(['status' => 'active', 'last_saved_at' => now()]);

        $this->actingAs($agent)
            ->get(route('team.fna.show', $invite->fna_record_id))
            ->assertOk()
            ->assertSee('Client portal invite')
            ->assertSee('Active');
    }

    public function test_non_owner_cannot_view_invite_management(): void
    {
        $owner = $this->licensedAgent();
        $other = $this->licensedAgent();
        $prospect = $this->createProspect($owner);

        $result = app(FnaClientInviteService::class)->createInvite($owner, $prospect, [
            'recipient_name' => 'Portal Prospect',
        ]);

        $fna = FnaRecord::find($result['invite']->fna_record_id);

        $this->actingAs($other)
            ->get(route('team.fna.show', $fna))
            ->assertForbidden();

        Livewire::actingAs($other)
            ->test(FnaClientInvitePanel::class, ['prospect' => $prospect])
            ->assertForbidden();
    }

    public function test_cfm_can_create_invite_for_active_trainee(): void
    {
        $cfm = $this->licensedAgent();
        $cfm->assignRole('certified-field-mentor');

        $trainee = User::factory()->create(['email' => 'trainee.member@example.com']);
        $trainee->assignRole('associate');
        $trainee->profile()->create(['phone' => '6045550199']);

        \App\Models\MentorAssignment::create([
            'mentor_id' => $cfm->id,
            'apprentice_id' => $trainee->id,
            'status' => 'active',
            'started_at' => now()->toDateString(),
        ]);

        Livewire::actingAs($cfm)
            ->test(FnaClientInvitePanel::class, ['recipientMember' => $trainee])
            ->assertSet('recipient_name', $trainee->name)
            ->assertSet('recipient_email', 'trainee.member@example.com')
            ->call('sendInvite')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('fna_client_invites', [
            'sender_user_id' => $cfm->id,
            'recipient_user_id' => $trainee->id,
            'recipient_name' => $trainee->name,
        ]);

        $this->assertDatabaseHas('fna_records', [
            'owner_user_id' => $cfm->id,
            'is_client_portal' => true,
            'client_name' => $trainee->name,
        ]);
    }

    public function test_agent_can_create_invite_for_downline_member(): void
    {
        $sponsor = $this->licensedAgent();
        $sponsor->givePermissionTo('view full downline');

        $member = User::factory()->create([
            'sponsor_id' => $sponsor->id,
            'email' => 'downline.member@example.com',
        ]);
        $member->assignRole('associate');
        $member->profile()->create(['phone' => '6045550188']);

        app(\App\Services\DownlineHierarchyService::class)->rebuild();

        Livewire::actingAs($sponsor)
            ->test(FnaClientInvitePanel::class, ['recipientMember' => $member])
            ->call('sendInvite')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('fna_client_invites', [
            'sender_user_id' => $sponsor->id,
            'recipient_user_id' => $member->id,
        ]);
    }

    public function test_agent_cannot_invite_unrelated_member(): void
    {
        $agent = $this->licensedAgent();
        $stranger = User::factory()->create();
        $stranger->assignRole('associate');

        Livewire::actingAs($agent)
            ->test(FnaClientInvitePanel::class, ['recipientMember' => $stranger])
            ->assertForbidden();
    }

    public function test_member_invite_service_rejects_prospect_and_member_together(): void
    {
        $agent = $this->licensedAgent();
        $prospect = $this->createProspect($agent);
        $member = User::factory()->create(['sponsor_id' => $agent->id]);
        $member->assignRole('associate');
        app(\App\Services\DownlineHierarchyService::class)->rebuild();
        $agent->givePermissionTo('view full downline');

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        app(FnaClientInviteService::class)->createInvite($agent, $prospect, [
            'recipient_name' => 'Both',
        ], $member);
    }
}
