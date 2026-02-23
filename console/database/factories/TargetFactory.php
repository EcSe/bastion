<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Target>
 */
class TargetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $authMethod = fake()->randomElement(['ssh_key', 'password']);

        return [
            'name' => fake()->unique()->domainWord(),
            'host' => fake()->ipv4(),
            'port' => 22,
            'user' => fake()->userName(),
            'auth_method' => $authMethod,
            'key_path' => $authMethod === 'ssh_key' ? '/home/ops/.ssh/id_ed25519' : null,
            'secret_ref' => $authMethod === 'password' ? 'vault://targets/'.fake()->slug() : null,
            'tags' => [
                'env' => fake()->randomElement(['dev', 'staging', 'prod']),
                'role' => fake()->randomElement(['app', 'db', 'worker']),
            ],
            'is_active' => true,
        ];
    }
}
