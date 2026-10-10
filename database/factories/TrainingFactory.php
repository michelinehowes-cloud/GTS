<?php

namespace Database\Factories;

use App\Models\Training;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Training>
 */
class TrainingFactory extends Factory
{
    protected $model = Training::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'type' => 'workshop',
            'duration' => '30 hours',
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(20),
            'location' => 'University Hall',
            'seats' => 25,
            'status' => 'active',
            'company_id' => Company::factory(),
            'coordinator_id' => User::factory(),
            'category' => 'Technical',
        ];
    }
}
