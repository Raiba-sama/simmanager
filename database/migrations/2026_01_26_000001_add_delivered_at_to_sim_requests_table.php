<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sim_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('sim_requests', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('admin_processed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sim_requests', function (Blueprint $table) {
            if (Schema::hasColumn('sim_requests', 'delivered_at')) {
                $table->dropColumn('delivered_at');
            }
        });
    }
};
