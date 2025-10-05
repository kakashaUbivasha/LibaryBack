<?php

namespace Tests\Feature\Genre;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GenreCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_a_single_genre(): void
    {
        $genre = Genre::factory()->create();

        $response = $this->getJson("/api/genres/{$genre->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $genre->id)
            ->assertJsonPath('data.name', $genre->name);
    }

    public function test_admin_can_create_genre(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        Sanctum::actingAs($admin);

        $payload = ['name' => 'Science Fiction'];

        $response = $this->postJson('/api/genres', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.name', $payload['name']);

        $this->assertDatabaseHas('genres', $payload);
    }

    public function test_store_requires_name(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/genres', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    public function test_admin_can_update_genre(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        Sanctum::actingAs($admin);

        $genre = Genre::factory()->create(['name' => 'History']);
        $payload = ['name' => 'World History'];

        $response = $this->putJson("/api/genres/{$genre->id}", $payload);

        $response->assertOk()
            ->assertJsonPath('data.name', $payload['name']);

        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'name' => $payload['name'],
        ]);
    }

    public function test_non_admin_cannot_modify_genres(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/genres', ['name' => 'Drama']);

        $response->assertForbidden();
    }

    public function test_admin_can_delete_genre(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        Sanctum::actingAs($admin);

        $genre = Genre::factory()->create();

        $response = $this->deleteJson("/api/genres/{$genre->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('genres', ['id' => $genre->id]);
    }
}
