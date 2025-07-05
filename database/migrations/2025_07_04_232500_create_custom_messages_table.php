<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('custom_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');  // Friendly name for admin
            $table->enum('message_type', ['welcome', 'fallback', 'error', 'help']);
            $table->text('content');  // The actual message content
            $table->json('conditions')->nullable();  // Time, user type, context conditions
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);  // Higher priority = selected first
            $table->string('language')->default('en');
            $table->timestamps();

            // Indexes for better performance
            $table->index(['message_type', 'is_active']);
            $table->index(['priority', 'message_type']);
            $table->index('language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_messages');
    }
};
