<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_log_activities', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique()->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_matricule')->nullable();
            $table->string('type'); // payment, refund, etc.
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('currency', 3)->default('XOF');
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            $table->index('transaction_id');
            $table->index('user_id');
            $table->index('user_matricule');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_log_activities');
    }
};

