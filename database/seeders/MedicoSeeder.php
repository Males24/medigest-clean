<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medico;
use App\Models\User;
use App\Models\Especialidade;
use Illuminate\Support\Facades\Hash;

class MedicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $especialidades = Especialidade::pluck('id')->toArray();

        // Primeiro médico
        $medico1 = User::factory()->medico()->create([
            'name' => 'Ricardo Martins',
            'email' => 'ricardo@medigest.com',
            'password' => Hash::make('password'),
        ]);

        $m1 = Medico::firstOrCreate(
            ['user_id' => $medico1->id],
            ['crm' => 'CRM123', 'bio' => 'Cardiologista com experiência.']
        );

        $m1->especialidades()->sync(array_rand(array_flip($especialidades), 2));

        // Segundo médico
        $medico2 = User::factory()->medico()->create([
            'name' => 'Marta Ferreira',
            'email' => 'marta@medigest.com',
            'password' => Hash::make('password'),
        ]);

        $m2 = Medico::firstOrCreate(
            ['user_id' => $medico2->id],
            ['crm' => 'CRM456', 'bio' => 'Dermatologista com foco em cuidados estéticos.']
        );

        $m2->especialidades()->sync(array_rand(array_flip($especialidades), 2));
    }
}
