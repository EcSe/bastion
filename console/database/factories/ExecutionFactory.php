<?php

namespace Database\Factories;

use App\Models\Recipe;
use App\Models\Target;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Execution>
 */
class ExecutionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'recipe_id' => Recipe::factory(),
            'target_id' => Target::factory(),
            'status' => 'queued',
            'requested_by' => User::factory(),
            'params' => [
                'branch' => 'main',
            ],
            'started_at' => null,
            'finished_at' => null,
            'exit_code' => null,
            'error_summary' => null,
        ];
    }

    public function running(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'running',
            'started_at' => now(),
            'finished_at' => null,
            'exit_code' => null,
            'error_summary' => null,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'failed',
            'started_at' => now()->subMinute(),
            'finished_at' => now(),
            'exit_code' => 1,
            'error_summary' => fake()->sentence(),
        ]);
    }
}
