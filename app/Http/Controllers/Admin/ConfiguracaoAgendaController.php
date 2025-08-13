<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ParametrosAgendamento;
use Illuminate\Http\Request;

class ParametrosAgendamentoController extends Controller
{
    // Mostrar a página de configuração (singleton)
    public function index()
    {
        $config = ParametrosAgendamento::firstOrCreate([], ['tipo' => 'semanal']);
        return view('admin.agenda.index', compact('config'));
    }

    // Página de edição
    public function edit()
    {
        $config = ParametrosAgendamento::firstOrCreate([], ['tipo' => 'semanal']);
        return view('admin.agenda.edit', compact('config'));
    }

    // Guardar alterações
    public function update(Request $request)
    {
        $data = $request->validate([
            'tipo' => ['required', 'in:diario,semanal,quinzenal,mensal'],
        ], [
            'tipo.required' => 'Escolhe um tipo de agenda.',
            'tipo.in' => 'Valor inválido.',
        ]);

        $config = ParametrosAgendamento::firstOrCreate([]);
        $config->update($data);

        // Se fores usar cache para leitura
        // cache()->forever('agenda_tipo', $config->tipo);

        return redirect()->route('admin.agenda.index')->with('success', 'Configuração de agenda atualizada!');
    }
}
