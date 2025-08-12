<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Horario;
use App\Models\Medico;
use Carbon\Carbon;

class HorarioSeeder extends Seeder
{
    public function run(): void
    {
        $medicos = Medico::all();

        foreach ($medicos as $medico) {
            // Criar horários padrão de segunda a sexta
            for ($diaSemana = 1; $diaSemana <= 5; $diaSemana++) {

                // Criar turnos de manhã (9h às 12h, de 30 em 30 min)
                for ($hora = 9; $hora < 12; $hora++) {
                    Horario::firstOrCreate([
                        'medico_id' => $medico->id,
                        'dia_semana' => $diaSemana,
                        'hora_inicio' => Carbon::createFromTime($hora, 0)->format('H:i'),
                        'hora_fim' => Carbon::createFromTime($hora, 30)->format('H:i'),
                        'disponivel' => true
                    ]);
                }

                // Criar turnos da tarde (14h às 18h, de 30 em 30 min)
                for ($hora = 14; $hora < 18; $hora++) {
                    Horario::firstOrCreate([
                        'medico_id' => $medico->id,
                        'dia_semana' => $diaSemana,
                        'hora_inicio' => Carbon::createFromTime($hora, 0)->format('H:i'),
                        'hora_fim' => Carbon::createFromTime($hora, 30)->format('H:i'),
                        'disponivel' => true
                    ]);
                }
            }
        }
    }
}
