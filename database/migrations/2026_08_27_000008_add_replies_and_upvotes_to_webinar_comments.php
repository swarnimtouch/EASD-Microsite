<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('webinar_comments', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('user_id')->constrained('webinar_comments')->cascadeOnDelete();
        });

        Schema::create('webinar_comment_upvotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webinar_comment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['webinar_comment_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webinar_comment_upvotes');
        Schema::table('webinar_comments', fn (Blueprint $table) => $table->dropConstrainedForeignId('parent_id'));
    }
};
