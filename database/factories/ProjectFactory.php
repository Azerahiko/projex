<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
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
        return [
            'user_id' => User::factory(),
            'client_id' => null,
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'start_date' => fake()->date(),
            'end_date' => fake()->dateTimeBetween('+1 month', '+6 months')->format('Y-m-d'),
            'status' => 'aktif',
        ];
    }
}
