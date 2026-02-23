<?php

namespace Database\Factories;

use App\Models\Execution;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Approval>
 */
class ApprovalFactory extends Factory
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
            'requested_by' => User::factory(),
            'status' => 'pending',
            'risk_level' => fake()->numberBetween(0, 3),
            'summary' => fake()->sentence(),
            'expires_at' => now()->addMinutes(30),
            'approved_at' => null,
            'approved_by' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => User::factory(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'rejected',
            'approved_at' => null,
            'approved_by' => null,
        ]);
    }
}
