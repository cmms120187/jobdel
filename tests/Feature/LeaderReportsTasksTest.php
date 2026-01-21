<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Position;
use App\Models\Task;

class LeaderReportsTasksTest extends TestCase
{
    use RefreshDatabase;

    public function test_leader_can_view_filtered_tasks()
    {
        $leaderPos = Position::create(['name' => 'Leader', 'level' => 10]);
        $staffPos = Position::create(['name' => 'Staff', 'level' => 1]);

        $leader = User::factory()->create(['position_id' => $leaderPos->id]);
        $sub = User::factory()->create(['position_id' => $staffPos->id, 'leader_id' => $leader->id]);

        Task::create(['title' => 'Alpha task', 'created_by' => $sub->id, 'status' => 'pending']);
        Task::create(['title' => 'Beta task', 'created_by' => $leader->id, 'status' => 'completed']);

        $this->actingAs($leader)
            ->get(route('leader.reports.tasks', ['status' => 'pending']))
            ->assertStatus(200)
            ->assertSee('Alpha task')
            ->assertDontSee('Beta task');
    }

    public function test_leader_can_export_csv()
    {
        $leaderPos = Position::create(['name' => 'Leader', 'level' => 10]);
        $staffPos = Position::create(['name' => 'Staff', 'level' => 1]);

        $leader = User::factory()->create(['position_id' => $leaderPos->id]);
        $sub = User::factory()->create(['position_id' => $staffPos->id, 'leader_id' => $leader->id]);

        Task::create(['title' => 'Alpha task', 'created_by' => $sub->id, 'status' => 'pending']);

        $response = $this->actingAs($leader)
            ->get(route('leader.reports.export'));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
    }
}
