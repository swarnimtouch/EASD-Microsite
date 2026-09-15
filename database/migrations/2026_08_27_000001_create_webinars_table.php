<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webinars', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type', 100);
            $table->unsignedSmallInteger('duration_minutes')->default(60);
            $table->text('description')->nullable();
            $table->string('speaker_name')->nullable();
            $table->text('speaker_bio')->nullable();
            $table->string('meeting_url', 2048)->nullable();
            $table->string('recording_url', 2048)->nullable();
            $table->dateTime('scheduled_at')->nullable();
            $table->boolean('publish_immediately')->default(false);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index(['status', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webinars');
    }
};
