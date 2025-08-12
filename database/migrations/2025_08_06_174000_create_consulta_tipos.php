<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consulta_tipos', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();     // normal, prioritaria, urgente
            $t->string('nome');
            $t->integer('horizonte_horas')->default(365 * 24); // máximo de datas futuras (1 ano por padrão)
            $t->integer('lead_minutos')->default(0); // antecedência mínima
            $t->integer('duracao_min')->default(30);
            $t->boolean('ativo')->default(true);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consulta_tipos');
    }
};
