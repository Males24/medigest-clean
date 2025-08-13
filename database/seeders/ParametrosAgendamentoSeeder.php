<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ParametrosAgendamento;

class ParametrosAgendamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define o espaço de tempo pos consulta (pode ser alterado no painel admin)
        ParametrosAgendamento::firstOrCreate([], [
            'buffer_min' => 0,
            'passo_min'  => 30,
        ]);
    }
}
