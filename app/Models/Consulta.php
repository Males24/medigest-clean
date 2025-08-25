<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Consulta extends Model
{
    use HasFactory;

    protected $fillable = [
        'paciente_id',
        'medico_id',
        'especialidade_id',
        'data',        // Y-m-d
        'hora',        // H:i:s
        'duracao',     // minutos
        'tipo_slug',   // normal|prioritaria|urgente
        'motivo',
        'estado',      // agendada|confirmada|pendente|pendente_medico|cancelada_*|concluida
    ];

    protected $casts = [
        'data'    => 'date',
        'hora'    => 'string',
        'duracao' => 'integer',
    ];

    // Relações
    public function paciente() { return $this->belongsTo(User::class, 'paciente_id'); }
    public function medico()   { return $this->belongsTo(User::class, 'medico_id'); }
    public function especialidade() { return $this->belongsTo(\App\Models\Especialidade::class, 'especialidade_id'); }
    public function tipo() { return $this->belongsTo(\App\Models\ConsultaTipo::class, 'tipo_slug', 'slug'); }

    /** Consultas que contam como "ativas" (exclui canceladas e concluídas) */
    public function scopeAtivas(Builder $q): Builder
    {
        return $q->whereNotIn('estado', ['cancelada','cancelado','cancelada_paciente','cancelada_medico','concluida','concluído']);
    }

    /** Próximas (>= agora), comparando data e hora de forma segura */
    public function scopeFuturas(Builder $q, ?Carbon $ref = null): Builder
    {
        $ref = $ref ?: now();
        return $q->where(function($w) use ($ref) {
            $w->whereDate('data', '>', $ref->toDateString())
              ->orWhere(function($z) use ($ref) {
                  $z->whereDate('data', '=', $ref->toDateString())
                    ->whereTime('hora', '>=', $ref->format('H:i:s'));
              });
        });
    }

    /** Já realizadas (< agora) */
    public function scopePassadas(Builder $q, ?Carbon $ref = null): Builder
    {
        $ref = $ref ?: now();
        return $q->where(function($w) use ($ref) {
            $w->whereDate('data', '<', $ref->toDateString())
              ->orWhere(function($z) use ($ref) {
                  $z->whereDate('data', '=', $ref->toDateString())
                    ->whereTime('hora', '<', $ref->format('H:i:s'));
              });
        });
    }

    // Verificação de conflito (mantido)
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
