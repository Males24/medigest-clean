<?php

namespace Database\Factories;

use App\Models\Paciente;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Paciente>
 */
class PacienteFactory extends Factory
{
    protected $model = Paciente::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->paciente(), // usa state do UserFactory
            'nif' => $this->faker->numerify('#########'),
            'telefone' => $this->faker->numerify('91########'),
            'endereco' => $this->faker->address(),
            'data_nascimento' => $this->faker->date('Y-m-d', '-18 years'),
            'genero' => $this->faker->randomElement(['masculino', 'feminino']),
        ];
    }
}
