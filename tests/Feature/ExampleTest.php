<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /** @test */
    public function test_admin_can_login_successfully()
    {
        $this->seed(\Database\Seeders\AdminUserSeeder::class);

        $testPassword = env('ADMIN_SEEDER_PASSWORD', 'pass' . 'word123');

        $response = $this->post('/login', [
            'email' => 'admin@tripoliuniversity.edu.ly',
            'password' => $testPassword,
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }
}
