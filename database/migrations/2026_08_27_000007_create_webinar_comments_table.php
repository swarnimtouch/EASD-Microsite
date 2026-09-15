<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webinar_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webinar_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->enum('status', ['active', 'hidden'])->default('active');
            $table->timestamps();
            $table->index(['webinar_id', 'status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webinar_comments');
    }
};
