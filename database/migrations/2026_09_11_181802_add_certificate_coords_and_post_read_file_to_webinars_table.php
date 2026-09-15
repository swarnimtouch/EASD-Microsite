<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('webinars', function (Blueprint $table) {
            $table->decimal('certificate_name_x', 8, 2)->nullable()->after('certificate_template_path');
            $table->decimal('certificate_name_y', 8, 2)->nullable()->default(292.00)->after('certificate_name_x');
            $table->unsignedInteger('certificate_font_size')->nullable()->default(28)->after('certificate_name_y');
            $table->string('post_read_file_path')->nullable()->after('post_read_url');
        });
    }

    public function down(): void
    {
        Schema::table('webinars', function (Blueprint $table) {
            $table->dropColumn([
                'certificate_name_x',
                'certificate_name_y',
                'certificate_font_size',
                'post_read_file_path',
            ]);
        });
    }
};
