<?php

namespace Database\Factories;

use App\Models\Nomination;
use App\Models\JobOpportunity;
use App\Models\GraduateData;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Nomination>
 */
class NominationFactory extends Factory
{
    protected $model = Nomination::class;

    public function definition()
    {
        return [
            'job_opportunity_id' => JobOpportunity::factory(),
            'graduate_id' => GraduateData::factory(),
            'nominated_by' => User::factory(),
            'status' => 'pending',
            'nominated_at' => now(),
        ];
    }
}
