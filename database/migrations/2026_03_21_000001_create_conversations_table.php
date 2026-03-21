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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('subject')->nullable();
            $table->unsignedBigInteger('job_id')->nullable()->comment('Related job posting if any');
            $table->unsignedBigInteger('nurse_id');
            $table->unsignedBigInteger('healthcare_id');
            $table->unsignedBigInteger('last_message_id')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->boolean('nurse_deleted')->default(0);
            $table->boolean('healthcare_deleted')->default(0);
            $table->boolean('nurse_blocked')->default(0);
            $table->boolean('healthcare_blocked')->default(0);
            $table->enum('status', ['active', 'archived', 'closed'])->default('active');
            $table->timestamps();

            $table->index('nurse_id');
            $table->index('healthcare_id');
            $table->index('job_id');
            $table->index('last_message_at');

            $table->foreign('nurse_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('healthcare_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
