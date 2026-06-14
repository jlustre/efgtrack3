<?php

namespace Tests\Feature\Prospects;

use App\Livewire\Prospects\ProspectAiCoach;
use App\Livewire\Prospects\ProspectAiCoachPanel;
use App\Models\Prospect;
use App\Models\User;
use Database\Seeders\ProspectFunnelSeeder;
use Database\Seeders\ProspectLookupSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class ProspectAiCoachTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RolePermissionSeeder::class,
            ProspectLookupSeeder::class,
            ProspectFunnelSeeder::class,
        ]);

        $this->owner = User::factory()->create();
        $this->owner->assignRole('member');
    }

    public function test_ai_coach_page_renders_recommendations(): void
    {
        $stageId = DB::table('pipeline_stages')->where('slug', 'new-lead')->value('id');

        Prospect::create([
            'owner_id' => $this->owner->id,
            'pipeline_stage_id' => $stageId,
            'first_name' => 'Coach',
            'last_name' => 'Target',
            'status' => 'active',
            'interest_level' => 'warm',
            'priority' => 'medium',
            'last_contacted_at' => now()->subDays(10),
        ]);

        $this->actingAs($this->owner)
            ->get(route('team.prospects.ai-coach'))
            ->assertOk()
            ->assertSee('AI Coach')
            ->assertSee('Read-only recommendations');

        Livewire::actingAs($this->owner)
            ->test(ProspectAiCoach::class)
            ->assertSee('Coach Target')
            ->assertSee('Prospect has had no contact for over 7 days');
    }

    public function test_ai_coach_panel_shows_top_suggestions_on_profile(): void
    {
        $stageId = DB::table('pipeline_stages')->where('slug', 'new-lead')->value('id');

        $prospect = Prospect::create([
            'owner_id' => $this->owner->id,
            'pipeline_stage_id' => $stageId,
            'first_name' => 'Panel',
            'last_name' => 'Prospect',
            'status' => 'active',
            'interest_level' => 'hot',
            'priority' => 'high',
            'last_activity_at' => now()->subDays(5),
            'last_contacted_at' => now()->subDays(5),
        ]);

        Livewire::actingAs($this->owner)
            ->test(ProspectAiCoachPanel::class, ['prospect' => $prospect])
            ->assertSee('AI Coach Suggestions')
            ->assertSee('Hot prospect has had no recent activity');
    }

    public function test_dashboard_includes_ai_coach_shortcut(): void
    {
        $this->actingAs($this->owner)
            ->get(route('team.prospects'))
            ->assertOk()
            ->assertSee('AI Coach');
    }
}
