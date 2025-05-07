<?php

namespace Tests\Feature\Category;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateCategoryTest extends TestCase
{

    use RefreshDatabase;

    #[Test] public function test_user_can_create_category(): void
    {

        $userData = [
            "name" => "Test Category",
            "slug" => "test-category",
            "description" => "Test Category description",
        ];

        $response = $this->postJson('/api/category', $userData);

        $response->assertStatus(201);
    }
    #[Test] public function test_user_can_create_category_with_activity_log(): void
    {

        $userData = [
            "name" => "Test Category",
            "slug" => "test-category",
            "description" => "Test Category description",
        ];

        $response = $this->postJson('/api/category', $userData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('activity_logs', [
            'action' => "CREATE",
            'entity_type' => Category::class,
            'entity_id' => $response->json('id'),
        ]);

    }
}
