<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Dashboard do médico.
 *
 * - index(): métricas rápidas e listas (hoje / próximas)
 *   com o nome do tipo de consulta.
 */
class DashboardController extends Controller
{
    /**
     * Resumo do médico: contadores + listas de hoje e próximas.
     */
    public function index()
    {
        $medicoId = (int) Auth::id();

        $stats = [
            'hoje' => Consulta::where('medico_id', $medicoId)
                ->whereDate('data', Carbon::today()->toDateString())
                ->count(),

            'semana' => Consulta::where('medico_id', $medicoId)
                ->whereBetween('data', [Carbon::today()->startOfWeek()->toDateString(), Carbon::today()->endOfWeek()->toDateString()])
                ->count(),

            // ✅ inclui todos os estados de cancelamento
            'canceladas_mes' => Consulta::where('medico_id', $medicoId)
                ->whereMonth('data', Carbon::today()->month)
                ->whereYear('data', Carbon::today()->year)
                ->whereIn('estado', ['cancelada', 'cancelada_paciente', 'cancelada_medico'])
                ->count(),

            'pacientes_mes' => Consulta::where('medico_id', $medicoId)
                ->whereMonth('data', Carbon::today()->month)
                ->whereYear('data', Carbon::today()->year)
                ->distinct('paciente_id')
                ->count('paciente_id'),
        ];

        // Carrega também a relação 'tipo' para evitar N+1
        $consultasHoje = Consulta::with(['paciente','tipo'])
            ->where('medico_id', $medicoId)
            ->whereDate('data', Carbon::today()->toDateString())
            ->orderBy('hora')
            ->get()
            ->map(function ($c) {
                $dt = $c->data instanceof Carbon ? $c->data->copy() : Carbon::parse($c->data);
                if (!empty($c->hora)) {
                    try { $dt->setTimeFromTimeString($c->hora); } catch (\Throwable $e) {}
                }
                $c->inicio = $dt;
                return $c;
            });

        $proximas = Consulta::with(['paciente','tipo'])
            ->where('medico_id', $medicoId)
            ->whereDate('data', '>=', Carbon::today()->toDateString())
            ->orderBy('data')->orderBy('hora')
            ->limit(10)
            ->get()
            ->map(function ($c) {
                $dt = $c->data instanceof Carbon ? $c->data->copy() : Carbon::parse($c->data);
                if (!empty($c->hora)) {
                    try { $dt->setTimeFromTimeString($c->hora); } catch (\Throwable $e) {}
                }
                $c->inicio = $dt;
                return $c;
            });

        return view('medico.dashboard', compact('stats', 'consultasHoje', 'proximas'));
    }
}
