<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParametrosAgendamento extends Model
{
    use HasFactory;

    protected $table = 'parametros_agendamento';

    protected $fillable = ['buffer_min', 'passo_min'];

    protected $casts = [
        'buffer_min' => 'integer',
        'passo_min'  => 'integer',
    ];
}
