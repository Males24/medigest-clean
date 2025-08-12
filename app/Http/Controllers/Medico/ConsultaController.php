<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConsultaRequest;
use App\Models\Consulta;
use App\Services\ConsultaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ConsultaController extends Controller
{
    public function index()
    {
        $consultas = Consulta::where('medico_id', Auth::id())
            ->with(['paciente'])
            ->latest('data')
            ->paginate(15);

        return view('medico.consultas.index', compact('consultas'));
    }

    public function create()
    {
        $medicoUserId = auth()->id();
        $pacientes = \App\Models\User::where('role', 'paciente')
            ->orderBy('name')
            ->get(['id','name','email']);

        return view('medico.consultas.create', compact('medicoUserId', 'pacientes'));
    }

    // Slots (AJAX) para o médico autenticado
    public function slots(Request $request, ConsultaService $svc)
    {
        try {
            $request->validate([
                'data'    => ['required','date_format:Y-m-d'],
                'duracao' => ['nullable','integer','min:10','max:180'],
                'tipo'    => ['nullable','in:normal,prioritaria,urgente'],
            ]);

            $data    = $request->query('data');
            $duracao = $request->query('duracao'); // null -> serviço assume duração do tipo
            $tipo    = $request->query('tipo', 'normal');

            $slots = $svc->gerarSlotsDisponiveis((int)auth()->id(), $data, $duracao, $tipo);

            return response()->json(['data' => $slots]);
        } catch (\Throwable $e) {
            \Log::error('Erro ao gerar slots (medico)', ['msg' => $e->getMessage()]);
            return response()->json(['data' => []], 200);
        }
    }

    public function store(StoreConsultaRequest $request, ConsultaService $svc)
    {
        $dados = $request->validated();

        // mapear 'tipo' -> 'tipo_slug' se vier do form antigo
        if (!isset($dados['tipo_slug']) && isset($dados['tipo'])) {
            $dados['tipo_slug'] = $dados['tipo'];
            unset($dados['tipo']);
        }

        $dados['medico_id'] = (int)auth()->id();
        $dados['paciente_id'] = (int)($dados['paciente_id'] ?? 0);

        // duração pelo tipo
        $tipo = \App\Models\ConsultaTipo::where('slug',$dados['tipo_slug'] ?? 'normal')->where('ativo',1)->firstOrFail();
        $dados['duracao'] = $tipo->duracao_min;

        $ok = $svc->verificarDisponibilidade(
            $dados['medico_id'],
            $dados['data'],
            $dados['hora'],
            (int)$dados['duracao'],
            $dados['tipo_slug']
        );
        if (!$ok) {
            return back()->withInput()->withErrors(['hora' => 'Slot indisponível.']);
        }

        // médico cria -> fica agendada até paciente confirmar (teu fluxo)
        $dados['estado'] = 'agendada';

        Consulta::create($dados);

        return redirect()->route('medico.consultas.index')->with('success', 'Consulta agendada!');
    }

    public function cancelar(Consulta $consulta)
    {
        if ((int)$consulta->medico_id !== (int)Auth::id()) abort(403);

        $dataHora = Carbon::parse($consulta->data.' '.$consulta->hora);
        if ($dataHora->lt(now()->addDay())) {
            return back()->withErrors(['cancelar' => 'Não é possível cancelar com menos de 24h.']);
        }

        $consulta->update(['estado' => 'cancelada_medico']);
        return back()->with('success', 'Consulta cancelada.');
    }
}
