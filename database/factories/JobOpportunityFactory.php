<?php

namespace Database\Factories;

use App\Models\JobOpportunity;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobOpportunity>
 */
class JobOpportunityFactory extends Factory
{
    protected $model = JobOpportunity::class;

    public function definition()
    {
        return [
            'title' => fake()->jobTitle(),
            'description' => fake()->paragraph(),
            'type' => 'job',
            'contract_type' => 'full_time',
            'company_id' => Company::factory(),
            'created_by' => User::factory(),
            'location' => 'Tripoli',
            'seats' => 2,
            'status' => 'open',
            'required_specializations' => ['Computer Science', 'Information Technology'],
            'required_skills' => ['PHP', 'MySQL'],
            'salary' => 2500.00,
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'application_deadline' => now()->addDays(15),
        ];
    }
}
