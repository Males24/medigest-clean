<?php

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConsultaRequest;
use App\Models\Consulta;
use App\Models\Medico;
use App\Models\Especialidade;
use App\Services\ConsultaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ConsultaController extends Controller
{
    /** Landing “Consulta externa”. */
    public function index()
    {
        return view('paciente.consultas.index');
    }

    /** Wizard de marcação (carrega especialidades). */
    public function create(Request $request)
    {
        $especialidades = Especialidade::orderBy('nome')->get(['id','nome']);
        return view('paciente.consultas.create', compact('especialidades'));
    }

    /** POST: guarda marcação (estado: pendente_medico). */
    public function store(StoreConsultaRequest $request, ConsultaService $svc)
    {
        $dados = $request->validated();
        $dados['paciente_id'] = (int) auth()->id(); // paciente é o próprio

        // médico tem de possuir a especialidade
        $medicoPerfil = Medico::where('user_id', $dados['medico_id'])->first();
        if (!$medicoPerfil) {
            return back()->withInput()->withErrors(['medico_id' => 'Médico inválido.']);
        }
        $temEspecialidade = $medicoPerfil->especialidades()
            ->where('especialidades.id', $dados['especialidade_id'])
            ->exists();
        if (!$temEspecialidade) {
            return back()->withInput()->withErrors(['medico_id' => 'O médico não possui a especialidade escolhida.']);
        }

        $tipo = \App\Models\ConsultaTipo::where('slug', $dados['tipo_slug'])->where('ativo', 1)->firstOrFail();
        $duracao = (int) $tipo->duracao_min;

        $ok = $svc->verificarDisponibilidade(
            (int)$dados['medico_id'], $dados['data'], $dados['hora'], $duracao, $dados['tipo_slug']
        );
        if (!$ok) {
            return back()->withInput()->withErrors(['hora' => 'Slot indisponível para este médico.']);
        }

        Consulta::create([
            'paciente_id'      => (int)$dados['paciente_id'],
            'medico_id'        => (int)$dados['medico_id'],
            'especialidade_id' => (int)$dados['especialidade_id'],
            'tipo_slug'        => $dados['tipo_slug'],
            'data'             => $dados['data'],
            'hora'             => $dados['hora'],
            'duracao'          => $duracao,
            'motivo'           => $dados['descricao'] ?? null,
            'estado'           => 'pendente_medico',
        ]);

        return redirect()->route('paciente.home')
            ->with('success', 'Consulta agendada (aguarda confirmação do médico).');
    }

    /** POST: cancela (regra: > 24h). */
    public function cancelar(Consulta $consulta)
    {
        if ((int) $consulta->paciente_id !== (int) Auth::id()) abort(403);

        // Combina data + hora sem "double time"
        $dataHora = $consulta->data instanceof Carbon
            ? $consulta->data->copy()
            : Carbon::parse($consulta->data);

        if (!empty($consulta->hora)) {
            try { $dataHora->setTimeFromTimeString($consulta->hora); } catch (\Throwable $e) {}
        }

        if ($dataHora->lt(now()->addDay())) {
            return back()->withErrors(['cancelar' => 'Não é possível cancelar com menos de 24h de antecedência.']);
        }

        $consulta->update(['estado' => 'cancelada_paciente']);

        return back()->with('success', 'Consulta cancelada.');
    }

    /** Lista com tabs: futuras | passadas | todas */
    public function todas(\Illuminate\Http\Request $request)
{
    $pacienteId = (int) \Illuminate\Support\Facades\Auth::id();
    $tab = $request->query('tab', 'futuras');

    $nowDate = \Carbon\Carbon::today()->toDateString();
    $nowTime = \Carbon\Carbon::now()->format('H:i');

    $q = \App\Models\Consulta::with(['medico','especialidade','tipo'])
        ->where('paciente_id', $pacienteId);

    if ($tab === 'futuras') {
        $q->where(function($w) use ($nowDate,$nowTime){
              $w->where('data', '>', $nowDate)
                ->orWhere(function($i) use ($nowDate,$nowTime){
                    $i->where('data', $nowDate)->where('hora', '>=', $nowTime);
                });
          })
          ->whereNotIn('estado', ['cancelada','cancelada_paciente','cancelada_medico','concluida'])
          ->orderBy('data')->orderBy('hora');
    } elseif ($tab === 'passadas') {
        $q->where(function($w) use ($nowDate,$nowTime){
              $w->where('data', '<', $nowDate)
                ->orWhere(function($i) use ($nowDate,$nowTime){
                    $i->where('data', $nowDate)->where('hora', '<', $nowTime);
                });
          })
          ->orderByDesc('data')->orderByDesc('hora');
    } else {
        $q->orderByDesc('data')->orderByDesc('hora');
    }

    $consultas = $q->paginate(15)->withQueryString();

    return view('paciente.consultas.todas', compact('consultas','tab'));
}
}
