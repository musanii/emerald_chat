<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttachmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_upload_valid_attachment(): void
{
    Storage::fake(config('filesystems.default'));

    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('document.pdf', 1024, 'application/pdf');

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/attachments', [
            'file' => $file,
        ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.file_name', 'document.pdf');

    $this->assertDatabaseHas('attachments', ['file_name' => 'document.pdf']);
}

    public function test_attachment_rejects_invalid_file_types(): void
    {
        Storage::fake(config('filesystems.default'));

        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('executable.exe', 500, 'application/x-msdownload');

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/attachments', [
                'file' => $file,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['file']);
    }
}