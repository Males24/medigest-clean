<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracaoHorario;
use Illuminate\Http\Request;

class ConfiguracaoHorarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $horarios = ConfiguracaoHorario::orderBy('dia_semana')->get();
        return view('admin.horarios.index', compact('horarios'));
    }

    /**
     * Página para configurar vários dias de uma vez
     */
    public function configurar()
    {
        $horarios = ConfiguracaoHorario::orderBy('dia_semana')->get();
        $diasSemana = ConfiguracaoHorario::diasSemana();
        return view('admin.horarios.configurar', compact('horarios', 'diasSemana'));
    }

    /**
     * Atualiza em massa os dias selecionados
     */
    public function atualizarTodos(Request $request)
    {
        $data = $request->validate([
            'dias'          => ['required','array','min:1'],
            'dias.*'        => ['integer','in:1,2,3,4,5,6,7'],
            'manha_inicio'  => ['nullable','date_format:H:i'],
            'manha_fim'     => ['nullable','date_format:H:i'],
            'tarde_inicio'  => ['nullable','date_format:H:i'],
            'tarde_fim'     => ['nullable','date_format:H:i'],
            'ativo'         => ['nullable','boolean'],
        ], [
            'dias.required' => 'Seleciona pelo menos um dia.',
        ]);

        // Só altera os campos que realmente vierem preenchidos
        $campos = [
            'manha_inicio' => $request->input('manha_inicio'),
            'manha_fim'    => $request->input('manha_fim'),
            'tarde_inicio' => $request->input('tarde_inicio'),
            'tarde_fim'    => $request->input('tarde_fim'),
        ];

        // Limpa vazios
        foreach ($campos as $k => $v) {
            if ($v === '' || $v === null) unset($campos[$k]);
        }

        // Se enviou o checkbox "ativo", força para true; se quiseres permitir desativar, adapta aqui
        if ($request->filled('ativo')) {
            $campos['ativo'] = true;
        }

        if (!empty($campos)) {
            ConfiguracaoHorario::whereIn('dia_semana', $data['dias'])->update($campos);
        }

        return redirect()->route('admin.horarios.index')->with('success', 'Horários atualizados!');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ConfiguracaoHorario $configuracaoHorario)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ConfiguracaoHorario $configuracaoHorario)
    {
        $request->validate([
            'manha_inicio' => 'nullable|date_format:H:i',
            'manha_fim'    => 'nullable|date_format:H:i',
            'tarde_inicio' => 'nullable|date_format:H:i',
            'tarde_fim'    => 'nullable|date_format:H:i',
            'ativo'        => 'boolean',
        ]);

        $configuracaoHorario->update($request->all());

        return redirect()->route('admin.horarios.index')->with('success', 'Horário atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
