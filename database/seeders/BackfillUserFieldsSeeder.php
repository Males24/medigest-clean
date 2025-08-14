<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class BackfillUserFieldsSeeder extends Seeder
{
    public function run(): void
    {
        $defaultSettings = [
            'theme'         => 'system',
            'language'      => 'pt-PT',
            'notify_email'  => true,
            'notify_push'   => false,
            'weekly_digest' => true,
        ];

        User::chunk(200, function ($users) use ($defaultSettings) {
            foreach ($users as $u) {
                $dirty = false;

                // phone
                if (empty($u->phone)) {
                    // se for paciente e tiver ficha com telefone, usa-o
                    if ($u->role === 'paciente' && $u->relationLoaded('paciente') ? $u->paciente : $u->load('paciente')->paciente) {
                        $u->phone = $u->paciente->telefone ?: null;
                    }
                    if (empty($u->phone)) {
                        // fallback: gera um "91########"
                        $u->phone = '9' . random_int(100000000, 999999999);
                    }
                    $dirty = true;
                }

                // settings
                if (empty($u->settings)) {
                    $u->settings = $defaultSettings;
                    $dirty = true;
                }

                // avatar_path mantém-se NULL (usamos ui-avatars no layout)

                if ($dirty) $u->save();
            }
        });
    }
}
