<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description')->nullable();
            $table->unsignedInteger('sequence_order')->default(1)->index();
            $table->enum('content_type', ['pdf', 'external_url'])->default('pdf');
            $table->enum('content_source', ['upload', 'url'])->default('upload');
            $table->string('file_path')->nullable();
            $table->string('original_file_name')->nullable();
            $table->string('content_url', 2048)->nullable();
            $table->dateTime('release_at')->nullable()->index();
            $table->unsignedSmallInteger('minimum_viewing_minutes')->default(0);
            $table->enum('status', ['draft', 'active', 'inactive'])->default('draft')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
