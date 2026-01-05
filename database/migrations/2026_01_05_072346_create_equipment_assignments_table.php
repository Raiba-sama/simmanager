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
        Schema::create('equipment_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assigned_to_agency_id')->nullable()->constrained('agencies')->onDelete('set null');
            $table->foreignId('assigned_by')->constrained('users')->onDelete('restrict');
            $table->dateTime('assigned_at');
            $table->dateTime('returned_at')->nullable();
            $table->text('return_reason')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('transmission_sheet_id')->nullable();
            $table->timestamps();
            
            $table->index('equipment_id');
            $table->index('assigned_to_user_id');
            $table->index('assigned_to_agency_id');
            $table->index('transmission_sheet_id');
            // Note: La clé étrangère transmission_sheet_id sera ajoutée dans une migration ultérieure
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_assignments');
    }
};
