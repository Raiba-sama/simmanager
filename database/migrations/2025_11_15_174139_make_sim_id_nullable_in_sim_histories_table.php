<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modifier la colonne sim_id pour qu'elle soit nullable
        Schema::table('sim_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('sim_id')->nullable()->change();
        });

        // Ajouter la clé étrangère pour request_id si elle n'existe pas déjà
        if (!DB::select("SELECT * FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'sim_histories' AND COLUMN_NAME = 'request_id' AND CONSTRAINT_NAME LIKE '%foreign%'")) {
            Schema::table('sim_histories', function (Blueprint $table) {
                $table->foreign('request_id')->references('id')->on('sim_requests')->onDelete('cascade');
            });
        }

        // Ajouter l'index pour request_id s'il n'existe pas déjà
        if (!DB::select("SHOW INDEX FROM sim_histories WHERE Key_name = 'sim_histories_request_id_index'")) {
            Schema::table('sim_histories', function (Blueprint $table) {
                $table->index('request_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sim_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('sim_id')->nullable(false)->change();
        });
    }
};
