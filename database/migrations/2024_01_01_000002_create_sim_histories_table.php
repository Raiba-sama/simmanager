<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sim_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sim_id')->nullable(); // Nullable pour permettre les historiques de demandes sans SIM
            $table->string('action'); // assigned, unassigned, suspended, activated, validated, rejected, etc.
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_matricule')->nullable();
            $table->unsignedBigInteger('request_id')->nullable();
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('sim_id')->references('id')->on('sims')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('request_id')->references('id')->on('sim_requests')->onDelete('cascade');
            $table->index('sim_id');
            $table->index('user_id');
            $table->index('request_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sim_histories');
    }
};

