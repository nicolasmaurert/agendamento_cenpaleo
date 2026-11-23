<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->enum('turno', ['manha', 'tarde', 'ambos'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('agendamentos')
            ->where('turno', 'ambos')
            ->update(['turno' => 'manha']);

        Schema::table('agendamentos', function (Blueprint $table) {
            $table->enum('turno', ['manha', 'tarde'])->change();
        });
    }
};
