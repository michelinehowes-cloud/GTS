<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_cameras', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('location_tag')->nullable();
            $table->enum('stream_type', ['youtube_live', 'hls_m3u8', 'rtsp_ip', 'iframe_embed', 'zoom_meet', 'external_url'])->default('youtube_live');
            $table->text('stream_url');
            $table->boolean('is_live')->default(true);
            $table->boolean('is_primary')->default(false);
            $table->integer('display_order')->default(0);
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('live_broadcast_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_live_now')->default(false);
            $table->string('broadcast_title')->default('البث المباشر — فعاليات ومعرض جامعة طرابلس');
            $table->text('broadcast_description')->nullable();
            $table->foreignId('active_camera_id')->nullable()->constrained('media_cameras')->onDelete('set null');
            $table->integer('viewers_count')->default(245);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_broadcast_settings');
        Schema::dropIfExists('media_cameras');
    }
};
