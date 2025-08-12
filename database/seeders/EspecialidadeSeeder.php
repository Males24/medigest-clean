<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\Especialidade;

class EspecialidadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $especialidades = [
            'Cardiologia',
            'Dermatologia',
            'Pediatria',
            'Neurologia',
            'Ortopedia'
        ];

        foreach ($especialidades as $nome) {
            Especialidade::create(['nome' => $nome]);
        }
    }
}
