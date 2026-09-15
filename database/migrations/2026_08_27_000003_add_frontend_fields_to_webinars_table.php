<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('webinars', function (Blueprint $table) {
            $table->string('timezone_label', 100)->nullable()->after('scheduled_at');
            $table->string('cover_image_url', 2048)->nullable()->after('recording_url');
            $table->string('pre_read_url', 2048)->nullable()->after('cover_image_url');
            $table->string('post_read_url', 2048)->nullable()->after('pre_read_url');
        });
    }

    public function down(): void
    {
        Schema::table('webinars', function (Blueprint $table) {
            $table->dropColumn(['timezone_label', 'cover_image_url', 'pre_read_url', 'post_read_url']);
        });
    }
};
