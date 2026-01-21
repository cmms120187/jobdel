<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Position;

class LeaderReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_leader_can_view_overview_report()
    {
        $leaderPos = Position::create(['name' => 'Leader', 'level' => 10]);
        $staffPos = Position::create(['name' => 'Staff', 'level' => 1]);

        $leader = User::factory()->create(['position_id' => $leaderPos->id]);
        $sub = User::factory()->create(['position_id' => $staffPos->id, 'leader_id' => $leader->id]);

        $this->actingAs($leader)
            ->get(route('leader.reports.overview'))
            ->assertStatus(200)
            ->assertSee('Laporan Ringkas Tim');
    }
}
