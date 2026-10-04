<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\AuditLog;

class AdminBackupController extends Controller
{
    protected string $backupDir;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'admin') {
                abort(403, 'غير مصرح لك بالوصول إلى إدارة النسخ الاحتياطي للطوارئ.');
            }
            return $next($request);
        });

        $this->backupDir = storage_path('app/backups');
    }

    /**
     * عرض لوحة إدارة النسخ الاحتياطية
     */
    public function index()
    {
        if (!File::isDirectory($this->backupDir)) {
            File::makeDirectory($this->backupDir, 0755, true);
        }

        $files = glob($this->backupDir . '/*.sql');
        usort($files, fn($a, $b) => filemtime($b) - filemtime($a));

        $backups = [];
        $totalBytes = 0;

        foreach ($files as $file) {
            $bytes = filesize($file);
            $totalBytes += $bytes;
            $mtime = filemtime($file);

            $backups[] = [
                'filename' => basename($file),
                'size' => $this->formatBytes($bytes),
                'size_bytes' => $bytes,
                'created_at' => date('Y-m-d H:i:s', $mtime),
                'created_at_ar' => \Carbon\Carbon::createFromTimestamp($mtime)->locale('ar')->diffForHumans(),
                'is_safe' => true,
            ];
        }

        $stats = [
            'total_count' => count($backups),
            'total_size' => $this->formatBytes($totalBytes),
            'latest_backup' => !empty($backups) ? $backups[0]['created_at'] : 'لا توجد نسخ بعد',
            'latest_backup_human' => !empty($backups) ? $backups[0]['created_at_ar'] : '—',
            'next_scheduled' => 'اليوم الساعة 02:00 فجراً (تلقائياً)',
            'cloud_configured' => !empty(env('AWS_ACCESS_KEY_ID')) && !empty(env('AWS_BUCKET')),
            'cloud_disk' => env('BACKUP_CLOUD_DISK', 's3'),
        ];

        return view('admin.backup.index', compact('backups', 'stats'));
    }

    /**
     * إنشاء نسخة احتياطية فورية الآن
     */
    public function create(Request $request)
    {
        try {
            $exitCode = Artisan::call('backup:database');
            $output = Artisan::output();

            if ($exitCode === 0) {
                AuditLog::logAction(
                    'backup_create',
                    'تم إنشاء نسخة احتياطية جديدة لقاعدة البيانات يدوياً عبر لوحة الإدارة',
                    'DatabaseBackup',
                    null
                );

                return back()->with('success', 'تم إنشاء النسخة الاحتياطية بنجاح وحفظها في التخزين الآمن!');
            }

            return back()->with('error', 'حدث خطأ أثناء أخذ النسخة الاحتياطية: ' . $output);
        } catch (\Throwable $e) {
            return back()->with('error', 'فشلت العملية: ' . $e->getMessage());
        }
    }

    /**
     * تحميل النسخة الاحتياطية مباشرة إلى حاسوب المدير
     */
    public function download(string $filename)
    {
        $safeName = basename($filename);
        $filepath = $this->backupDir . DIRECTORY_SEPARATOR . $safeName;

        if (!str_ends_with($safeName, '.sql') || !file_exists($filepath)) {
            abort(404, 'ملف النسخة الاحتياطية غير موجود أو غير صالح.');
        }

        AuditLog::logAction(
            'backup_download',
            "تم تحميل النسخة الاحتياطية {$safeName} إلى الحاسوب الشخصي للمدير",
            'DatabaseBackup',
            null
        );

        return response()->download($filepath, $safeName, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => 'attachment; filename="' . $safeName . '"',
        ]);
    }

    /**
     * استعادة قاعدة البيانات من نسخة محددة
     */
    public function restore(Request $request, string $filename)
    {
        $safeName = basename($filename);
        $filepath = $this->backupDir . DIRECTORY_SEPARATOR . $safeName;

        if (!str_ends_with($safeName, '.sql') || !file_exists($filepath)) {
            return back()->with('error', 'ملف النسخة الاحتياطية غير موجود.');
        }

        try {
            // تنفيذ الاستعادة عبر أمر الآرتيزان المعزول
            $exitCode = Artisan::call('backup:restore', [
                'file' => $safeName,
                '--no-interaction' => true,
            ]);

            if ($exitCode === 0) {
                AuditLog::logAction(
                    'backup_restore',
                    "تمت استعادة قاعدة بيانات المنظومة بنجاح من النسخة: {$safeName}",
                    'DatabaseBackup',
                    null
                );

                return back()->with('success', "تمت استعادة قاعدة البيانات بنجاح من النسخة: {$safeName}!");
            }

            return back()->with('error', 'فشلت عملية الاستعادة: ' . Artisan::output());
        } catch (\Throwable $e) {
            return back()->with('error', 'فشلت عملية الاستعادة: ' . $e->getMessage());
        }
    }

    /**
     * حذف نسخة احتياطية
     */
    public function destroy(string $filename)
    {
        $safeName = basename($filename);
        $filepath = $this->backupDir . DIRECTORY_SEPARATOR . $safeName;

        if (str_ends_with($safeName, '.sql') && file_exists($filepath)) {
            @unlink($filepath);

            AuditLog::logAction(
                'backup_delete',
                "تم حذف النسخة الاحتياطية {$safeName}",
                'DatabaseBackup',
                null
            );

            return back()->with('success', 'تم حذف ملف النسخة الاحتياطية بنجاح.');
        }

        return back()->with('error', 'الملف غير موجود.');
    }

    /**
     * رفع نسخة احتياطية إلى التخزين السحابي (S3 / Cloudflare R2 / Drive)
     */
    public function uploadCloud(string $filename)
    {
        $safeName = basename($filename);
        $filepath = $this->backupDir . DIRECTORY_SEPARATOR . $safeName;

        if (!str_ends_with($safeName, '.sql') || !file_exists($filepath)) {
            return back()->with('error', 'الملف غير موجود.');
        }

        $cloudDisk = env('BACKUP_CLOUD_DISK', 's3');

        try {
            if (!config("filesystems.disks.{$cloudDisk}.key") || !config("filesystems.disks.{$cloudDisk}.bucket")) {
                return back()->with('error', 'لم يتم ضبط إعدادات التخزين السحابي (AWS_ACCESS_KEY_ID و AWS_BUCKET) في ملف .env بعد.');
            }

            Storage::disk($cloudDisk)->putFileAs('backups', new \Illuminate\Http\File($filepath), $safeName);

            AuditLog::logAction(
                'backup_cloud_upload',
                "تم رفع النسخة الاحتياطية {$safeName} إلى السحابة ({$cloudDisk})",
                'DatabaseBackup',
                null
            );

            return back()->with('success', "تم بنجاح رفع النسخة {$safeName} إلى التخزين السحابي الخارجي ({$cloudDisk})!");
        } catch (\Throwable $e) {
            return back()->with('error', 'تعذر الرفع إلى السحابة: ' . $e->getMessage());
        }
    }

    /**
     * تنسيق حجم الملف
     */
    protected function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
