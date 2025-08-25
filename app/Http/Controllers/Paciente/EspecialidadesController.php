<?php

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use App\Models\Especialidade;

/**
 * Catálogo de especialidades para o paciente.
 */
class EspecialidadesController extends Controller
{
    public function index()
    {
        $especialidades = Especialidade::query()
            ->withCount('medicos')
            ->with(['medicos' => function ($q) {
                $q->with(['user:id,name,email'])
                  ->select('medicos.id', 'medicos.user_id')
                  ->take(3);
            }])
            ->orderBy('nome')
            ->get(['id', 'nome']);

        return view('paciente.especialidades.index', compact('especialidades'));
    }

    public function show(\App\Models\Especialidade $especialidade)
    {
        // Médicos associados a esta especialidade (com dados do utilizador para avatar/nome)
        $medicos = $especialidade->medicos()
            ->with(['user:id,name,email'])       // para usar avatar_url accessor
            ->get(['medicos.id','medicos.user_id'])
            ->sortBy(fn($m) => mb_strtolower($m->user?->name ?? '')) // ordenação A–Z por nome
            ->values();

        return view('paciente.especialidades.show', compact('especialidade', 'medicos'));
    }
}
