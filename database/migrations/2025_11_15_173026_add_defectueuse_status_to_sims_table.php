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
        // Pour modifier un enum dans MySQL, on doit utiliser DB::statement
        DB::statement("ALTER TABLE sims MODIFY COLUMN status ENUM(
            'libre', 
            'attribue', 
            'suspendu',
            'defectueuse'
        ) DEFAULT 'libre'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Retirer le statut 'defectueuse' de l'enum
        DB::statement("ALTER TABLE sims MODIFY COLUMN status ENUM(
            'libre', 
            'attribue', 
            'suspendu'
        ) DEFAULT 'libre'");
    }
};
