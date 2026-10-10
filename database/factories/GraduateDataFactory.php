<?php

namespace Database\Factories;

use App\Models\GraduateData;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GraduateData>
 */
class GraduateDataFactory extends Factory
{
    protected $model = GraduateData::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'national_id' => fake()->numerify('############'),
            'city' => 'Tripoli',
            'faculty' => 'Faculty of Information Technology',
            'major' => 'Software Engineering',
            'graduation_year' => 2024,
            'gpa' => 85.50,
            'added_by' => User::factory(),
            'data_source' => 'system',
            'is_active' => true,
        ];
    }
}
