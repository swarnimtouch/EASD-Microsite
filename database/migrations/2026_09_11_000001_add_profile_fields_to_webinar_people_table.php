<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('webinar_people', function (Blueprint $table) {
            $table->string('qualifications')->nullable()->after('designation');
            $table->string('current_position')->nullable()->after('qualifications');
            $table->string('institution')->nullable()->after('current_position');
            $table->string('country')->nullable()->after('institution');
        });
    }

    public function down(): void
    {
        Schema::table('webinar_people', function (Blueprint $table) {
            $table->dropColumn(['qualifications', 'current_position', 'institution', 'country']);
        });
    }
};
