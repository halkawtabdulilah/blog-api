<?php

namespace Tests\Feature\Category;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateCategoryTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function endpoint_updates_category(): void
    {

        $category = Category::factory()->create([
            'name' => 'Original Name',
            'slug' => 'original-slug'
        ]);

        $updatedData = [
            "slug" => "test",
        ];

        $response = $this->patchJson("/api/category/{$category->id}", $updatedData);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Category updated successfully'
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'slug' => 'test'
        ]);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
            'name' => 'original-slug'
        ]);

    }
}
