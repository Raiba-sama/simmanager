<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mission_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mission_id')->constrained('missions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('role_in_mission')->nullable(); // optional: chef de mission, participant, etc.
            $table->timestamps();

            $table->unique(['mission_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mission_user');
    }
};
