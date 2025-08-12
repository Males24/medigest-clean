<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ConfiguracaoHorario;

class ConfiguracaoHorarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 0=Dom, 1=Seg, ... 6=Sáb
        $defaults = [
            0 => [null, null, null, null, false],           // Domingo
            1 => ['09:00','12:00','14:00','18:00', true],   // Seg
            2 => ['09:00','12:00','14:00','18:00', true],
            3 => ['09:00','12:00','14:00','18:00', true],
            4 => ['09:00','12:00','14:00','18:00', true],
            5 => ['09:00','12:00','14:00','18:00', true],   // Sex
            6 => [null, null, null, null, false],           // Sáb
        ];

        foreach ($defaults as $dia => [$mi,$mf,$ti,$tf,$ativo]) {
            ConfiguracaoHorario::updateOrCreate(
                ['dia_semana' => $dia],
                [
                    'manha_inicio' => $mi,
                    'manha_fim'    => $mf,
                    'tarde_inicio' => $ti,
                    'tarde_fim'    => $tf,
                    'ativo'        => $ativo,
                ]
            );
        }
    }
}
