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
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'number_of_pauses')) {
                $table->unsignedSmallInteger('number_of_pauses')->default(0)->after('deadline');
            }
            if (!Schema::hasColumn('tasks', 'rest_time')) {
                $table->unsignedSmallInteger('rest_time')->default(0)->after('effective_time');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'number_of_pauses')) {
                $table->dropColumn('number_of_pauses');
            }
            if (Schema::hasColumn('tasks', 'rest_time')) {
                $table->dropColumn('rest_time');
            }
        });
    }
};
