<?php

namespace Database\Factories;

use App\Models\Medico;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medico>
 */
class MedicoFactory extends Factory
{
    protected $model = Medico::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->medico(), // usa state do UserFactory
            'crm' => strtoupper('CRM' . $this->faker->unique()->numerify('###')),
            'bio' => $this->faker->sentence(10),
        ];
    }
}
