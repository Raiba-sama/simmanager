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
            $table->date('temporary_start_date')->nullable()->after('is_temporary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sim_requests', function (Blueprint $table) {
            $table->dropColumn('temporary_start_date');
        });
    }
};
