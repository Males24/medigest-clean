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
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medico_id')->constrained()->onDelete('cascade');
            $table->date('data')->nullable(); // usado no modo diário
            $table->integer('dia_semana')->nullable(); // usado no modo semanal/quinzenal/mensal
            $table->integer('semana_mes')->nullable(); // usado no modo quinzenal/mensal
            $table->time('hora_inicio');
            $table->time('hora_fim')->nullable(); // opcional
            $table->boolean('disponivel')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
