<?php

namespace Database\Factories;

use App\Models\TrainingApplication;
use App\Models\Training;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TrainingApplication>
 */
class TrainingApplicationFactory extends Factory
{
    protected $model = TrainingApplication::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'training_id' => Training::factory(),
            'user_id' => User::factory(),
            'status' => 'pending',
            'message' => fake()->sentence(),
            'applied_at' => now(),
        ];
    }
}
