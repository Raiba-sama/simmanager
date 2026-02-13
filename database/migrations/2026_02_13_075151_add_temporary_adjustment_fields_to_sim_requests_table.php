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
        Schema::table('sim_requests', function (Blueprint $table) {
            $table->boolean('is_temporary')->default(false)->after('limite_data');
            $table->date('temporary_end_date')->nullable()->after('is_temporary');
            $table->decimal('previous_limite_credit', 10, 2)->nullable()->after('temporary_end_date');
            $table->decimal('previous_limite_data', 10, 2)->nullable()->after('previous_limite_credit');
            $table->unsignedBigInteger('previous_plan_id')->nullable()->after('previous_limite_data');
            
            $table->foreign('previous_plan_id')->references('id')->on('plans')->onDelete('set null');
            $table->index('is_temporary');
            $table->index('temporary_end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sim_requests', function (Blueprint $table) {
            $table->dropForeign(['previous_plan_id']);
            $table->dropIndex(['is_temporary']);
            $table->dropIndex(['temporary_end_date']);
            $table->dropColumn([
                'is_temporary',
                'temporary_end_date',
                'previous_limite_credit',
                'previous_limite_data',
                'previous_plan_id',
            ]);
        });
    }
};
