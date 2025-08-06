<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MedicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Médico User',
            'email' => 'medico@medigest.com',
            'password' => Hash::make('password'),
            'role' => 'medico',
        ]);
    }
}
