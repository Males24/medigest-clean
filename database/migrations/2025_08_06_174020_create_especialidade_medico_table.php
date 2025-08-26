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
        Schema::create('especialidade_medico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medico_id')->constrained()->onDelete('cascade');
            $table->foreignId('especialidade_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            // evita duplicados do mesmo par
            $table->unique(['medico_id', 'especialidade_id'], 'especialidade_medico_unique');
        });

        // Adiciona a coluna de capa na tabela de especialidades
        Schema::table('especialidades', function (Blueprint $table) {
            if (!Schema::hasColumn('especialidades', 'cover_path')) {
                $table->string('cover_path')->nullable()->after('nome');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove a coluna de capa
        Schema::table('especialidades', function (Blueprint $table) {
            if (Schema::hasColumn('especialidades', 'cover_path')) {
                $table->dropColumn('cover_path');
            }
        });

        // Elimina a pivô
        Schema::dropIfExists('especialidade_medico');
    }
};
