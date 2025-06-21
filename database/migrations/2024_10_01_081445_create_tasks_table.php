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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->date('deadline')->nullable();
            $table->integer('number_of_pauses')->nullable();
            $table->unsignedSmallInteger('estimated_time');
            $table->unsignedSmallInteger('effective_time')->nullable();
            $table->unsignedSmallInteger('rest_time')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->dateTime('paused_at')->nullable();
            $table->dateTime('resumed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('priority_id');
            $table->unsignedBigInteger('status_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('priority_id')->references('id')->on('priorities')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
