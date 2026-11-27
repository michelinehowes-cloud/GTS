<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VisitorAccountRestrictionsTest extends TestCase
{
    use DatabaseTransactions;

    private function makeVisitor(): User
    {
        return User::factory()->create([
            'email' => 'visitor-' . uniqid() . '@example.com',
            'role' => 'visitor',
            'is_active' => true,
            'is_approved' => true,
            'password' => Hash::make('Visitor#Pass123'),
        ]);
    }

    /** @test */
    public function visitor_cannot_log_in_even_with_correct_password()
    {
        $visitor = $this->makeVisitor();

        $this->post('/login', [
            'email' => $visitor->email,
            'password' => 'Visitor#Pass123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    /** @test */
    public function existing_visitor_session_is_terminated()
    {
        $visitor = $this->makeVisitor();

        $this->actingAs($visitor)->get('/dashboard')->assertRedirect(route('login'));

        $this->assertGuest();
    }

    /** @test */
    public function visitors_are_excluded_from_staff_list()
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true, 'is_approved' => true]);
        $visitor = $this->makeVisitor();

        $this->actingAs($admin)
            ->get(route('admin.users'))
            ->assertOk()
            ->assertDontSee($visitor->email);
    }
}
