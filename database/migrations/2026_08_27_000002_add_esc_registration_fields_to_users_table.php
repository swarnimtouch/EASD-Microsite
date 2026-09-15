<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('hcp_confirmed')->default(false)->after('speciality');
            $table->timestamp('terms_accepted_at')->nullable()->after('hcp_confirmed');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['hcp_confirmed', 'terms_accepted_at']);
        });
    }
};
