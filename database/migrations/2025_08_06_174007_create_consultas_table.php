<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('consultas', function (Blueprint $table) {
            $table->id();

            // Relações
            $table->foreignId('paciente_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('medico_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('especialidade_id')->constrained('especialidades')->cascadeOnDelete();

            // Tipo e duração (AGORA com tipo_slug a apontar a consulta_tipos.slug)
            $table->string('tipo_slug')->nullable();   // normal|prioritaria|urgente
            $table->integer('duracao')->default(30);

            // Data/hora
            $table->date('data');
            $table->time('hora');

            // Motivo e estado
            $table->string('motivo')->nullable();
            $table->string('estado')->default('agendada'); // agendada|pendente_medico|confirmada|concluida|cancelada_*|no_show

            $table->timestamps();

            // Índices e FKs
            $table->index('tipo_slug');
            $table->unique(['medico_id', 'data', 'hora'], 'uniq_medico_data_hora');
            $table->foreign('tipo_slug')->references('slug')->on('consulta_tipos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultas');
    }
};
