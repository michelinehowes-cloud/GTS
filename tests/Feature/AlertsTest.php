<?php

namespace Tests\Feature;

use App\Services\AlertService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AlertsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['alerts.force_in_tests' => true, 'alerts.email' => 'ops@example.com', 'alerts.max_per_hour' => 10]);
        Cache::flush();
        Mail::fake();
    }

    /** @test */
    public function an_alert_is_emailed_once_and_repeats_are_throttled()
    {
        $this->assertTrue(AlertService::notify('db-down', 'قاعدة البيانات', 'تفاصيل'));
        $this->assertFalse(AlertService::notify('db-down', 'قاعدة البيانات', 'تفاصيل'));
        $this->assertTrue(AlertService::notify('other-problem', 'مشكلة أخرى', 'تفاصيل'));
    }

    /** @test */
    public function alerts_per_hour_are_capped()
    {
        config(['alerts.max_per_hour' => 2]);

        $this->assertTrue(AlertService::notify('a', 's', 'b'));
        $this->assertTrue(AlertService::notify('b', 's', 'b'));
        $this->assertFalse(AlertService::notify('c', 's', 'b'));
    }

    /** @test */
    public function no_alerts_are_sent_from_tests_by_default()
    {
        config(['alerts.force_in_tests' => false]);

        $this->assertFalse(AlertService::notify('x', 's', 'b'));
    }
}
