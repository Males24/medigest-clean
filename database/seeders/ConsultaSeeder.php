<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Consulta;
use App\Models\User;
use App\Models\Medico;
use Illuminate\Support\Carbon;

class ConsultaSeeder extends Seeder
{
    public function run(): void
    {
        // Médico (User) já criado no MedicoSeeder
        $medicoUser = User::where('role', 'medico')->inRandomOrder()->first();
        if (!$medicoUser) {
            $this->command->warn('Sem médicos para criar consultas.');
            return;
        }

        // Perfil Medico + uma especialidade válida desse médico
        $medicoPerfil = Medico::where('user_id', $medicoUser->id)->first();
        if (!$medicoPerfil) {
            $this->command->warn('Sem perfil médico associado ao user '.$medicoUser->id);
            return;
        }

        $especialidadeId = $medicoPerfil->especialidades()->inRandomOrder()->value('especialidades.id');
        if (!$especialidadeId) {
            $this->command->warn('Médico '.$medicoUser->name.' sem especialidades — não criei consulta.');
            return;
        }

        // Um paciente (User) válido
        $pacienteUser = User::where('role', 'paciente')->inRandomOrder()->first();
        if (!$pacienteUser) {
            $this->command->warn('Sem pacientes para criar consultas.');
            return;
        }

        // Cria uma consulta exemplo
        Consulta::create([
            'paciente_id'      => $pacienteUser->id,
            'medico_id'        => $medicoUser->id, // guarda users.id
            'especialidade_id' => $especialidadeId,
            'tipo_slug'        => 'normal',              // <— trocado de 'tipo' para 'tipo_slug'
            'data'             => Carbon::tomorrow()->format('Y-m-d'),
            'hora'             => '09:00',
            'duracao'          => 30,
            'motivo'           => 'Consulta de rotina',
            'estado'           => 'agendada',
        ]);
    }
}