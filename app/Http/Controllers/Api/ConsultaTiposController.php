<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConsultaTipo;

class ConsultaTiposController extends Controller
{
    /**
     * GET /api/consulta-tipos/{slug}
     * Devolve metadados do tipo de consulta para o frontend.
     */
    public function show(string $slug)
    {
        $tipo = ConsultaTipo::where('slug', $slug)->where('ativo', true)->first();

        if (!$tipo) {
            return response()->json(['error' => 'Tipo não encontrado'], 404);
        }

        return response()->json([
            'slug'            => $tipo->slug,
            'nome'            => $tipo->nome,
            'lead_minutos'    => (int) $tipo->lead_minutos,
            'horizonte_horas' => (int) $tipo->horizonte_horas,
            'duracao_min'     => (int) $tipo->duracao_min,
            'ativo'           => (bool) $tipo->ativo,
        ]);
    }

    /**
     * (Opcional) GET /api/consulta-tipos
     * Lista todos os tipos ativos.
     */
    public function index()
    {
        $tipos = ConsultaTipo::where('ativo', true)
            ->orderByRaw("FIELD(slug,'urgente','prioritaria','normal') DESC")
            ->get(['slug','nome','lead_minutos','horizonte_horas','duracao_min','ativo']);

        return response()->json($tipos);
    }
}
