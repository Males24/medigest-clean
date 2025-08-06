<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PacienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Paciente User',
            'email' => 'paciente@medigest.com',
            'password' => Hash::make('password'),
            'role' => 'paciente',
        ]);
    }
}
