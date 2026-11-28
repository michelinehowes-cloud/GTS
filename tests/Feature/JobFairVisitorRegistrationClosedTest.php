<?php

namespace Tests\Feature;

use App\Models\JobFair;
use App\Models\JobFairVisitor;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class JobFairVisitorRegistrationClosedTest extends TestCase
{
    use DatabaseTransactions;

    private function fair(string $status, string $date): JobFair
    {
        return JobFair::create([
            'title' => 'معرض اختبار ' . Str::random(4),
            'slug' => 'test-fair-' . Str::random(8),
            'event_date' => $date,
            'location' => 'طرابلس',
            'status' => $status,
            'registration_open' => true,
            'created_by' => User::factory()->create(['role' => 'admin'])->id,
        ]);
    }

    private function visitorData(string $phone): array
    {
        return ['name' => 'زائر تجريبي', 'phone' => $phone, 'visitor_type' => 'job_seeker'];
    }

    /** @test */
    public function new_visitors_cannot_register_for_a_completed_fair()
    {
        $fair = $this->fair('completed', now()->addDays(3)->toDateString());

        $this->postJson(route('job-fair.visitor.register', $fair), $this->visitorData('0911000001'))
            ->assertStatus(422)
            ->assertJson(['success' => false]);

        $this->assertSame(0, JobFairVisitor::where('job_fair_id', $fair->id)->count());
    }

    /** @test */
    public function new_visitors_cannot_register_for_a_cancelled_or_past_fair()
    {
        $cancelled = $this->fair('cancelled', now()->addDays(3)->toDateString());
        $past = $this->fair('published', now()->subDays(3)->toDateString());

        $this->postJson(route('job-fair.visitor.register', $cancelled), $this->visitorData('0911000002'))->assertStatus(422);
        $this->postJson(route('job-fair.visitor.register', $past), $this->visitorData('0911000003'))->assertStatus(422);
    }

    /** @test */
    public function a_visitor_who_registered_earlier_can_still_open_their_ticket()
    {
        $fair = $this->fair('completed', now()->subDays(1)->toDateString());
        $visitor = JobFairVisitor::create([
            'job_fair_id' => $fair->id, 'name' => 'زائر سابق', 'phone' => '0911000004',
            'visitor_type' => 'job_seeker', 'ticket_number' => 'VIS-TEST-' . Str::random(5), 'attended' => true,
        ]);

        $this->postJson(route('job-fair.visitor.register', $fair), $this->visitorData('0911000004'))
            ->assertOk()
            ->assertJson(['success' => true, 'already_registered' => true, 'ticket_number' => $visitor->ticket_number]);
    }

    /** @test */
    public function visitors_can_register_while_the_fair_is_open()
    {
        $fair = $this->fair('published', now()->addDays(3)->toDateString());

        $this->postJson(route('job-fair.visitor.register', $fair), $this->visitorData('0911000005'))
            ->assertOk()
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function the_visitor_button_is_hidden_once_the_fair_has_ended()
    {
        $open = $this->fair('published', now()->addDays(3)->toDateString());
        $ended = $this->fair('completed', now()->subDays(1)->toDateString());

        $this->get(route('job-fair.public', $open))->assertOk()->assertSee('data-bs-target="#visitorRegisterModal"', false);
        $this->get(route('job-fair.public', $ended))->assertOk()->assertDontSee('data-bs-target="#visitorRegisterModal"', false);
    }
}
