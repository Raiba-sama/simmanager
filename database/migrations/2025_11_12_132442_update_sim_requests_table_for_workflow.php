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
        // Créer la table plans d'abord si elle n'existe pas
        if (!Schema::hasTable('plans')) {
            Schema::create('plans', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('description')->nullable();
                $table->decimal('limite_credit', 10, 2)->default(0);
                $table->decimal('limite_data', 10, 2)->default(0);
                $table->decimal('monthly_cost', 10, 2)->nullable();
                $table->string('operator')->nullable();
                $table->boolean('active')->default(true);
                $table->timestamps();
                
                $table->index('active');
                $table->index('operator');
            });
        }

        Schema::table('sim_requests', function (Blueprint $table) {
            // Modifier le type ENUM pour request_type
            $table->dropColumn('request_type');
        });
        
        Schema::table('sim_requests', function (Blueprint $table) {
            $table->enum('request_type', [
                'recuperation', 
                'ajustement', 
                'desactivation', 
                'suspension', 
                'creation'
            ])->default('recuperation')->after('requested_iccid');
        });

        Schema::table('sim_requests', function (Blueprint $table) {
            // Modifier le statut pour inclure les statuts admin
            $table->dropColumn('status');
        });
        
        Schema::table('sim_requests', function (Blueprint $table) {
            $table->enum('status', [
                'en_attente', 
                'validee', 
                'rejetee',
                'pending',    // En attente chez l'opérateur
                'refused',    // Refusé par l'opérateur
                'accepted'    // Accepté par l'opérateur
            ])->default('en_attente')->after('priority');
        });

        Schema::table('sim_requests', function (Blueprint $table) {
            // Nouveaux champs pour le workflow (vérifier l'existence avant d'ajouter)
            if (!Schema::hasColumn('sim_requests', 'plan_id')) {
                $table->unsignedBigInteger('plan_id')->nullable()->after('request_type');
            }
            if (!Schema::hasColumn('sim_requests', 'limite_credit')) {
                $table->decimal('limite_credit', 10, 2)->nullable()->after('plan_id');
            }
            if (!Schema::hasColumn('sim_requests', 'limite_data')) {
                $table->decimal('limite_data', 10, 2)->nullable()->after('limite_credit');
            }
            if (!Schema::hasColumn('sim_requests', 'phone_number')) {
                $table->string('phone_number')->nullable()->after('requested_iccid');
            }
            if (!Schema::hasColumn('sim_requests', 'admin_comment')) {
                $table->text('admin_comment')->nullable()->after('rejection_reason');
            }
            if (!Schema::hasColumn('sim_requests', 'admin_id')) {
                $table->unsignedBigInteger('admin_id')->nullable()->after('validator_id');
            }
            if (!Schema::hasColumn('sim_requests', 'admin_processed_at')) {
                $table->timestamp('admin_processed_at')->nullable()->after('validated_at');
            }
            
            // Champs pour bénéficiaire (création)
            if (!Schema::hasColumn('sim_requests', 'beneficiary_name')) {
                $table->string('beneficiary_name')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('sim_requests', 'beneficiary_first_name')) {
                $table->string('beneficiary_first_name')->nullable()->after('beneficiary_name');
            }
            if (!Schema::hasColumn('sim_requests', 'beneficiary_fonction')) {
                $table->string('beneficiary_fonction')->nullable()->after('beneficiary_first_name');
            }
            if (!Schema::hasColumn('sim_requests', 'beneficiary_matricule')) {
                $table->string('beneficiary_matricule')->nullable()->after('beneficiary_fonction');
            }
        });

        // Ajouter les clés étrangères et index après avoir créé les colonnes
        Schema::table('sim_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('sim_requests', 'plan_id')) {
                return; // Ne pas continuer si plan_id n'existe pas
            }
            
            // Vérifier si les clés étrangères existent déjà
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'sim_requests' 
                AND CONSTRAINT_NAME LIKE '%plan_id%'
            ");
            
            if (empty($foreignKeys)) {
                $table->foreign('plan_id')->references('id')->on('plans')->onDelete('set null');
            }
            
            $adminForeignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'sim_requests' 
                AND CONSTRAINT_NAME LIKE '%admin_id%'
            ");
            
            if (empty($adminForeignKeys)) {
                $table->foreign('admin_id')->references('id')->on('users')->onDelete('set null');
            }
            
            // Ajouter les index s'ils n'existent pas
            $indexes = DB::select("SHOW INDEX FROM sim_requests WHERE Key_name = 'sim_requests_plan_id_index'");
            if (empty($indexes) && Schema::hasColumn('sim_requests', 'plan_id')) {
                $table->index('plan_id');
            }
            
            $phoneIndexes = DB::select("SHOW INDEX FROM sim_requests WHERE Key_name = 'sim_requests_phone_number_index'");
            if (empty($phoneIndexes) && Schema::hasColumn('sim_requests', 'phone_number')) {
                $table->index('phone_number');
            }
            
            $adminIndexes = DB::select("SHOW INDEX FROM sim_requests WHERE Key_name = 'sim_requests_admin_id_index'");
            if (empty($adminIndexes) && Schema::hasColumn('sim_requests', 'admin_id')) {
                $table->index('admin_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sim_requests', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropForeign(['admin_id']);
            $table->dropIndex(['plan_id']);
            $table->dropIndex(['phone_number']);
            $table->dropIndex(['admin_id']);
            
            $table->dropColumn([
                'plan_id',
                'limite_credit',
                'limite_data',
                'phone_number',
                'admin_comment',
                'admin_id',
                'admin_processed_at',
                'beneficiary_name',
                'beneficiary_first_name',
                'beneficiary_fonction',
                'beneficiary_matricule',
            ]);
        });
        
        Schema::table('sim_requests', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        Schema::table('sim_requests', function (Blueprint $table) {
            $table->enum('status', ['en_attente', 'validee', 'rejetee'])->default('en_attente');
        });
        
        Schema::table('sim_requests', function (Blueprint $table) {
            $table->dropColumn('request_type');
        });
        
        Schema::table('sim_requests', function (Blueprint $table) {
            $table->enum('request_type', ['attribution', 'suspension', 'reactivation', 'retour'])->default('attribution');
        });
    }
};
