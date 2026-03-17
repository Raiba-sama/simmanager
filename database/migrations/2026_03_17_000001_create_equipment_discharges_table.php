<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_discharges', function (Blueprint $table) {
            $table->id();
            $table->string('discharge_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('generated_by')->constrained('users')->restrictOnDelete();
            $table->enum('reason', ['depart', 'remplacement', 'nouvelle_attribution'])->index();
            $table->date('effective_date')->nullable();
            $table->text('notes')->nullable();
            $table->json('equipment_snapshot');
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_discharges');
    }
};

