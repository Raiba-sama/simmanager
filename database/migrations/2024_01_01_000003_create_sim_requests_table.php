<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sim_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('sim_id')->nullable();
            $table->string('requested_iccid')->nullable();
            $table->enum('request_type', ['attribution', 'suspension', 'reactivation', 'retour'])->default('attribution');
            $table->text('motif')->nullable();
            $table->text('justification')->nullable();
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->enum('status', ['en_attente', 'validee', 'rejetee'])->default('en_attente');
            $table->unsignedBigInteger('validator_id')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->json('request_details')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sim_id')->references('id')->on('sims')->onDelete('set null');
            $table->foreign('validator_id')->references('id')->on('users')->onDelete('set null');
            $table->index('request_number');
            $table->index('user_id');
            $table->index('status');
            $table->index('validator_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sim_requests');
    }
};

