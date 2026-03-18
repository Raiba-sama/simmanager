<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transmission_sheets', function (Blueprint $table) {
            $table->string('recipient_name')->nullable()->after('recipient_signature');
            $table->string('recipient_fonction')->nullable()->after('recipient_name');
        });
    }

    public function down(): void
    {
        Schema::table('transmission_sheets', function (Blueprint $table) {
            $table->dropColumn(['recipient_name', 'recipient_fonction']);
        });
    }
};
