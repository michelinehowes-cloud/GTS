<?php

use Illuminate\Database\Migrations\Migration;
use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            $seeder = new PermissionSeeder();
            $seeder->run();
            Log::info('Successfully synced all permissions via migration.');
        } catch (\Throwable $e) {
            Log::warning('PermissionSeeder auto-run in migration skipped or failed: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
