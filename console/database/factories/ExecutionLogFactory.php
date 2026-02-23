<?php

namespace Database\Factories;

use App\Models\Execution;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExecutionLog>
 */
class ExecutionLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'execution_id' => Execution::factory(),
            'ts' => now(),
            'stream' => fake()->randomElement(['stdout', 'stderr', 'system']),
            'line' => fake()->sentence(),
        ];
    }
}
