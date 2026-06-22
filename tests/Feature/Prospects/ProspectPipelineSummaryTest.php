<?php

namespace Tests\Feature\Prospects;

use App\Models\Prospect;
use App\Models\User;
use App\Services\Prospects\ProspectFunnelService;
use Database\Seeders\ProspectDashboardTestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProspectPipelineSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_pipeline_summary_uses_numbered_funnel_lifecycle_order(): void
    {
        $this->seed(ProspectDashboardTestSeeder::class);

        $user = User::where('email', 'prospects@efgtrack.com')->firstOrFail();
        $service = app(ProspectFunnelService::class);
        $funnelId = $service->primaryFunnelIdFor($user);
        $summary = $service->pipelineSummaryFor($user, $funnelId);

        $this->assertNotEmpty($summary);
        $this->assertSame('1. New Lead', $summary->first()->label);
        $this->assertSame(1, $summary->first()->sequence);
        $this->assertSame('2. Contact Attempted', $summary->get(1)->label);

        $this->actingAs($user)
            ->get(route('team.prospects'))
            ->assertOk()
            ->assertSee('1. New Lead', false)
            ->assertSee('2. Contact Attempted', false);
    }

    public function test_edit_prospect_form_shows_numbered_pipeline_stages(): void
    {
        $this->seed(ProspectDashboardTestSeeder::class);

        $user = User::where('email', 'prospects@efgtrack.com')->firstOrFail();
        $prospect = Prospect::query()->where('owner_id', $user->id)->firstOrFail();

        $this->actingAs($user)
            ->get(route('team.prospects.records.edit', $prospect))
            ->assertOk()
            ->assertSee('1. New Lead', false)
            ->assertSee('3. Contact Made', false);
    }
}
