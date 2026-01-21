<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Position;

class LeaderSubordinatesTest extends TestCase
{
    use RefreshDatabase;

    public function test_leader_sees_subordinates()
    {
        $super = Position::create(['name' => 'Superuser', 'level' => 999]);
        $leaderPos = Position::create(['name' => 'Leader', 'level' => 10]);
        $staffPos = Position::create(['name' => 'Staff', 'level' => 1]);

        $leader = User::factory()->create(['position_id' => $leaderPos->id]);
        $sub1 = User::factory()->create(['position_id' => $staffPos->id, 'leader_id' => $leader->id]);
        $sub2 = User::factory()->create(['position_id' => $staffPos->id, 'leader_id' => $sub1->id]);

        $this->actingAs($leader)
            ->get(route('leader.subordinates.index'))
            ->assertStatus(200)
            ->assertSee($sub1->name)
            ->assertSee($sub2->name);
    }

    public function test_user_not_leader_sees_forbidden_when_viewing_other_subordinate_show()
    {
        $leaderPos = Position::create(['name' => 'Leader', 'level' => 10]);
        $staffPos = Position::create(['name' => 'Staff', 'level' => 1]);

        $leader = User::factory()->create(['position_id' => $leaderPos->id]);
        $other = User::factory()->create(['position_id' => $staffPos->id]);
        $sub = User::factory()->create(['position_id' => $staffPos->id, 'leader_id' => $leader->id]);

        $this->actingAs($other)
            ->get(route('leader.subordinates.show', $sub->id))
            ->assertStatus(403);
    }
}
