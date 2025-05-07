<?php

namespace Feature\Post;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeletePostTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function endpoint_deletes_posts_successfully()
    {
        $post = Post::factory()->create();

        $response = $this->deleteJson("/api/post/{$post->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('posts', [['id' => $post->id]]);
    }
    /** @test */
    public function endpoint_deletes_posts_successfully_with_activity_logs()
    {
        $post = Post::factory()->create();

        $response = $this->deleteJson("/api/post/{$post->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('posts', [['id' => $post->id]]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => "DELETE",
            'entity_type' => get_class($post),
            'entity_id' => $post->id
        ]);

    }

    /** @test */
    public function endpoint_returns_404_for_nonexistent_posts()
    {
        $nonExistentId = 9999;

        $response = $this->deleteJson("/api/post/{$nonExistentId}");

        $response->assertStatus(404);
    }

    /** @test */
    public function endpoint_soft_deletes_posts_when_enabled()
    {
        // Only test if Post uses SoftDeletes
        if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(Post::class))) {
            $post = Post::factory()->create();

            $this->deleteJson("/api/post/{$post->id}");

            $this->assertSoftDeleted($post);
        } else {
            $this->markTestSkipped('SoftDeletes not enabled for Post model');
        }
    }

}
