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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('sender_id');
            $table->enum('sender_type', ['nurse', 'healthcare']);
            $table->text('message');
            $table->enum('message_type', ['text', 'file', 'image', 'system'])->default('text');
            $table->string('file_url')->nullable();
            $table->string('file_name')->nullable();
            $table->integer('file_size')->nullable();
            $table->boolean('is_read')->default(0);
            $table->timestamp('read_at')->nullable();
            $table->boolean('deleted_by_sender')->default(0);
            $table->boolean('deleted_by_receiver')->default(0);
            $table->boolean('edited')->default(0);
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();

            $table->index('conversation_id');
            $table->index('sender_id');
            $table->index('is_read');
            $table->index('created_at');

            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
