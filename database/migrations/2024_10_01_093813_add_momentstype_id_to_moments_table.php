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
        Schema::table('moments', function (Blueprint $table) {
            $table->foreignId('moments_type_id')->nullable()
                ->constrained()
                ->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('moments', function (Blueprint $table) {
            $table->dropForeign(['moments_type_id']);
            $table->dropColumn('moments_type_id');
        });
    }
};
