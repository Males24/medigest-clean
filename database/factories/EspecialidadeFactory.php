<?php

namespace Database\Factories;

use App\Models\Especialidade;
use Illuminate\Database\Eloquent\Factories\Factory;

class EspecialidadeFactory extends Factory
{
    protected $model = Especialidade::class;

    public function definition(): array
    {
        // Mesma lista usada no seeder (nome -> capa em /public)
        $map = [
            'Cardiologia'  => '/cardiologia.jpg',
            'Dermatologia' => '/dermatologia.jpg',
            'Pediatria'    => '/pediatria.jpg',
            'Neurologia'   => '/neurologia.jpg',
            'Ortopedia'    => '/ortopedia.jpg',
        ];

        // Escolhe um nome único da lista e obtém a capa correspondente
        $nome = $this->faker->unique()->randomElement(array_keys($map));
        $cover = $map[$nome];

        return [
            'nome'       => $nome,
            'cover_path' => $cover,
        ];
    }
}
