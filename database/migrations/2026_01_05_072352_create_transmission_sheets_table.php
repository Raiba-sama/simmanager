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
        Schema::create('transmission_sheets', function (Blueprint $table) {
            $table->id();
            $table->string('sheet_number')->unique();
            $table->enum('type', ['assignment', 'return', 'transfer'])->default('assignment');
            $table->foreignId('from_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('from_agency_id')->nullable()->constrained('agencies')->onDelete('set null');
            $table->foreignId('to_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('to_agency_id')->nullable()->constrained('agencies')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->date('transmission_date');
            $table->enum('status', ['draft', 'pending', 'completed', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->boolean('signed_by_recipient')->default(false);
            $table->dateTime('signed_at')->nullable();
            $table->text('recipient_signature')->nullable();
            $table->timestamps();
            
            $table->index('sheet_number');
            $table->index('status');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transmission_sheets');
    }
};
