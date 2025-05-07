<?php

namespace Tests\Feature\Category;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReadCategoryTest extends TestCase
{

    use RefreshDatabase;

    #[Test] public function endpoint_returns_paginated_categories(): void
    {

        Category::factory()->count(15)->create();

        $response = $this->getJson('/api/category?page=2&limit=5');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'meta' => [
                    'page',
                    'pages',
                    'limit',
                    'total',
                ],
                'data' => [
                    '*' => ['id', 'name', 'slug']
                ]
            ])
            ->assertJson([
                'meta' => [
                    'page' => 2,
                    'limit' => 5,
                    'pages' => 3, // 15 items / 5 per page = 3 pages
                ],
                'data' => array_map(function ($item) {
                    return ['id' => $item['id']];
                }, Category::skip(5)->take(5)->get()->toArray())
            ]);
    }

    #[Test] public function endpoint_filters_categories_by_search_term()
    {
        $electronics = Category::factory()->create(['name' => 'Electronics']);
        $books = Category::factory()->create(['name' => 'Books']);


        $response = $this->getJson('/api/category?search=Electron');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => $electronics->name])
            ->assertJsonMissing(['name' => $books->name]);
    }

    #[Test] public function endpoint_returns_single_category() {
        Category::factory()->count(5)->create();
        $electronics = Category::factory()->create(['name' => 'Electronics']);
        Category::factory()->count(4)->create();

        $response = $this->getJson('/api/category/6');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'Electronics',
        ]);

    }
    #[Test] public function endpoint_returns_single_category_with_activity_log() {
        Category::factory()->count(5)->create();
        $electronics = Category::factory()->create(['name' => 'Electronics']);
        Category::factory()->count(4)->create();

        $response = $this->getJson('/api/category/6');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'Electronics',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => "READ",
            'entity_type' => get_class($electronics),
            'entity_id' => $electronics->id,
        ]);

    }

}
