<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Calendário do médico com eventos (consultas).
 */
class CalendarioController extends Controller
{
    public function index()
    {
        $medicoId = (int) Auth::id();

        $start = Carbon::today()->startOfMonth()->subWeeks(2)->startOfDay();
        $end   = Carbon::today()->endOfMonth()->addWeeks(4)->endOfDay();

        $consultas = Consulta::with(['paciente:id,name', 'tipo'])
            ->where('medico_id', $medicoId)
            ->whereBetween('data', [$start->toDateString(), $end->toDateString()])
            ->orderBy('data')
            ->orderBy('hora')
            ->get();

        $events = $consultas->map(function ($c) {
            $data = $c->data ? Carbon::parse($c->data->format('Y-m-d')) : null;
            $hora = $c->hora ?: null;

            $startDT = $data
                ? ($hora ? Carbon::parse($c->data->format('Y-m-d') . ' ' . $hora) : $data->copy()->startOfDay())
                : null;

            $minDur = (int) ($c->duracao ?? ($c->tipo->duracao_min ?? 30));
            $endDT  = $startDT && $hora ? $startDT->copy()->addMinutes($minDur) : null;

            $tipoSlug = $c->tipo_slug ?? 'normal';
            $tipoNome = $c->tipo->nome ?? ucfirst($tipoSlug);

            return [
                'id'     => $c->id,
                'title'  => ($c->paciente->name ?? 'Paciente') . ($tipoSlug ? ' • ' . $tipoNome : ''),
                'start'  => $startDT ? $startDT->toIso8601String() : null,
                'end'    => $endDT ? $endDT->toIso8601String() : null,
                'allDay' => $hora ? false : true,
                'tipo'   => $tipoSlug,
                'estado' => $c->estado ?? '',
            ];
        })->values();

        return view('medico.calendario', ['events' => $events]);
    }
}
