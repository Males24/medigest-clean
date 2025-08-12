<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracaoHorario extends Model
{
    use HasFactory;

    protected $table = 'configuracao_horarios';

    protected $fillable = [
        'dia_semana',
        'manha_inicio',
        'manha_fim',
        'tarde_inicio',
        'tarde_fim',
        'ativo',
    ];

    protected $casts = [
        'manha_inicio' => 'string',
        'manha_fim'    => 'string',
        'tarde_inicio' => 'string',
        'tarde_fim'    => 'string',
        'ativo'        => 'boolean',
    ];

    public static function diasSemana(): array
    {
        return [
            0 => 'Domingo',
            1 => 'Segunda-feira',
            2 => 'Terça-feira',
            3 => 'Quarta-feira',
            4 => 'Quinta-feira',
            5 => 'Sexta-feira',
            6 => 'Sábado',
        ];
    }

    public function getNomeDiaAttribute(): string
    {
        $dias = self::diasSemana();
        return $dias[$this->dia_semana] ?? '';
    }
}
