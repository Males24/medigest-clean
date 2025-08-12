<?php

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConsultaRequest;
use App\Models\Consulta;
use App\Models\Medico;
use App\Services\ConsultaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ConsultaController extends Controller
{
    public function index()
    {
        $consultas = Consulta::where('paciente_id', Auth::id())
            ->with(['medico.user'])
            ->latest('data')
            ->paginate(15);

        return view('paciente.consultas.index', compact('consultas'));
    }

    public function create()
    {
        // Mostra nomes vindos de users relacionados ao médico
        $medicos = Medico::with('user')->get();
        return view('paciente.consultas.create', compact('medicos'));
    }

    // Endpoint para a view buscar slots disponíveis (AJAX)
    public function slots(Request $request, Medico $medico, ConsultaService $svc)
    {
        $request->validate([
            'data'    => ['required','date_format:Y-m-d'],
            'duracao' => ['nullable','integer','min:10','max:180'],
            'tipo'    => ['nullable','in:normal,prioritaria,urgente'],
        ]);

        $data    = $request->query('data');
        $duracao = $request->query('duracao'); // null -> serviço decide
        $tipo    = $request->query('tipo', 'normal');

        // médico no serviço = users.id
        $medicoUserId = (int) $medico->user_id;

        $slots = $svc->gerarSlotsDisponiveis($medicoUserId, $data, $duracao, $tipo);

        return response()->json(['data' => $slots]);
    }

    public function store(StoreConsultaRequest $request, ConsultaService $svc)
    {
        $dados = $request->validated();

        // mapear 'tipo' -> 'tipo_slug' se necessário
        if (!isset($dados['tipo_slug']) && isset($dados['tipo'])) {
            $dados['tipo_slug'] = $dados['tipo'];
            unset($dados['tipo']);
        }

        // paciente autenticado
        $dados['paciente_id'] = (int)auth()->id();

        // duração pelo tipo
        $tipo = \App\Models\ConsultaTipo::where('slug',$dados['tipo_slug'] ?? 'normal')->where('ativo',1)->firstOrFail();
        $dados['duracao'] = $tipo->duracao_min;

        $ok = $svc->verificarDisponibilidade(
            (int)$dados['medico_id'], // aqui deve já vir users.id do médico a partir do form
            $dados['data'],
            $dados['hora'],
            (int)$dados['duracao'],
            $dados['tipo_slug']
        );

        if (!$ok) {
            return back()->withInput()->withErrors(['hora' => 'Slot indisponível para este médico.']);
        }

        // paciente cria -> fica pendente até médico aceitar
        $dados['estado'] = 'pendente_medico';

        Consulta::create($dados);

        return redirect()->route('paciente.consultas.index')->with('success', 'Consulta agendada (aguarda confirmação do médico).');
    }

    public function cancelar(Consulta $consulta)
    {
        // Só o dono pode cancelar
        if ((int) $consulta->paciente_id !== (int) Auth::id()) {
            abort(403);
        }

        // Regra: não permite cancelar a < 24h do início
        $dataHora = Carbon::parse($consulta->data . ' ' . $consulta->hora);
        if ($dataHora->lt(now()->addDay())) {
            return back()->withErrors(['cancelar' => 'Não é possível cancelar com menos de 24h de antecedência.']);
        }

        $consulta->update(['estado' => 'cancelada_paciente']);

        return back()->with('success', 'Consulta cancelada.');
    }
}
