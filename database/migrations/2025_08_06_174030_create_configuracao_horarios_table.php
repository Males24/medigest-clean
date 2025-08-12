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
        Schema::create('configuracao_horarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('dia_semana'); // 0=domingo ... 6=sábado

            // Horários SEM segundos
            $table->time('manha_inicio')->nullable();
            $table->time('manha_fim')->nullable();
            $table->time('tarde_inicio')->nullable();
            $table->time('tarde_fim')->nullable();

            $table->boolean('ativo')->default(true); // O sistema permite ou não marcações neste dia
            $table->timestamps();
            $table->unique('dia_semana'); // Garante que só há uma configuração por dia
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracao_horarios');
    }
};
