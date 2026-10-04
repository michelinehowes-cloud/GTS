<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RestoreDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:restore {file? : اسم ملف النسخة الاحتياطية الموجود في storage/app/backups}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'استعادة قاعدة بيانات المنظومة في حالات الطوارئ من ملف SQL احتياطي';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $backupDir = storage_path('app/backups');
        if (!File::isDirectory($backupDir)) {
            $this->error('❌ مجلد النسخ الاحتياطية غير موجود.');
            return 1;
        }

        $file = $this->argument('file');

        if (!$file) {
            $files = glob($backupDir . '/*.sql');
            if (empty($files)) {
                $this->error('❌ لا توجد أي نسخ احتياطية متوفرة في مجلد storage/app/backups');
                return 1;
            }

            usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
            $choices = array_map('basename', $files);
            $file = $this->choice('اختر ملف النسخة الاحتياطية المراد استعادته:', $choices, 0);
        }

        $filepath = $backupDir . DIRECTORY_SEPARATOR . basename($file);
        if (!file_exists($filepath)) {
            $this->error("❌ ملف النسخة الاحتياطية غير موجود: {$filepath}");
            return 1;
        }

        $this->warn("⚠️ تحذير شديد الأهمية:");
        $this->warn("هذه العملية ستقوم باستبدال كافة البيانات الحالية بقاعدة البيانات واسترجاع النسخة: {$file}");
        
        if (!$this->confirm('هل أنت متأكد من رغبتك في استعادة هذه النسخة الآن؟', false)) {
            $this->info('تم إلغاء عملية الاستعادة.');
            return 0;
        }

        $this->info('🔄 جاري استعادة قاعدة البيانات...');

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
            DB::unprepared(file_get_contents($filepath));
            DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

            $this->info('✅ تم استرجاع كافة بيانات المنظومة بنجاح تام وبكامل سلامتها!');
            return 0;
        } catch (\Throwable $e) {
            $this->error('❌ فشلت عملية الاستعادة: ' . $e->getMessage());
            return 1;
        }
    }
}
