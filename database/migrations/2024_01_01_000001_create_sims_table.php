<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sims', function (Blueprint $table) {
            $table->id();
            $table->string('iccid')->unique();
            $table->string('phone_number')->nullable();
            $table->enum('status', ['libre', 'attribue', 'suspendu'])->default('libre');
            $table->string('operator')->nullable();
            $table->string('plan_type')->nullable();
            $table->decimal('monthly_cost', 10, 2)->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->string('assigned_to_matricule')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index('iccid');
            $table->index('status');
            $table->index('assigned_to');
            $table->index('assigned_to_matricule');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sims');
    }
};

