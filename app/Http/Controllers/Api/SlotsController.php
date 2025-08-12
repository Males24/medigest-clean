<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ConsultaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SlotsController extends Controller
{
    /**
     * GET /api/slots
     * Query: medico_id, data (Y-m-d ou d/m/Y), tipo (normal|prioritaria|urgente), duracao (min)
     * Resposta: { data: ["09:00","09:30", ...] }
     */
    public function index(Request $request, ConsultaService $svc)
    {
        try {
            // Validação branda: normalizamos depois
            $request->validate([
                'medico_id' => ['nullable','integer','exists:users,id'],
                'data'      => ['required','string'],
                'tipo'      => ['nullable','in:normal,prioritaria,urgente'],
                'duracao'   => ['nullable','integer','min:10','max:180'],
            ]);

            // Resolve medico_id por role
            $medicoId = $request->integer('medico_id') ?: null;
            if (Auth::check() && Auth::user()->role === 'medico' && !$medicoId) {
                $medicoId = Auth::id(); // médico pode omitir
            }
            if (Auth::check() && Auth::user()->role === 'paciente' && !$medicoId) {
                return response()->json(['data' => [], 'error' => 'medico_id é obrigatório para pacientes'], 422);
            }
            if (!$medicoId) {
                // admin sem medico_id explícito -> exigir
                return response()->json(['data' => [], 'error' => 'medico_id é obrigatório'], 422);
            }

            // Normaliza data: "10/08/2025" -> "2025-08-10"
            $raw = trim((string) $request->input('data'));
            $data = str_contains($raw, '/')
                ? sprintf('%04d-%02d-%02d', ...array_map('intval', array_reverse(explode('/', $raw))))
                : $raw;

            $slots = $svc->gerarSlotsDisponiveis(
                $medicoId,
                $data,
                (int) ($request->input('duracao') ?? 30),
                $request->input('tipo', 'normal')
            );

            // Nunca partir o UI
            return response()->json(['data' => is_array($slots) ? $slots : []]);
        } catch (\Throwable $e) {
            \Log::warning('API /api/slots falhou', [
                'msg' => $e->getMessage(),
                'q'   => $request->all(),
            ]);
            return response()->json(['data' => []]); // 200 vazio
        }
    }
}
