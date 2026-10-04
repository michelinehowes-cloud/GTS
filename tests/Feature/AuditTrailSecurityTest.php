<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Str;

class AuditTrailSecurityTest extends TestCase
{
    protected function fakePassword(): string
    {
        return 'Sec_' . Str::random(12) . '1!';
    }

    /** @test */
    public function audit_log_is_immutable_and_cannot_be_updated()
    {
        $log = AuditLog::create([
            'action' => 'security_test_action',
            'entity' => 'SecurityTest',
            'entity_id' => 999,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit Test Agent',
            'timestamp' => now(),
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('محاولة أمنية محظورة: سجل العمليات والرقابة محصن ضد التعديل');

        $log->update(['action' => 'tampered_action']);
    }

    /** @test */
    public function audit_log_is_immutable_and_cannot_be_deleted()
    {
        $log = AuditLog::create([
            'action' => 'security_test_delete_action',
            'entity' => 'SecurityTest',
            'entity_id' => 999,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit Test Agent',
            'timestamp' => now(),
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('محاولة أمنية محظورة: سجل العمليات والرقابة محصن ضد الحذف');

        $log->delete();
    }

    /** @test */
    public function successful_login_listener_records_audit_trail()
    {
        $user = User::factory()->create([
            'email' => 'audittest_' . uniqid() . '@example.com',
            'password' => Hash::make($this->fakePassword()),
            'role' => 'admin',
        ]);

        event(new Login('web', $user, false));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'auth_login_success',
            'entity' => 'User',
            'entity_id' => $user->id,
        ]);
    }

    /** @test */
    public function failed_login_listener_records_audit_trail()
    {
        $testEmail = 'intruder_' . uniqid() . '@unknown.com';

        event(new Failed('web', null, ['email' => $testEmail, 'password' => $this->fakePassword()]));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'auth_login_failed',
            'entity' => 'Security',
        ]);
    }

    /** @test */
    public function logout_listener_records_audit_trail()
    {
        $user = User::factory()->create([
            'email' => 'logout_' . uniqid() . '@example.com',
            'role' => 'graduate',
        ]);

        event(new Logout('web', $user));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'auth_logout',
            'entity' => 'User',
            'entity_id' => $user->id,
        ]);
    }

    /** @test */
    public function unauthorized_admin_route_access_is_logged_as_security_violation()
    {
        $user = User::factory()->create([
            'role' => 'graduate',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/admin/users');

        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'security_unauthorized_admin_access',
            'entity' => 'Security',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function auditable_trait_automatically_records_model_updates_with_clean_diffs()
    {
        $user = User::factory()->create([
            'name' => 'Original Name ' . uniqid(),
            'role' => 'graduate',
        ]);

        $oldName = $user->name;
        $newName = 'Updated Auditable Name ' . uniqid();

        $user->update([
            'name' => $newName,
            'password' => Hash::make($this->fakePassword()), // Sensitive field should NOT be stored in diff
        ]);

        $log = AuditLog::where('action', 'user_updated')
            ->where('entity', 'User')
            ->where('entity_id', $user->id)
            ->latest('timestamp')
            ->first();

        $this->assertNotNull($log, 'Audit log for user update should exist.');
        $this->assertEquals($oldName, $log->old_values['name'] ?? null);
        $this->assertEquals($newName, $log->new_values['name'] ?? null);

        // Verify password is NOT in diff
        $this->assertArrayNotHasKey('password', $log->old_values ?? []);
        $this->assertArrayNotHasKey('password', $log->new_values ?? []);
    }

    /** @test */
    public function admin_can_view_audit_logs_dashboard_and_filters()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.audit-logs'));
        $response->assertStatus(200);
        $response->assertSee('سجل الرقابة والعمليات');
        $response->assertSee('سجل غير قابل للتعديل');

        // Test filter by security action
        $filterResponse = $this->actingAs($admin)->get(route('admin.reports.audit-logs', ['action' => 'security']));
        $filterResponse->assertStatus(200);
    }
}
