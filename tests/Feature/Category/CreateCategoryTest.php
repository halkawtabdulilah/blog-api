<?php

namespace Tests\Feature\Category;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateCategoryTest extends TestCase
{

    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_user_can_create_category(): void
    {

        $userData = [
            "name" => "Test Category",
            "slug" => "test-category",
            "description" => "Test Category description",
        ];

        $response = $this->postJson('/api/category', $userData);

        $response->assertStatus(201);
    }
}
