<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    use HasFactory;

    protected $fillable = [
        'paciente_id',        // -> users.id
        'medico_id',          // -> users.id
        'especialidade_id',   // FK -> especialidades.id
        'data',               // Y-m-d
        'hora',               // H:i
        'duracao',            // minutos
        'tipo_slug',          // normal|prioritaria|Urgente 
        'motivo',             // opcional
        'estado',             // agendada|confirmada|cancelada_paciente|cancelada_medico|concluida

    ];

    protected $casts = [
        'data'    => 'date',
        'hora'    => 'string',
        'duracao' => 'integer',
    ];

    // Relações principais (users)
    public function paciente() { return $this->belongsTo(User::class, 'paciente_id'); }
    public function medico()   { return $this->belongsTo(User::class, 'medico_id'); }

    // Especialidade
    public function especialidade()
    {
        return $this->belongsTo(\App\Models\Especialidade::class, 'especialidade_id');
    }

    // Tipo
    public function tipo()
    {
        return $this->belongsTo(\App\Models\ConsultaTipo::class, 'tipo_slug', 'slug');
    }

    // Scope utilitário para "não tem conflito" (mantém a ideia, mas estados mais amplos)
    public function scopeSemConflito($query, int $medicoId, string $data, string $horaInicio, int $duracaoMin)
    {
        return $query->where('medico_id', $medicoId)
            ->where('data', $data)
            ->whereIn('estado', ['agendada','confirmada','pendente_medico'])
            ->where(function ($q) use ($horaInicio, $duracaoMin) {
                $q->whereBetween('hora', [$horaInicio, $horaInicio])
                  ->orWhereRaw('? BETWEEN hora AND ADDTIME(hora, SEC_TO_TIME(duracao * 60))', [$horaInicio])
                  ->orWhereRaw(
                      'ADDTIME(?, SEC_TO_TIME(? * 60)) BETWEEN hora AND ADDTIME(hora, SEC_TO_TIME(duracao * 60))',
                      [$horaInicio, $duracaoMin]
                  );
            })
            ->doesntExist();
    }
}
