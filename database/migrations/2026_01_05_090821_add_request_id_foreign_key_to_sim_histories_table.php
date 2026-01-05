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
        // Vérifier que la table sim_requests existe avant d'ajouter la clé étrangère
        if (Schema::hasTable('sim_requests')) {
            Schema::table('sim_histories', function (Blueprint $table) {
                // Vérifier si la clé étrangère n'existe pas déjà
                $foreignKeys = Schema::getConnection()
                    ->getDoctrineSchemaManager()
                    ->listTableForeignKeys('sim_histories');
                
                $foreignKeyExists = false;
                foreach ($foreignKeys as $foreignKey) {
                    if ($foreignKey->getName() === 'sim_histories_request_id_foreign') {
                        $foreignKeyExists = true;
                        break;
                    }
                }
                
                if (!$foreignKeyExists) {
                    $table->foreign('request_id')
                        ->references('id')
                        ->on('sim_requests')
                        ->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sim_histories', function (Blueprint $table) {
            $table->dropForeign(['request_id']);
        });
    }
};
