<?php

namespace Tests\Feature\Tag;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TagControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_tags(): void
    {
        Tag::factory()->count(3)->create();

        $response = $this->getJson('/api/tags');

        $response->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_can_show_a_tag(): void
    {
        $tag = Tag::factory()->create();

        $response = $this->getJson("/api/tags/{$tag->id}");

        $response->assertOk()->assertJsonPath('data.id', $tag->id);
    }

    public function test_admin_can_create_tag(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'Admin']));

        $response = $this->postJson('/api/tags', [
            'name' => 'New Tag',
        ]);

        $response->assertCreated()->assertJsonPath('data.name', 'New Tag');

        $this->assertDatabaseHas('tags', [
            'name' => 'New Tag',
        ]);
    }

    public function test_admin_can_update_tag(): void
    {
        $tag = Tag::factory()->create();
        Sanctum::actingAs(User::factory()->create(['role' => 'Admin']));

        $response = $this->putJson("/api/tags/{$tag->id}", [
            'name' => 'Updated Tag',
        ]);

        $response->assertOk()->assertJsonPath('data.name', 'Updated Tag');

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => 'Updated Tag',
        ]);
    }

    public function test_admin_can_delete_tag(): void
    {
        $tag = Tag::factory()->create();
        Sanctum::actingAs(User::factory()->create(['role' => 'Admin']));

        $response = $this->deleteJson("/api/tags/{$tag->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('tags', [
            'id' => $tag->id,
        ]);
    }
}
