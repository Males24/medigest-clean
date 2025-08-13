<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parametros_agendamento', function (Blueprint $table) {
            $table->id();

            // Parâmetros globais do motor de agendamento
            $table->unsignedSmallInteger('buffer_min')->default(0); // intervalo / almofada (minutos)
            $table->unsignedSmallInteger('passo_min')->default(30);  // passo para gerar slots (minutos)

            $table->timestamps();
        });

        // registo inicial (singleton)
        DB::table('parametros_agendamento')->insert([
            'buffer_min' => 0,
            'passo_min'  => 30,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('parametros_agendamento');
    }
};
