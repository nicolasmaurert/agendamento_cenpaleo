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
        Schema::create('bloqueios', function (Blueprint $table) {
            $table->id();
            $table->date('data');
            $table->enum('turno', ['manha', 'tarde', 'ambos']);
            $table->string('motivo')->nullable();
            $table->timestamps();

            // Índices para pesquisa rápida de bloqueio por data e turno
            $table->index(['data', 'turno']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bloqueios');
    }
};
