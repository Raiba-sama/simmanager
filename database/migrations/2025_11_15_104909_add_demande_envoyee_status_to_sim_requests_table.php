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
        // Pour modifier un enum dans MySQL, on doit d'abord le supprimer puis le recréer
        DB::statement("ALTER TABLE sim_requests MODIFY COLUMN status ENUM(
            'en_attente', 
            'validee', 
            'rejetee',
            'demande_envoyee',
            'pending',
            'refused',
            'accepted'
        ) DEFAULT 'en_attente'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Retirer le statut 'demande_envoyee' de l'enum
        DB::statement("ALTER TABLE sim_requests MODIFY COLUMN status ENUM(
            'en_attente', 
            'validee', 
            'rejetee',
            'pending',
            'refused',
            'accepted'
        ) DEFAULT 'en_attente'");
    }
};
