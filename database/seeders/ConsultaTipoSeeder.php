<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConsultaTipoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $rows = [
            [
                'slug'            => 'normal',
                'nome'            => 'Normal',
                'horizonte_horas' => 365 * 24,       // grande janela máxima
                'lead_minutos'    => 60 * 24 * 60,   // só a partir de +60 dias
                'duracao_min'     => 30,
                'ativo'           => 1,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'slug'            => 'prioritaria',
                'nome'            => 'Prioritária',
                'horizonte_horas' => 365 * 24,
                'lead_minutos'    => 30 * 24 * 60,   // só a partir de +30 dias
                'duracao_min'     => 30,
                'ativo'           => 1,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'slug'            => 'urgente',
                'nome'            => 'Urgente',
                'horizonte_horas' => 365 * 24,
                'lead_minutos'    => 72 * 60,        // só a partir de +72 horas
                'duracao_min'     => 30,
                'ativo'           => 1,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
        ];

        // upsert por 'slug' (atualiza se já existir)
        DB::table('consulta_tipos')->upsert(
            $rows,
            ['slug'], // chave única
            ['nome','horizonte_horas','lead_minutos','duracao_min','ativo','updated_at'] // colunas a atualizar
        );
    }
}
