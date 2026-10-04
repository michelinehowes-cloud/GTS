<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database {--keep=10 : عدد أحدث النسخ الاحتياطية المراد الاحتفاظ بها}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إنشاء نسخة احتياطية كاملة وشاملة من قاعدة بيانات المنظومة للطوارئ';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔄 بدء إنشاء النسخة الاحتياطية لقاعدة البيانات...');

        $connection = config('database.default');
        $dbName = config("database.connections.{$connection}.database");
        $dbUser = config("database.connections.{$connection}.username");
        $dbPass = config("database.connections.{$connection}.password");
        $dbHost = config("database.connections.{$connection}.host", '127.0.0.1');
        $dbPort = config("database.connections.{$connection}.port", '3306');

        $backupDir = storage_path('app/backups');
        if (!File::isDirectory($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = date('Y-m-d_H-i-s');
        $filename = "backup-{$dbName}-{$timestamp}.sql";
        $filepath = $backupDir . DIRECTORY_SEPARATOR . $filename;

        // Try mysqldump if available
        $mysqldumpPath = $this->findMysqldump();
        $success = false;

        if ($mysqldumpPath) {
            $this->line("⚡ استخدام محرك mysqldump المكتشف: {$mysqldumpPath}");
            $cmd = sprintf(
                '"%s" --host=%s --port=%s --user=%s %s %s > "%s"',
                $mysqldumpPath,
                escapeshellarg($dbHost),
                escapeshellarg($dbPort),
                escapeshellarg($dbUser),
                $dbPass ? '--password=' . escapeshellarg($dbPass) : '',
                escapeshellarg($dbName),
                $filepath
            );

            exec($cmd, $output, $returnVar);
            if ($returnVar === 0 && file_exists($filepath) && filesize($filepath) > 0) {
                $success = true;
            }
        }

        // Fallback to pure PDO backup if mysqldump is not available
        if (!$success) {
            $this->line('🛡️ استخدام المحرك البرمجي الداخلي (PHP Native PDO Backup Engine)...');
            $success = $this->createPdoBackup($filepath, $dbName);
        }

        if ($success) {
            $sizeKb = round(filesize($filepath) / 1024, 2);
            $this->info("✅ تم بنجاح إنشاء النسخة الاحتياطية الكاملة!");
            $this->info("📁 مسار الحفظ: {$filepath}");
            $this->info("📊 الحجم: {$sizeKb} KB");

            // تنظيف النسخ القديمة بناءً على سياسة الاحتفاظ
            $this->applyRetentionPolicy($backupDir, (int) $this->option('keep'));
            return 0;
        }

        $this->error('❌ حدث خطأ أثناء محاولة إنشاء النسخة الاحتياطية.');
        return 1;
    }

    /**
     * البحث عن مسار mysqldump
     */
    protected function findMysqldump(): ?string
    {
        $possiblePaths = [
            'mysqldump',
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\laragon\\bin\\mysql\\current\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 5.7\\bin\\mysqldump.exe',
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
        ];

        foreach ($possiblePaths as $path) {
            if ($path === 'mysqldump') {
                exec('mysqldump --version 2>&1', $output, $res);
                if ($res === 0) return 'mysqldump';
            } elseif (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * إنشاء نسخة احتياطية برمجية بالكامل عبر PDO
     */
    protected function createPdoBackup(string $filepath, string $dbName): bool
    {
        try {
            $pdo = DB::connection()->getPdo();
            $tables = [];
            $result = $pdo->query('SHOW TABLES');
            while ($row = $result->fetch(\PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }

            $sql = "-- ============================================================\n";
            $sql .= "-- GTO Graduate Training System - Automated Emergency Database Backup\n";
            $sql .= "-- Database: {$dbName}\n";
            $sql .= "-- Generation Date: " . date('Y-m-d H:i:s') . "\n";
            $sql .= "-- ============================================================\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n";
            $sql .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
            $sql .= "SET time_zone = \"+00:00\";\n\n";

            foreach ($tables as $table) {
                // Table structure
                $sql .= "-- ------------------------------------------------------------\n";
                $sql .= "-- Table structure for table `{$table}`\n";
                $sql .= "-- ------------------------------------------------------------\n";
                $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
                
                $createTableQuery = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_NUM);
                $sql .= $createTableQuery[1] . ";\n\n";

                // Table data
                $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(\PDO::FETCH_ASSOC);
                if (!empty($rows)) {
                    $sql .= "-- Data for table `{$table}`\n";
                    $columns = array_keys($rows[0]);
                    $quotedCols = array_map(fn($c) => "`{$c}`", $columns);
                    $colList = implode(', ', $quotedCols);

                    $chunks = array_chunk($rows, 200);
                    foreach ($chunks as $chunk) {
                        $valuesList = [];
                        foreach ($chunk as $row) {
                            $escapedValues = array_map(function ($val) use ($pdo) {
                                if (is_null($val)) return 'NULL';
                                return $pdo->quote($val);
                            }, array_values($row));
                            $valuesList[] = '(' . implode(', ', $escapedValues) . ')';
                        }
                        $sql .= "INSERT INTO `{$table}` ({$colList}) VALUES\n" . implode(",\n", $valuesList) . ";\n";
                    }
                    $sql .= "\n";
                }
            }

            $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";
            $sql .= "-- Backup Completed Successfully.\n";

            file_put_contents($filepath, $sql);
            return true;
        } catch (\Throwable $e) {
            $this->error('PDO Backup Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * سياسة الاحتفاظ بالنسخ وحذف القديمة
     */
    protected function applyRetentionPolicy(string $backupDir, int $keepCount)
    {
        $files = glob($backupDir . '/*.sql');
        if (count($files) > $keepCount) {
            usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
            $filesToDelete = array_slice($files, $keepCount);
            foreach ($filesToDelete as $file) {
                @unlink($file);
            }
            $this->line("🧹 تم تنظيف النسخ القديمة والاحتفاظ بأحدث {$keepCount} نسخ احتياطية.");
        }
    }
}
