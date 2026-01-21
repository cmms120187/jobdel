<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserAccessTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // Seed positions
        Position::create(['name' => 'Staff', 'level' => 1]);
        Position::create(['name' => 'Leader', 'level' => 2]);
        Position::create(['name' => 'Superuser', 'level' => 99]);
    }

    public function test_superuser_can_create_user()
    {
        $super = User::factory()->create(['position_id' => Position::where('name', 'Superuser')->first()->id]);
        $this->actingAs($super)
            ->post(route('admin.users.store'), [
                'nik' => 'U12345',
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'secret',
                'position_id' => Position::where('name','Staff')->first()->id,
            ])
            ->assertStatus(302);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_leader_cannot_create_user()
    {
        $leader = User::factory()->create(['position_id' => Position::where('name', 'Leader')->first()->id]);
        $this->actingAs($leader)
            ->post(route('admin.users.store'), [
                'nik' => 'U54321',
                'name' => 'Another',
                'email' => 'another@example.com',
                'password' => 'secret',
                'position_id' => Position::where('name','Staff')->first()->id,
            ])
            ->assertStatus(403);

        $this->assertDatabaseMissing('users', ['email' => 'another@example.com']);
    }
}
