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
        // Vérifier que la table transmission_sheets existe avant d'ajouter la clé étrangère
        if (Schema::hasTable('transmission_sheets')) {
            Schema::table('equipment_assignments', function (Blueprint $table) {
                // Vérifier si la clé étrangère n'existe pas déjà
                $foreignKeys = Schema::getConnection()
                    ->getDoctrineSchemaManager()
                    ->listTableForeignKeys('equipment_assignments');
                
                $foreignKeyExists = false;
                foreach ($foreignKeys as $foreignKey) {
                    if ($foreignKey->getName() === 'equipment_assignments_transmission_sheet_id_foreign') {
                        $foreignKeyExists = true;
                        break;
                    }
                }
                
                if (!$foreignKeyExists) {
                    $table->foreign('transmission_sheet_id')
                        ->references('id')
                        ->on('transmission_sheets')
                        ->onDelete('set null');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment_assignments', function (Blueprint $table) {
            $table->dropForeign(['transmission_sheet_id']);
        });
    }
};
