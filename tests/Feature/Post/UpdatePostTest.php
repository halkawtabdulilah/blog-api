<?php

namespace Feature\Post;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdatePostTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function endpoint_updates_post_title()
    {
        $post = Post::factory()->create(['title' => 'Old Title']);
        $newData = ['title' => 'New Title'];

        $response = $this->patchJson("/api/post/{$post->id}", $newData);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Post updated successfully']);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'New Title'
        ]);
    }

    /** @test */
    public function endpoint_updates_post_content()
    {
        $post = Post::factory()->create(['content' => 'Old content']);
        $longContent = str_repeat('New content.', 10);

        $response = $this->patchJson("/api/post/{$post->id}", [
            'content' => $longContent
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'content' => $longContent
        ]);
    }

    /** @test */
    public function endpoint_updates_post_category()
    {
        $post = Post::factory()->create();
        $newCategory = Category::factory()->create();

        $response = $this->patchJson("/api/post/{$post->id}", [
            'category_id' => $newCategory->id
        ]);

        $response->assertStatus(200);
        $this->assertEquals($newCategory->id, $post->fresh()->category_id);
    }

    /** @test */
    public function endpoint_allows_same_title_for_same_post()
    {
        $post = Post::factory()->create(['title' => 'Existing Title']);

        $response = $this->patchJson("/api/post/{$post->id}", [
            'title' => 'Existing Title'
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function endpoint_rejects_invalid_category_id()
    {
        $post = Post::factory()->create();

        $response = $this->patchJson("/api/post/{$post->id}", [
            'category_id' => 9999
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['category_id']);
    }

}
