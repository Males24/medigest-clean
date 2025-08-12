<?php

namespace Database\Factories;

use App\Models\Consulta;
use App\Models\User;
use App\Models\Medico;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Consulta>
 */
class ConsultaFactory extends Factory
{
    protected $model = Consulta::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Médico (User) existente ou cria um novo com role 'medico'
        $medicoUser = User::where('role', 'medico')->inRandomOrder()->first()
            ?? User::factory()->medico()->create();

        // Perfil Medico
        $medicoPerfil = Medico::firstOrCreate(
            ['user_id' => $medicoUser->id],
            ['crm' => 'CRM'.mt_rand(100,999), 'bio' => '—']
        );

        // Especialidade do médico (se não tiver, cria uma ligação rápida)
        $espId = $medicoPerfil->especialidades()->inRandomOrder()->value('especialidades.id');
        if (!$espId) {
            $esp = \App\Models\Especialidade::inRandomOrder()->first()
                ?? \App\Models\Especialidade::create(['nome' => 'Geral']);
            $medicoPerfil->especialidades()->syncWithoutDetaching([$esp->id]);
            $espId = $esp->id;
        }

        // Paciente (User) existente ou cria
        $pacienteUser = User::where('role', 'paciente')->inRandomOrder()->first()
            ?? User::factory()->paciente()->create();

        return [
            'paciente_id'      => $pacienteUser->id,
            'medico_id'        => $medicoUser->id,       // users.id
            'especialidade_id' => $espId,
            'tipo_slug'        => 'normal',              // <— trocado de 'tipo' para 'tipo_slug'
            'data'             => Carbon::parse($this->faker->dateTimeBetween('+1 day', '+1 week'))->format('Y-m-d'),
            'hora'             => $this->faker->time('H:i'),
            'duracao'          => 30,
            'motivo'           => $this->faker->sentence(),
            'estado'           => 'agendada',
        ];
    }
}
