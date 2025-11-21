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
        Schema::table('users', function (Blueprint $table) {
            $table->string('lieu_affectation')->nullable()->after('fonction');
            $table->string('zone_affectation')->nullable()->after('lieu_affectation');
            $table->string('direction')->nullable()->after('zone_affectation');
            $table->string('numero_flotte')->nullable()->after('direction');
            
            $table->index('lieu_affectation');
            $table->index('zone_affectation');
            $table->index('direction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['lieu_affectation']);
            $table->dropIndex(['zone_affectation']);
            $table->dropIndex(['direction']);
            
            $table->dropColumn(['lieu_affectation', 'zone_affectation', 'direction', 'numero_flotte']);
        });
    }
};
