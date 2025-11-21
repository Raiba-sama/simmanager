<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_identifier'); // matricule or id
            $table->string('action'); // login, create_request, approve_request, etc.
            $table->string('table_name')->nullable();
            $table->unsignedBigInteger('record_id')->nullable();
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index('user_identifier');
            $table->index('action');
            $table->index('table_name');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};

