<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            EspecialidadeSeeder::class,
            MedicoSeeder::class,
            PacienteSeeder::class,
            ConsultaTipoSeeder::class,
            ConsultaSeeder::class,
            ParametrosAgendamentoSeeder::class,
            HorarioSeeder::class,
            ConfiguracaoHorarioSeeder::class,
        ]);
    }
}
