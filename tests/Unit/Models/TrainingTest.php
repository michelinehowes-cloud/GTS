<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Training;
use App\Models\User;
use App\Models\Company;
use App\Models\TrainingApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class TrainingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_training()
    {
        $company = Company::factory()->create();
        $coordinator = User::factory()->create(['role' => 'training_coordinator']);
        
        $training = Training::factory()->create([
            'title' => 'Advanced Laravel Development',
            'company_id' => $company->id,
            'coordinator_id' => $coordinator->id
        ]);

        $this->assertDatabaseHas('trainings', [
            'title' => 'Advanced Laravel Development',
            'company_id' => $company->id
        ]);
    }

    /** @test */
    public function it_belongs_to_company()
    {
        $company = Company::factory()->create();
        $training = Training::factory()->create(['company_id' => $company->id]);

        $this->assertInstanceOf(Company::class, $training->company);
        $this->assertEquals($company->id, $training->company->id);
    }

    /** @test */
    public function it_belongs_to_coordinator()
    {
        $coordinator = User::factory()->create(['role' => 'training_coordinator']);
        $training = Training::factory()->create(['coordinator_id' => $coordinator->id]);

        $this->assertInstanceOf(User::class, $training->coordinator);
        $this->assertEquals($coordinator->id, $training->coordinator->id);
    }

    /** @test */
    public function it_has_applications()
    {
        $training = Training::factory()->create();
        $application = TrainingApplication::factory()->create(['training_id' => $training->id]);

        $this->assertCount(1, $training->applications);
        $this->assertEquals($application->id, $training->applications->first()->id);
    }

    /** @test */
    public function it_returns_arabic_type()
    {
        $training = Training::factory()->create(['type' => 'workshop']);
        
        $this->assertEquals('ورشة عمل', $training->type_arabic);
    }

    /** @test */
    public function it_returns_arabic_status()
    {
        $training = Training::factory()->create(['status' => 'active']);
        
        $this->assertEquals('نشط', $training->status_arabic);
    }

    /** @test */
    public function it_checks_if_training_is_active()
    {
        $futureTraining = Training::factory()->create([
            'status' => 'active',
            'start_date' => Carbon::now()->addDays(10)
        ]);
        
        $pastTraining = Training::factory()->create([
            'status' => 'active',
            'start_date' => Carbon::now()->subDays(10)
        ]);
        
        $inactiveTraining = Training::factory()->create([
            'status' => 'inactive',
            'start_date' => Carbon::now()->addDays(10)
        ]);

        $this->assertTrue($futureTraining->is_active);
        $this->assertFalse($pastTraining->is_active);
        $this->assertFalse($inactiveTraining->is_active);
    }

    /** @test */
    public function it_calculates_available_seats()
    {
        $training = Training::factory()->create(['seats' => 10]);
        
        // No approved applications
        $this->assertEquals(10, $training->available_seats);

        // With approved applications
        TrainingApplication::factory()->count(3)->create([
            'training_id' => $training->id,
            'status' => 'approved'
        ]);
        
        $this->assertEquals(7, $training->available_seats);
    }

    /** @test */
    public function it_casts_dates_correctly()
    {
        $training = Training::factory()->create([
            'start_date' => '2023-01-15',
            'end_date' => '2023-01-20'
        ]);

        $this->assertInstanceOf(Carbon::class, $training->start_date);
        $this->assertInstanceOf(Carbon::class, $training->end_date);
    }

    /** @test */
    public function it_returns_correct_arabic_type_for_all_types()
    {
        $types = [
            'workshop' => 'ورشة عمل',
            'course' => 'دورة',
            'seminar' => 'ندوة',
            'internship' => 'تدريب عملي'
        ];

        foreach ($types as $type => $arabic) {
            $training = Training::factory()->create(['type' => $type]);
            $this->assertEquals($arabic, $training->type_arabic);
        }
    }

    /** @test */
    public function it_returns_correct_arabic_status_for_all_statuses()
    {
        $statuses = [
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'completed' => 'مكتمل'
        ];

        foreach ($statuses as $status => $arabic) {
            $training = Training::factory()->create(['status' => $status]);
            $this->assertEquals($arabic, $training->status_arabic);
        }
    }

    /** @test */
    public function it_handles_unknown_type_gracefully()
    {
        $training = Training::factory()->create(['type' => 'unknown_type']);
        
        $this->assertEquals('unknown_type', $training->type_arabic);
    }

    /** @test */
    public function it_handles_unknown_status_gracefully()
    {
        $training = Training::factory()->create(['status' => 'unknown_status']);
        
        $this->assertEquals('unknown_status', $training->status_arabic);
    }

    /** @test */
    public function it_does_not_count_pending_applications_in_available_seats()
    {
        $training = Training::factory()->create(['seats' => 5]);
        
        TrainingApplication::factory()->create([
            'training_id' => $training->id,
            'status' => 'pending'
        ]);
        
        TrainingApplication::factory()->create([
            'training_id' => $training->id,
            'status' => 'approved'
        ]);

        $this->assertEquals(4, $training->available_seats);
    }

    /** @test */
    public function it_does_not_count_rejected_applications_in_available_seats()
    {
        $training = Training::factory()->create(['seats' => 5]);
        
        TrainingApplication::factory()->create([
            'training_id' => $training->id,
            'status' => 'rejected'
        ]);
        
        TrainingApplication::factory()->create([
            'training_id' => $training->id,
            'status' => 'approved'
        ]);

        $this->assertEquals(4, $training->available_seats);
    }

    /** @test */
    public function it_returns_zero_available_seats_when_full()
    {
        $training = Training::factory()->create(['seats' => 2]);
        
        TrainingApplication::factory()->count(2)->create([
            'training_id' => $training->id,
            'status' => 'approved'
        ]);

        $this->assertEquals(0, $training->available_seats);
    }

    /** @test */
    public function it_returns_negative_available_seats_when_overbooked()
    {
        $training = Training::factory()->create(['seats' => 2]);
        
        TrainingApplication::factory()->count(3)->create([
            'training_id' => $training->id,
            'status' => 'approved'
        ]);

        $this->assertEquals(-1, $training->available_seats);
    }
}
?>