<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultaTipo extends Model
{
    protected $table = 'consulta_tipos';
    protected $fillable = ['slug','nome','horizonte_horas','lead_minutos','duracao_min','ativo'];
}
