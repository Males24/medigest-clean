<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConsultaRequest;
use App\Models\Consulta;
use App\Models\User;
use App\Models\Especialidade;
use App\Models\Medico;
use Illuminate\Http\Request;
use App\Services\ConsultaService;
use Illuminate\Validation\Rule;

/**
 * CRUD de consultas pelo administrador.
 */
class ConsultaController extends Controller
{
    /** Lista de consultas com filtros. */
    public function index(Request $request)
    {
        $q = Consulta::with(['medico', 'paciente', 'especialidade']);

        // Busca livre (paciente/médico/especialidade)
        if ($request->filled('q')) {
            $term = trim($request->input('q'));
            $q->where(function ($w) use ($term) {
                $w->whereHas('paciente', function ($s) use ($term) {
                    $s->where('name', 'like', "%{$term}%")
                      ->orWhere('email', 'like', "%{$term}%");
                })->orWhereHas('medico', function ($s) use ($term) {
                    $s->where('name', 'like', "%{$term}%")
                      ->orWhere('email', 'like', "%{$term}%");
                })->orWhereHas('especialidade', function ($s) use ($term) {
                    $s->where('nome', 'like', "%{$term}%");
                });
            });
        }

        // Filtros de data
        if ($request->filled('data_de'))  $q->whereDate('data', '>=', $request->input('data_de'));
        if ($request->filled('data_ate')) $q->whereDate('data', '<=', $request->input('data_ate'));

        // Filtros específicos
        if ($request->filled('paciente_id'))      $q->where('paciente_id', $request->input('paciente_id'));
        if ($request->filled('medico_id'))        $q->where('medico_id', $request->input('medico_id'));
        if ($request->filled('especialidade_id')) $q->where('especialidade_id', $request->input('especialidade_id'));

        $consultas = $q->orderBy('data', 'desc')
                       ->orderBy('hora', 'desc')
                       ->paginate(12)
                       ->withQueryString();

        $medicos        = User::where('role', 'medico')->orderBy('name')->get(['id','name','email']);
        $pacientes      = User::where('role', 'paciente')->orderBy('name')->get(['id','name','email']);
        $especialidades = Especialidade::orderBy('nome')->get(['id','nome']);

        return view('admin.consultas.index', compact('consultas','medicos','pacientes','especialidades'));
    }

    /** Form para criar nova consulta. */
    public function create()
    {
        $medicos        = User::where('role', 'medico')->orderBy('name')->get();
        $pacientes      = User::where('role', 'paciente')->orderBy('name')->get();
        $especialidades = Especialidade::orderBy('nome')->get();

        return view('admin.consultas.create', compact('medicos', 'pacientes', 'especialidades'));
    }

    /** Endpoint de slots (AJAX) — se precisares para o form admin. */
    public function slots(Request $request, ConsultaService $svc)
    {
        $validated = $request->validate([
            'medico_id' => ['required','integer','exists:users,id'],
            'data'      => ['required','date_format:Y-m-d'],
            'duracao'   => ['nullable','integer','min:10','max:180'],
            'tipo'      => ['nullable', Rule::in(['normal','prioritaria','urgente'])],
        ]);

        $medicoId = (int) $validated['medico_id'];
        $data     = $validated['data'];
        $duracao  = $validated['duracao'] ?? null;
        $tipo     = $validated['tipo'] ?? 'normal';

        try {
            $slots = $svc->gerarSlotsDisponiveis($medicoId, $data, $duracao, $tipo);
            return response()->json(['data' => $slots]);
        } catch (\Throwable $e) {
            \Log::error('Erro ao gerar slots (admin): '.$e->getMessage(), compact('medicoId','data','duracao','tipo'));
            return response()->json(['data' => []]);
        }
    }

    /** POST: cria consulta (com validações de especialidade e disponibilidade). */
    public function store(StoreConsultaRequest $request, ConsultaService $service)
    {
        $dados = $request->validated();

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

        // tipo -> duração
        $tipo = \App\Models\ConsultaTipo::where('slug', $dados['tipo_slug'])->where('ativo',1)->firstOrFail();
        $duracao = (int) $tipo->duracao_min;

        // disponibilidade
        $ok = $service->verificarDisponibilidade(
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
            'motivo'           => $dados['descricao'] ?? null, // mapeia textarea
            'estado'           => 'agendada',
        ]);

        return redirect()->route('admin.consultas.index')->with('success', 'Consulta criada com sucesso.');
    }

    /** POST: cancela uma consulta (admin). */
    public function cancelar(Consulta $consulta)
    {
        $consulta->estado = 'cancelada';
        $consulta->save();

        return redirect()->back()->with('success', 'Consulta cancelada.');
    }
}
