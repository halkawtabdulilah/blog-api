<?php

namespace Tests\Feature\Post;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreatePostTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function test_user_can_create_post(): void
    {

        $category = Category::factory()->create();

        $userData = [
            "title" => "A new post",
            "content" => "this is the content of the new post",
            "author" => "John Doe",
            "category_id" => $category->id,
        ];

        $response = $this->postJson('/api/post', $userData);

        $response->assertStatus(201);
    }
    /** @test */
    public function test_user_can_create_post_with_activity_log(): void
    {

        $category = Category::factory()->create();

        $userData = [
            "title" => "A new post",
            "content" => "this is the content of the new post",
            "author" => "John Doe",
            "category_id" => $category->id,
        ];

        $response = $this->postJson('/api/post', $userData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('activity_logs', [
            'action' => "CREATE",
            'entity_type' => Post::class,
            'entity_id' => $response->json('id'),
        ]);

    }

    /** @test */
    public function test_user_cannot_create_post_with_fake_category_id(): void
    {

        $category = Category::factory()->create();

        $userData = [
            "title" => "A new post",
            "content" => "this is the content of the new post",
            "author" => "John Doe",
            "category_id" => 345,
        ];

        $response = $this->postJson('/api/post', $userData);

        $response->assertStatus(422);
    }
}
