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
        Schema::create('equipment_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->enum('action', ['created', 'assigned', 'returned', 'transferred', 'status_changed', 'maintenance', 'updated'])->default('updated');
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->foreignId('performed_by')->constrained('users')->onDelete('restrict');
            $table->text('notes')->nullable();
            $table->timestamp('created_at');
            
            $table->index('equipment_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_history');
    }
};
