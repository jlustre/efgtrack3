<?php

namespace Tests\Feature\Prospects;

use App\Jobs\Prospects\RollupProspectAnalytics;
use App\Livewire\Prospects\ProspectAnalytics;
use App\Livewire\Prospects\ProspectGoalsPanel;
use App\Models\Prospect;
use App\Models\ProspectGoal;
use App\Models\User;
use App\Services\Prospects\ProspectAnalyticsService;
use Database\Seeders\ProspectFunnelSeeder;
use Database\Seeders\ProspectLookupSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class ProspectAnalyticsTest extends TestCase
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

    public function test_analytics_page_loads_with_charts_and_summary(): void
    {
        $stageId = DB::table('pipeline_stages')->where('slug', 'new-lead')->value('id');
        $funnelId = DB::table('prospect_funnels')->where('key', 'insurance')->value('id');

        Prospect::create([
            'owner_id' => $this->owner->id,
            'prospect_funnel_id' => $funnelId,
            'pipeline_stage_id' => $stageId,
            'funnel_type' => 'insurance',
            'first_name' => 'Analytics',
            'last_name' => 'Chart',
            'interest_level' => 'hot',
            'priority' => 'high',
        ]);

        $this->actingAs($this->owner)
            ->get(route('team.prospects.analytics'))
            ->assertOk()
            ->assertSee('Analytics & Goals')
            ->assertSee('Funnel Conversion')
            ->assertSee('Lead Sources')
            ->assertSee('Monthly Activity')
            ->assertSee('Prospect Growth')
            ->assertSee('Dual Pipeline')
            ->assertSee('Period Goals');

        Livewire::actingAs($this->owner)
            ->test(ProspectAnalytics::class)
            ->assertSee('Active Prospects')
            ->assertSee('Funnel Conversion')
            ->set('funnelFilter', 'recruiting')
            ->assertSet('funnelFilter', 'recruiting');
    }

    public function test_goals_crud_and_progress_bars(): void
    {
        Livewire::actingAs($this->owner)
            ->test(ProspectGoalsPanel::class)
            ->call('openCreateForm')
            ->assertSet('showForm', true)
            ->set('metricKey', 'contacts')
            ->set('targetValue', 15)
            ->call('saveGoal')
            ->assertHasNoErrors()
            ->assertSet('showForm', false);

        $bounds = app(ProspectAnalyticsService::class)->periodBounds('monthly');

        $this->assertDatabaseHas('prospect_goals', [
            'user_id' => $this->owner->id,
            'period_type' => 'monthly',
            'metric_key' => 'contacts',
            'target_value' => 15,
        ]);

        $goal = ProspectGoal::query()->where('user_id', $this->owner->id)->firstOrFail();

        Livewire::actingAs($this->owner)
            ->test(ProspectGoalsPanel::class)
            ->call('editGoal', $goal->id)
            ->assertSet('targetValue', 15)
            ->set('targetValue', 20)
            ->call('saveGoal')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('prospect_goals', [
            'id' => $goal->id,
            'target_value' => 20,
        ]);

        Livewire::actingAs($this->owner)
            ->test(ProspectGoalsPanel::class)
            ->call('deleteGoal', $goal->id);

        $this->assertDatabaseMissing('prospect_goals', ['id' => $goal->id]);
    }

    public function test_rollup_job_writes_snapshots_and_refreshes_goals(): void
    {
        $stageId = DB::table('pipeline_stages')->where('slug', 'new-lead')->value('id');
        $funnelId = DB::table('prospect_funnels')->where('key', 'insurance')->value('id');

        Prospect::create([
            'owner_id' => $this->owner->id,
            'prospect_funnel_id' => $funnelId,
            'pipeline_stage_id' => $stageId,
            'funnel_type' => 'insurance',
            'first_name' => 'Rollup',
            'last_name' => 'Job',
            'interest_level' => 'warm',
            'priority' => 'medium',
        ]);

        $bounds = app(ProspectAnalyticsService::class)->periodBounds('monthly');

        ProspectGoal::create([
            'user_id' => $this->owner->id,
            'period_type' => 'monthly',
            'period_start' => $bounds['start']->toDateString(),
            'period_end' => $bounds['end']->toDateString(),
            'metric_key' => 'new_prospects',
            'target_value' => 5,
            'actual_value' => 0,
        ]);

        (new RollupProspectAnalytics)->handle(app(ProspectAnalyticsService::class));

        $this->assertDatabaseHas('prospect_goal_snapshots', [
            'user_id' => $this->owner->id,
            'metric_key' => 'new_prospects',
            'value' => 1,
        ]);

        $this->assertDatabaseHas('prospect_goals', [
            'user_id' => $this->owner->id,
            'metric_key' => 'new_prospects',
            'actual_value' => 1,
        ]);
    }

    public function test_prospects_dashboard_includes_analytics_shortcut(): void
    {
        $this->actingAs($this->owner)
            ->get(route('team.prospects'))
            ->assertOk()
            ->assertSee('Analytics & Goals');
    }
}
