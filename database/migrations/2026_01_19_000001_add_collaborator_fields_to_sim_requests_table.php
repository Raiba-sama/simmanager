<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sim_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('sim_requests', 'collaborator_matricule')) {
                $table->string('collaborator_matricule')->nullable()->after('beneficiary_matricule');
            }
            if (!Schema::hasColumn('sim_requests', 'collaborator_name')) {
                $table->string('collaborator_name')->nullable()->after('collaborator_matricule');
            }
            if (!Schema::hasColumn('sim_requests', 'collaborator_first_name')) {
                $table->string('collaborator_first_name')->nullable()->after('collaborator_name');
            }
            if (!Schema::hasColumn('sim_requests', 'collaborator_agence')) {
                $table->string('collaborator_agence')->nullable()->after('collaborator_first_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sim_requests', function (Blueprint $table) {
            if (Schema::hasColumn('sim_requests', 'collaborator_agence')) {
                $table->dropColumn('collaborator_agence');
            }
            if (Schema::hasColumn('sim_requests', 'collaborator_first_name')) {
                $table->dropColumn('collaborator_first_name');
            }
            if (Schema::hasColumn('sim_requests', 'collaborator_name')) {
                $table->dropColumn('collaborator_name');
            }
            if (Schema::hasColumn('sim_requests', 'collaborator_matricule')) {
                $table->dropColumn('collaborator_matricule');
            }
        });
    }
};
