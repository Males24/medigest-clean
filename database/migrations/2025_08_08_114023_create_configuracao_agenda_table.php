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
        Schema::create('configuracao_agenda', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('buffer_min')->default(10); // intervalo entre consultas
            $table->unsignedSmallInteger('passo_min')->default(30);  // passo de geração de slots
            $table->timestamps();
        });

        DB::table('configuracao_agenda')->insert([
            'buffer_min' => 10,
            'passo_min'  => 30,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracao_agenda');
    }
};
