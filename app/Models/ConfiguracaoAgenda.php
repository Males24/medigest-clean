<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracaoAgenda extends Model
{
    use HasFactory;

    // a tua tabela chama-se configuracao_agenda
    protected $table = 'configuracao_agenda';

    protected $fillable = [
        'tipo', // 'diario'|'semanal'|'quinzenal'|'mensal' (se usares)
        'horizonte_normal_dias',
        'horizonte_prioritaria_dias',
        'lead_normal_min',
        'lead_prioritaria_min',
        'buffer_min',
        'passo_min',
    ];

    protected $casts = [
        'horizonte_normal_dias'      => 'integer',
        'horizonte_prioritaria_dias' => 'integer',
        'lead_normal_min'            => 'integer',
        'lead_prioritaria_min'       => 'integer',
        'buffer_min'                 => 'integer',
        'passo_min'                  => 'integer',
    ];
}
