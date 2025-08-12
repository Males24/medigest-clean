<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConfiguracaoAgenda;

class ConfiguracaoAgendaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define o tipo padrão como semanal (pode ser alterado no painel admin)
        ConfiguracaoAgenda::firstOrCreate(
            ['id' => 1],
            ['tipo' => 'semanal']
        );
    }
}
