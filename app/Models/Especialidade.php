<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Especialidade extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'cover_path',
    ];

    protected $appends = ['cover_url'];

    public function medicos()
    {
        return $this->belongsToMany(Medico::class, 'especialidade_medico')->withTimestamps();
    }

    public function getCoverUrlAttribute(): ?string
    {
        $path = $this->cover_path;
        if (!$path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return $path;
        }

        // ex: "especialidades/cardiologia.jpg" -> "/storage/especialidades/cardiologia.jpg"
        return asset('storage/' . ltrim($path, '/'));
    }
}
