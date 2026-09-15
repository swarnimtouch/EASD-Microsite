<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('countries') && DB::table('countries')->doesntExist()) {
            $sql = file_get_contents(database_path('sql/countries.sql'));
            $start = strpos($sql, 'INSERT INTO `countries`');
            $marker = strpos($sql, '-- Indexes for dumped tables', $start);
            $insert = trim(substr($sql, $start, $marker - $start));

            DB::unprepared($insert);
        }

        Schema::dropIfExists('program_countries');
    }

    public function down(): void
    {
        if (!Schema::hasTable('program_countries')) {
            Schema::create('program_countries', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
            });
        }
    }
};
