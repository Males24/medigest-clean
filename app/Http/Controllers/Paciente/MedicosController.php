<?php

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use App\Models\Medico;
use App\Models\Especialidade;
use Illuminate\Http\Request;

/**
 * Listagem e (opcional) perfil de médicos visível ao paciente.
 */
class MedicosController extends Controller
{
    public function index(Request $request)
    {
        $medicos = Medico::query()
            ->with(['user:id,name,email','especialidades:id,nome'])
            ->get(['id', 'user_id']);

        $especialidades = Especialidade::orderBy('nome')->get(['id', 'nome']);

        return view('paciente.medicos.index', compact('medicos', 'especialidades'));
    }

    public function show(Medico $medico)
    {
        $medico->load(['user:id,name,email', 'especialidades:id,nome']);
        return view('paciente.medicos.show', compact('medico'));
    }

    /** API usada no wizard: médicos por especialidade (devolve USERS.ID em 'id'). */
    public function medicosPorEspecialidade(int $id)
    {
        // Lista médicos dessa especialidade + dados mínimos do utilizador
        $lista = Medico::query()
            ->whereHas('especialidades', fn($q) => $q->where('especialidades.id', $id))
            ->with(['user:id,name,email,updated_at']) // sem dependência de coluna física 'avatar'
            ->get(['id','user_id']);

        // O wizard/slots precisam do USERS.ID
        $payload = $lista->map(fn($m) => [
            'id'         => (int) $m->user_id,         // <- USERS.ID (não o medicos.id)
            'name'       => $m->user?->name,
            'email'      => $m->user?->email,
            'avatar_url' => $m->user?->avatar_url,    // accessor
        ])->values();

        return response()->json($payload);
    }
}
