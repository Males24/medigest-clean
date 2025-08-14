<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::factory()->admin()->create([
            'name'     => 'Administrador',
            'email'    => 'admin@medigest.com',
            'password' => bcrypt('password'),
            
            'phone'    => '910000001',
            'settings' => [
                'theme' => 'system', 'language' => 'pt-PT',
                'notify_email' => true, 'notify_push' => false, 'weekly_digest' => true,
            ],
        ]);
    }
}
