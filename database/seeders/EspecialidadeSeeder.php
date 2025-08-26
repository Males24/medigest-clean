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
        // Mapa nome -> capa (podes ajustar para o que tens em /public ou URLs externas)
        $seed = [
            'Cardiologia'  => '/cardiologia.jpg',         
            'Dermatologia' => '/dermatologia.jpg',
            'Pediatria'    => '/pediatria.jpg',
            'Neurologia'   => '/neurologia.jpg',                        
            'Ortopedia'    => '/ortopedia.jpg',
        ];

        foreach ($seed as $nome => $cover) {
            \App\Models\Especialidade::firstOrCreate(
                ['nome' => $nome],
                ['cover_path' => $cover]
            );
        }
    }
}
