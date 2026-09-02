<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Permission;

class RbacSecurityTest extends TestCase
{
    /** @test */
    public function super_admin_bypasses_all_gates()
    {
        $superAdmin = User::find(1);
        if (!$superAdmin) {
            $superAdmin = User::factory()->create([
                'id' => 1,
                'role' => 'admin',
                'is_active' => true,
                'is_approved' => true,
            ]);
        }

        $this->assertTrue($superAdmin->isAdmin());
        $this->assertTrue($superAdmin->hasPermission('any.random.permission'));
        $this->assertTrue($superAdmin->can('viewAny', \App\Models\Company::class));
    }

    /** @test */
    public function staff_with_permission_can_access_authorized_action()
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $permission = Permission::firstOrCreate(
            ['name' => 'companies.view'],
            ['display_name' => 'عرض الشركات', 'module' => 'companies']
        );

        $staff->givePermission('companies.view');

        $this->assertTrue($staff->hasPermission('companies.view'));
        $this->assertFalse($staff->hasPermission('companies.create'));
    }

    /** @test */
    public function user_without_permission_is_denied()
    {
        $graduate = User::factory()->create([
            'role' => 'graduate',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $this->assertFalse($graduate->hasPermission('users.manage'));
        $this->assertFalse($graduate->hasPermission('companies.create'));
    }

    /** @test */
    public function protected_super_admin_cannot_be_deleted()
    {
        $superAdmin = User::find(1);
        if (!$superAdmin) {
            $superAdmin = User::factory()->create([
                'id' => 1,
                'role' => 'admin',
                'is_active' => true,
                'is_approved' => true,
            ]);
        }

        $otherAdmin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $response = $this->actingAs($otherAdmin)->delete("/admin/users/1");

        $response->assertRedirect();
        $response->assertSessionHas('error', 'محاولة محظورة: لا يمكن حذف حساب مدير النظام الأساسي نهائياً.');
        $this->assertDatabaseHas('users', ['id' => 1]);
    }

    /** @test */
    public function sync_permissions_updates_user_privileges_cleanly()
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'is_active' => true,
            'is_approved' => true,
        ]);

        Permission::firstOrCreate(['name' => 'graduates.view'], ['display_name' => 'عرض الخريجين', 'module' => 'graduates']);
        Permission::firstOrCreate(['name' => 'graduates.create'], ['display_name' => 'إضافة خريج', 'module' => 'graduates']);

        $staff->syncPermissions(['graduates.view']);
        $this->assertTrue($staff->hasPermission('graduates.view'));
        $this->assertFalse($staff->hasPermission('graduates.create'));

        $staff->syncPermissions(['graduates.view', 'graduates.create']);
        $this->assertTrue($staff->hasPermission('graduates.view'));
        $this->assertTrue($staff->hasPermission('graduates.create'));
    }
}
