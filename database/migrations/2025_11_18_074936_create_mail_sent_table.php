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
        Schema::create('mail_sent', function (Blueprint $table) {
            $table->id();
            $table->string('thread_id')->nullable();
            $table->json('label_ids')->nullable();
            $table->string('request_type')->nullable();
            $table->string('request_number')->nullable();
            $table->string('request_matricule')->nullable();
            $table->string('request_name')->nullable();
            $table->string('sim_iccid')->nullable();
            $table->string('sim_assign_to')->nullable();
            $table->string('message_subject')->nullable();
            $table->text('message_corps')->nullable();
            $table->unsignedBigInteger('request_id')->nullable();
            $table->text('operator_response')->nullable();
            $table->string('status_after_sent')->nullable();
            $table->timestamps();
            
            $table->foreign('request_id')->references('id')->on('sim_requests')->onDelete('set null');
            $table->index('request_id');
            $table->index('request_number');
            $table->index('thread_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_sent');
    }
};
