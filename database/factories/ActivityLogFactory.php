<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Post;
use App\Models\Category;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $loggables = [
            Post::class,
            Category::class,
        ];

        $selectedModel = fake()->randomElement($loggables);

        return [
            "action" => fake()->randomElement(["CREATE", "READ", "UPDATE", "DELETE"]),
            "entity_type" => $selectedModel,
            "entity_id" => rand(1, $selectedModel::count()),
            "changed_fields" => json_encode([]),
            "actor" => fake()->userName(),
        ];
    }
}
