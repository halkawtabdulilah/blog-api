<?php

namespace Tests\Feature\Category;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateCategoryTest extends TestCase
{

    use RefreshDatabase;

    #[Test] public function endpoint_updates_category(): void
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
    #[Test] public function endpoint_updates_category_with_activity_log(): void
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

        $this->assertDatabaseHas('activity_logs', [
            'action' => "UPDATE",
            'entity_type' => get_class($category),
            'entity_id' => $category->id,
        ]);

    }
}
