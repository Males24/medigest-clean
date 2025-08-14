<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Paciente;
use Illuminate\Support\Facades\Hash;

class PacienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Paciente 1
        $paciente1 = User::factory()->paciente()->create([
            'name' => 'Sofia Carvalho',
            'email' => 'sofia@medigest.com',
            'password' => Hash::make('password'),
            'phone'    => '910000004',
        ]);

        Paciente::firstOrCreate(
            ['user_id' => $paciente1->id],
            [
                'genero' => 'feminino',
                'nif' => '111111111',
                'telefone' => '910000004',
                'endereco' => 'Rua das Flores, 50, Coimbra',
                'data_nascimento' => '1990-07-10',
            ]
        );

        // Paciente 2
        $paciente2 = User::factory()->paciente()->create([
            'name' => 'Carlos Neves',
            'email' => 'carlos@medigest.com',
            'password' => Hash::make('password'),
            'phone'    => '910000005',
        ]);

        Paciente::firstOrCreate(
            ['user_id' => $paciente2->id],
            [
                'genero' => 'masculino',
                'nif' => '222222222',
                'telefone' => '910000005',
                'endereco' => 'Av. Liberdade, 101, Lisboa',
                'data_nascimento' => '2001-07-10',
            ]
        );
    }
}
