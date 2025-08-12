<?php

namespace Database\Factories;

use App\Models\ConfiguracaoHorario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConfiguracaoHorario>
 */
class ConfiguracaoHorarioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dia_semana' => $this->faker->numberBetween(0, 6),
            'manha_inicio' => '09:00',
            'manha_fim' => '12:00',
            'tarde_inicio' => '14:00',
            'tarde_fim' => '18:00',
            'ativo' => true,
        ];
    }
}
