<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use App\Models\Medico;
use App\Models\Consulta;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // KPIs do topo
        $totUsers      = Paciente::count(); // todos os utilizadores
        $totMedicos    = Medico::count();
        $totConsultas  = Consulta::whereIn('estado', ['agendada','confirmada','pendente_medico'])->count();
        $consultasHoje = Consulta::whereDate('data', now()->toDateString())->count();
        $consultasMes  = Consulta::whereYear('data', now()->year)
                                 ->whereMonth('data', now()->month)
                                 ->count();

        return view('admin.dashboard', compact(
            'totUsers','totMedicos','totConsultas','consultasHoje','consultasMes'
        ));
    }

    /**
     * Endpoint JSON para os gráficos do dashboard.
     * GET /admin/dashboard/charts?start=YYYY-MM-DD&end=YYYY-MM-DD
     */
    public function charts(Request $r)
    {
        // Por defeito: últimos 30 dias ATÉ +30 dias (inclui futuras)
        $start = Carbon::parse($r->get('start', now()->subDays(30)->toDateString()))->startOfDay();
        $end   = Carbon::parse($r->get('end',   now()->addDays(30)->toDateString()))->endOfDay();

        // 1) Consultas por dia (preenche dias sem registos com zero)
        $rawByDay = Consulta::select('data', DB::raw('COUNT(*) as total'))
            ->whereBetween('data', [$start->toDateString(), $end->toDateString()])
            ->groupBy('data')->orderBy('data')
            ->pluck('total','data');

        $labels = [];
        $seriesByDay = [];
        foreach (CarbonPeriod::create($start, $end) as $d) {
            $labels[] = $d->format('d/m');
            $seriesByDay[] = (int)($rawByDay[$d->toDateString()] ?? 0);
        }

        // 2) Distribuição por estado
        $byStatus = Consulta::select('estado', DB::raw('COUNT(*) as total'))
            ->whereBetween('data', [$start->toDateString(), $end->toDateString()])
            ->groupBy('estado')
            ->pluck('total','estado');

        // 3) Top especialidades no período
        $byEsp = Consulta::join('especialidades','consultas.especialidade_id','=','especialidades.id')
            ->whereBetween('consultas.data', [$start->toDateString(), $end->toDateString()])
            ->select('especialidades.nome as label', DB::raw('COUNT(*) as total'))
            ->groupBy('label')->orderBy('total','desc')
            ->limit(10)
            ->get();

        return response()->json([
            'range' => [$start->toDateString(), $end->toDateString()],
            'byDay' => ['labels'=>$labels, 'series'=>$seriesByDay],
            'status' => [
                'labels' => array_values(array_keys($byStatus->toArray())),
                'series' => array_values(array_map('intval', $byStatus->toArray())),
            ],
            'byEspecialidade' => [
                'labels' => $byEsp->pluck('label'),
                'series' => $byEsp->pluck('total')->map(fn($n)=>(int)$n),
            ],
        ]);
    }
}
