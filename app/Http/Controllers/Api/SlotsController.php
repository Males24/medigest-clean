<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ConsultaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * API: devolve slots disponíveis para marcação.
 */
class SlotsController extends Controller
{
    /**
     * GET /api/slots?medico_id&data=Y-m-d|d/m/Y&tipo&duracao
     * Retorna { data: ["09:00","09:30", ...] }
     */
    public function index(Request $request, ConsultaService $svc)
    {
        try {
            $request->validate([
                'medico_id' => ['nullable','integer','exists:users,id'],
                'data'      => ['required','string'],
                'tipo'      => ['nullable','in:normal,prioritaria,urgente'],
                'duracao'   => ['nullable','integer','min:10','max:180'],
            ]);

            // Resolve medico_id por role
            $medicoId = $request->integer('medico_id') ?: null;
            if (Auth::check() && Auth::user()->role === 'medico' && !$medicoId) {
                $medicoId = Auth::id();
            }
            if (Auth::check() && Auth::user()->role === 'paciente' && !$medicoId) {
                return response()->json(['data' => [], 'error' => 'medico_id é obrigatório para pacientes'], 422);
            }
            if (!$medicoId) {
                return response()->json(['data' => [], 'error' => 'medico_id é obrigatório'], 422);
            }

            // Normaliza data
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

            return response()->json(['data' => is_array($slots) ? $slots : []]);
        } catch (\Throwable $e) {
            \Log::warning('API /api/slots falhou', [
                'msg' => $e->getMessage(),
                'q'   => $request->all(),
            ]);
            return response()->json(['data' => []]); // sem partir o UI
        }
    }
}
