<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use App\Models\User;
use App\Models\Especialidade;
use App\Models\Medico;
use Illuminate\Http\Request;
use App\Services\ConsultaService;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ConsultaController extends Controller
{
    public function index()
    {
        $consultas = Consulta::with(['medico', 'paciente', 'especialidade'])->latest()->get();
        return view('admin.consultas.index', compact('consultas'));
    }

    public function create()
    {
        $medicos = User::where('role', 'medico')->orderBy('name')->get();
        $pacientes = User::where('role', 'paciente')->orderBy('name')->get();
        $especialidades = Especialidade::orderBy('nome')->get();

        return view('admin.consultas.create', compact('medicos', 'pacientes', 'especialidades'));
    }

    public function slots(Request $request, ConsultaService $svc)
    {
        $validated = $request->validate([
            'medico_id' => ['required','integer','exists:users,id'],
            'data'      => ['required','date_format:Y-m-d'],
            'duracao'   => ['nullable','integer','min:10','max:180'],
            'tipo'      => ['nullable', Rule::in(['normal','prioritaria','urgente'])], // + urgente
        ]);

        $medicoId = (int) $validated['medico_id'];
        $data     = $validated['data'];
        $duracao  = $validated['duracao'] ?? null; // deixa o serviço assumir 30 pelo tipo
        $tipo     = $validated['tipo'] ?? 'normal';

        try {
            $slots = $svc->gerarSlotsDisponiveis($medicoId, $data, $duracao, $tipo);
            return response()->json(['data' => $slots]); // { data: [...] }
        } catch (\Throwable $e) {
            \Log::error('Erro ao gerar slots (admin): '.$e->getMessage(), [
                'medico_id' => $medicoId, 'data' => $data, 'duracao' => $duracao, 'tipo' => $tipo,
            ]);
            return response()->json(['data' => []]);
        }
    }


    public function store(Request $request, ConsultaService $service)
    {
        $data = $request->validate([
            'paciente_id'      => ['required','exists:users,id'],
            'medico_id'        => ['required','exists:users,id'],
            'especialidade_id' => ['required','exists:especialidades,id'],
            'data'             => ['required','date_format:Y-m-d'],
            'hora'             => ['required','date_format:H:i'],
            'motivo'           => ['nullable','string','max:255'],
            'tipo_slug'        => ['required', Rule::in(['normal','prioritaria','urgente'])], // <- aqui
        ]);

        // médico tem a especialidade?
        $medicoPerfil = Medico::where('user_id', $data['medico_id'])->first();
        if (!$medicoPerfil) {
            return back()->withInput()->withErrors(['medico_id' => 'Médico inválido.']);
        }
        $temEspecialidade = $medicoPerfil->especialidades()
            ->where('especialidades.id', $data['especialidade_id'])
            ->exists();
        if (!$temEspecialidade) {
            return back()->withInput()->withErrors(['medico_id' => 'O médico não possui a especialidade escolhida.']);
        }

        // duração pelo tipo
        $tipo = \App\Models\ConsultaTipo::where('slug',$data['tipo_slug'])->where('ativo',1)->firstOrFail();
        $duracao = $tipo->duracao_min; // 30

        // verificar disponibilidade
        $disponivel = $service->verificarDisponibilidade(
            (int)$data['medico_id'],
            $data['data'],
            $data['hora'],
            $duracao,
            $data['tipo_slug']
        );
        if (!$disponivel) {
            return back()->withInput()->withErrors(['hora' => 'O médico não está disponível neste horário.']);
        }

        Consulta::create([
            'paciente_id'      => $data['paciente_id'],
            'medico_id'        => $data['medico_id'],
            'especialidade_id' => $data['especialidade_id'],
            'tipo_slug'        => $data['tipo_slug'],   // <- salva slug
            'data'             => $data['data'],
            'hora'             => $data['hora'],
            'duracao'          => $duracao,
            'motivo'           => $data['motivo'] ?? null,
            'estado'           => 'agendada',           // admin cria -> paciente confirma noutro fluxo
        ]);

        return redirect()->route('admin.consultas.index')->with('success', 'Consulta criada com sucesso.');
    }

    public function cancelar(Consulta $consulta)
    {
        $consulta->estado = 'cancelada';
        $consulta->save();

        return redirect()->back()->with('success', 'Consulta cancelada.');
    }
}
