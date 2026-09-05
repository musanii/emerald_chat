<?php

namespace Tests\Feature\Api;

use App\Events\MessageSent;
use App\Models\Channel;
use App\Models\Department;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_post_message_and_trigger_broadcast_event(): void
    {
        Event::fake([MessageSent::class]);

        $department = Department::factory()->create();
        $user = User::factory()->create(['department_id' => $department->id]);
        $channel = Channel::factory()->create(['department_id' => $department->id, 'type' => 'public']);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/channels/{$channel->id}/messages", [
                'body' => 'Hello team!',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.body', 'Hello team!');

        // Assert that MessageSent WebSocket event was dispatched
        Event::assertDispatched(MessageSent::class, function ($event) use ($channel) {
            return $event->message['channel_id'] === $channel->id;
        });
    }

    public function test_unauthorized_user_cannot_read_private_channel_messages(): void
    {
        $department = Department::factory()->create();
        $user = User::factory()->create(['department_id' => $department->id]);
        
        // Private channel where user is NOT attached
        $channel = Channel::factory()->create(['department_id' => $department->id, 'type' => 'private']);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/v1/channels/{$channel->id}/messages");

        $response->assertStatus(403);
    }
}