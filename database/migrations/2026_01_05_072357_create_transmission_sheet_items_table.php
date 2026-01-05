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
        Schema::create('transmission_sheet_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transmission_sheet_id')->constrained('transmission_sheets')->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->enum('condition_at_transmission', ['new', 'excellent', 'good', 'fair', 'poor'])->default('good');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('transmission_sheet_id');
            $table->index('equipment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transmission_sheet_items');
    }
};
