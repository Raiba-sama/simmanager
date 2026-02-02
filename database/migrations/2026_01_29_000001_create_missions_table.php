<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('missions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type'); // formation, audit, support_technique, deploiement, maintenance, autre
            $table->foreignId('agency_id')->constrained('agencies')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->unsignedSmallInteger('number_of_days')->nullable();
            $table->string('status')->default('planned'); // planned, in_progress, completed, cancelled
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('missions');
    }
};
