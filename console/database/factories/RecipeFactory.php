<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recipe>
 */
class RecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $slug = fake()->unique()->slug(2);

        return [
            'slug' => $slug,
            'name' => 'Recipe '.str($slug)->replace('-', ' ')->title(),
            'description' => fake()->sentence(),
            'risk_level' => fake()->numberBetween(0, 3),
            'timeout_sec' => fake()->numberBetween(60, 1800),
            'parameters' => [
                'branch' => [
                    'type' => 'string',
                    'enum' => ['main', 'develop'],
                ],
            ],
            'steps' => "- name: pull\n  run: git pull\n- name: health\n  run: php artisan about",
            'version' => '1.0.0',
            'is_active' => true,
        ];
    }
}
