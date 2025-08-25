<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConsultaRequest;
use App\Models\Consulta;
use App\Models\User;
use App\Models\Especialidade;
use App\Models\Medico;
use App\Services\ConsultaService;
use App\Notifications\ConsultaConfirmada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

/**
 * Gestão de consultas pelo próprio médico.
 */
class ConsultaController extends Controller
{
    /** Lista do médico autenticado (com filtros simples). */
    public function index(Request $request)
    {
        $q = Consulta::where('medico_id', Auth::id())
            ->with(['paciente', 'especialidade']);

        if ($request->filled('q')) {
            $term = trim($request->input('q'));
            $q->where(function ($w) use ($term) {
                $w->whereHas('paciente', function ($s) use ($term) {
                    $s->where('name', 'like', "%{$term}%")
                      ->orWhere('email', 'like', "%{$term}%");
                })->orWhereHas('especialidade', function ($s) use ($term) {
                    $s->where('nome', 'like', "%{$term}%");
                })->orWhere('motivo', 'like', "%{$term}%")
                  ->orWhere('estado', 'like', "%{$term}%");
            });
        }

        if ($request->filled('data_de'))  $q->whereDate('data', '>=', $request->input('data_de'));
        if ($request->filled('data_ate')) $q->whereDate('data', '<=', $request->input('data_ate'));

        $consultas = $q->orderBy('data', 'desc')->orderBy('hora', 'desc')->paginate(12)->withQueryString();

        return view('medico.consultas.index', compact('consultas'));
    }

    /** Form para criar uma consulta (médico só pode criar dele próprio). */
    public function create()
    {
        $pacientes = User::where('role', 'paciente')->orderBy('name')->get(['id','name','email']);

        $perfil = Medico::where('user_id', auth()->id())->first();

        $especialidades = $perfil
            ? $perfil->especialidades()->select('especialidades.id', 'especialidades.nome')->orderBy('especialidades.nome')->get()
            : Especialidade::orderBy('nome')->get(['id','nome']);

        $medicos = collect([auth()->user()]);

        return view('medico.consultas.create', compact('pacientes','especialidades','medicos'));
    }

    /** Slots (AJAX) para o médico autenticado. */
    public function slots(Request $request, ConsultaService $svc)
    {
        try {
            $request->validate([
                'data'    => ['required','date_format:Y-m-d'],
                'duracao' => ['nullable','integer','min:10','max:180'],
                'tipo'    => ['nullable', Rule::in(['normal','prioritaria','urgente'])],
            ]);

            $data    = $request->query('data');
            $duracao = $request->query('duracao');
            $tipo    = $request->query('tipo', 'normal');

            $slots = $svc->gerarSlotsDisponiveis((int)auth()->id(), $data, $duracao, $tipo);

            return response()->json(['data' => $slots]);
        } catch (\Throwable $e) {
            \Log::error('Erro ao gerar slots (medico)', ['msg' => $e->getMessage()]);
            return response()->json(['data' => []], 200);
        }
    }

    /** POST: cria consulta do médico autenticado. */
    public function store(StoreConsultaRequest $request, ConsultaService $svc)
    {
        $dados = $request->validated();

        // força o médico a ser o autenticado
        $dados['medico_id'] = (int) auth()->id();

        $perfil = Medico::where('user_id', $dados['medico_id'])->first();
        if (!$perfil) {
            return back()->withInput()->withErrors(['medico_id' => 'Perfil do médico não encontrado.']);
        }
        $temEsp = $perfil->especialidades()->where('especialidades.id', $dados['especialidade_id'])->exists();
        if (!$temEsp) {
            return back()->withInput()->withErrors(['especialidade_id' => 'Esta especialidade não pertence ao médico.']);
        }

        $tipo = \App\Models\ConsultaTipo::where('slug', $dados['tipo_slug'])->where('ativo', 1)->firstOrFail();
        $duracao = (int) $tipo->duracao_min;

        $ok = $svc->verificarDisponibilidade(
            (int)$dados['medico_id'], $dados['data'], $dados['hora'], $duracao, $dados['tipo_slug']
        );
        if (!$ok) {
            return back()->withInput()->withErrors(['hora' => 'O médico não está disponível neste horário.']);
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
            'estado'           => 'agendada',
        ]);

        return redirect()->route('medico.consultas.index')->with('success', 'Consulta agendada!');
    }

    /** Cancela consulta do próprio médico (>24h). Mantido mas não exposto no UI. */
    public function cancelar(Consulta $consulta)
    {
        if ((int)$consulta->medico_id !== (int)Auth::id()) abort(403);

        $dataHora = $this->dataHora($consulta);
        if ($dataHora->lt(now()->addDay())) {
            return back()->withErrors(['cancelar' => 'Não é possível cancelar com menos de 24h.']);
        }

        $consulta->update(['estado' => 'cancelada_medico']);
        return back()->with('success', 'Consulta cancelada.');
    }

    /** CONFIRMAR pedido do paciente (pendente_medico -> agendada). */
    public function confirmar(Consulta $consulta)
    {
        if ((int)$consulta->medico_id !== (int)Auth::id()) abort(403);

        if (strtolower($consulta->estado ?? '') !== 'pendente_medico') {
            return back()->withErrors(['estado' => 'Esta consulta não está pendente para confirmação.']);
        }

        $dataHora = $this->dataHora($consulta);
        if ($dataHora->lt(now())) {
            return back()->withErrors(['estado' => 'Consulta já ocorreu.']);
        }

        $consulta->update(['estado' => 'agendada']);

        //notificar paciente
        $consulta->loadMissing('paciente','medico','especialidade');
        $consulta->paciente?->notify(new ConsultaConfirmada($consulta));

        return back()->with('success', 'Consulta confirmada.');
    }

    /** REJEITAR pedido do paciente (pendente_medico -> cancelada_medico). */
    public function rejeitar(Consulta $consulta)
    {
        if ((int)$consulta->medico_id !== (int)Auth::id()) abort(403);

        if (strtolower($consulta->estado ?? '') !== 'pendente_medico') {
            return back()->withErrors(['estado' => 'Esta consulta não está pendente.']);
        }

        $dataHora = $this->dataHora($consulta);
        if ($dataHora->lt(now())) {
            return back()->withErrors(['estado' => 'Consulta já ocorreu.']);
        }

        $consulta->update(['estado' => 'cancelada_medico']);
        return back()->with('success', 'Pedido rejeitado.');
    }

    /**
     * Helper seguro: devolve um Carbon com a data + hora da consulta,
     * evitando “Double time specification”.
     */
    private function dataHora(Consulta $c): Carbon
    {
        $dt = $c->data instanceof Carbon ? $c->data->copy() : Carbon::parse($c->data);
        if (!empty($c->hora)) {
            try { $dt->setTimeFromTimeString($c->hora); } catch (\Throwable $e) {}
        }
        return $dt;
    }
}
