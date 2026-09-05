<?php

namespace Tests\Feature\Api;

use App\Models\Channel;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChannelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_accessible_channels(): void
    {
        $department = Department::factory()->create();
        $user = User::factory()->create(['department_id' => $department->id]);
        $channel = Channel::factory()->create(['department_id' => $department->id, 'type' => 'public']);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/channels');

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $channel->id]);
    }

    public function test_user_can_create_a_channel(): void
    {
        $department = Department::factory()->create();
        $user = User::factory()->create(['department_id' => $department->id]);

        $payload = [
            'department_id' => $department->id,
            'name' => 'General Chat',
            'type' => 'public',
            'description' => 'Team discussions',
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/channels', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'General Chat');

        $this->assertDatabaseHas('channels', ['name' => 'General Chat']);
    }
}