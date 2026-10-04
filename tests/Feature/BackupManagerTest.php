<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\File;

class BackupManagerTest extends TestCase
{
    /** @test */
    public function non_admin_is_forbidden_from_accessing_backups()
    {
        $graduate = User::factory()->create([
            'role' => 'graduate',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $response = $this->actingAs($graduate)->get('/admin/backup');
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
    }

    /** @test */
    public function admin_can_view_backup_dashboard()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin/backup');
        $response->assertStatus(200);
        $response->assertSee('إدارة النسخ الاحتياطي واستعادة الطوارئ');
    }

    /** @test */
    public function path_traversal_is_blocked_on_download()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'is_approved' => true,
        ]);

        // Attempting to download .env or non-.sql files via traversal
        $response = $this->actingAs($admin)->get('/admin/backup/download/..%2F..%2F.env');
        $response->assertStatus(404);
    }

    /** @test */
    public function admin_can_download_valid_sql_backup()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $backupDir = storage_path('app/backups');
        if (!File::isDirectory($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $dummyBackup = $backupDir . '/test-backup-sample.sql';
        file_put_contents($dummyBackup, '-- Test SQL Content');

        $response = $this->actingAs($admin)->get('/admin/backup/download/test-backup-sample.sql');
        $response->assertStatus(200);
        $this->assertStringContainsString('test-backup-sample.sql', (string)$response->headers->get('content-disposition'));

        @unlink($dummyBackup);
    }
}
