<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'title' => ucwords($title),
            'slug' => str()->slug($title),
            'tagline' => fake()->sentence(8),
            'category' => fake()->randomElement(['full-stack', 'backend-api', 'dev-tool']),
            'description' => fake()->paragraphs(3, true),
            'architecture_notes' => fake()->paragraph(),
            'key_features' => [
                fake()->sentence(6),
                fake()->sentence(6),
                fake()->sentence(6),
            ],
            'tech_stack' => fake()->randomElements(
                ['Laravel 12', 'PHP 8.5', 'PostgreSQL', 'Tailwind CSS', 'Vue.js', 'Alpine.js', 'Redis', 'Docker'],
                4
            ),
            'github_url' => 'https://github.com/example/'.str()->slug($title),
            'demo_url' => 'https://demo.example.com/'.str()->slug($title),
            'system_metrics' => 'Processed '.fake()->numberBetween(10, 50).'k req/min',
            'is_featured' => true,
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }
}
