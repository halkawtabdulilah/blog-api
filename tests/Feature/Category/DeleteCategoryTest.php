<?php

namespace Tests\Feature\Category;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteCategoryTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function endpoint_deletes_category(): void
    {

        $category = Category::factory()->create([
            'name' => 'Original Name',
            'slug' => 'original-slug'
        ]);


        $response = $this->delete("/api/category/{$category->id}");

        // Assert
        $response->assertNoContent();


    }
    /** @test */
    public function endpoint_deletes_category_with_activity_log(): void
    {

        $category = Category::factory()->create([
            'name' => 'Original Name',
            'slug' => 'original-slug'
        ]);


        $response = $this->delete("/api/category/{$category->id}");

        // Assert
        $response->assertNoContent();


        $this->assertDatabaseHas('activity_logs', [
            'action' => "DELETE",
            'entity_type' => get_class($category),
            'entity_id' => $category->id,
        ]);


    }
}
