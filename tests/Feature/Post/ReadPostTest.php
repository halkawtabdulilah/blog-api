<?php

namespace Feature\Post;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadPostTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function endpoint_returns_paginated_posts(): void
    {

        Post::factory()->count(15)->create();

        $response = $this->getJson('/api/post?page=2&limit=5');

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
                    '*' => ['id', 'title', 'content', 'author']
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
                }, Post::skip(5)->take(5)->get()->toArray())
            ]);
    }

    /** @test */
    public function endpoint_filters_posts_by_search_term_and_category()
    {
        // Create test categories
        $techCategory = Category::factory()->create(['name' => 'Technology']);
        $scienceCategory = Category::factory()->create(['name' => 'Science']);

        // Create test post
        $quantumComputing = Post::factory()->create([
            'title' => 'Quantum Computers Emerging',
            'category_id' => $techCategory->id
        ]);
        $quantumPhysics = Post::factory()->create([
            'title' => 'Quantum Physics Explained',
            'category_id' => $scienceCategory->id
        ]);
        $classicalPhysics = Post::factory()->create([
            'title' => 'Classical Physics Basics',
            'category_id' => $scienceCategory->id
        ]);

        // Test 1: Filter by search term only
        $response1 = $this->getJson('/api/post?search=quantum');
        $response1->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['title' => $quantumComputing->title])
            ->assertJsonFragment(['title' => $quantumPhysics->title])
            ->assertJsonMissing(['title' => $classicalPhysics->title]);

        // Test 2: Filter by category only
        $response2 = $this->getJson('/api/post?category='.$scienceCategory->id);
        $response2->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['title' => $quantumPhysics->title])
            ->assertJsonFragment(['title' => $classicalPhysics->title])
            ->assertJsonMissing(['title' => $quantumComputing->title]);

        // Test 3: Combined search + category filter
        $response3 = $this->getJson('/api/post?search=quantum&category='.$scienceCategory->id);
        $response3->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['title' => $quantumPhysics->title])
            ->assertJsonMissing(['title' => $quantumComputing->title])
            ->assertJsonMissing(['title' => $classicalPhysics->title]);
    }

    /**
     * @test
     */
    public function endpoint_filters_posts_by_category_slug()
    {
        $techCategory = Category::factory()->create(['slug' => 'tech']);
        Post::factory()->create(['category_id' => $techCategory->id]);

        $response = $this->getJson('/api/post?category='.$techCategory->id);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.category.slug', 'tech');
    }

}
