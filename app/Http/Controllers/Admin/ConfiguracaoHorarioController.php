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
            'dias.*'        => ['integer','in:0,1,2,3,4,5,6'],
            'manha_inicio'  => ['nullable','date_format:H:i'],
            'manha_fim'     => ['nullable','date_format:H:i'],
            'tarde_inicio'  => ['nullable','date_format:H:i'],
            'tarde_fim'     => ['nullable','date_format:H:i'],
        ], [
            'dias.required' => 'Seleciona pelo menos um dia.',
        ]);

        $selecionados = collect($data['dias'])->map(fn ($d) => (int)$d)->all();

        // Para os dias SELECIONADOS: aplica valores; '' => null (limpa)
        $mi = $request->input('manha_inicio');
        $mf = $request->input('manha_fim');
        $ti = $request->input('tarde_inicio');
        $tf = $request->input('tarde_fim');

        $horasSelecionados = [
            'manha_inicio' => ($mi === '' ? null : $mi),
            'manha_fim'    => ($mf === '' ? null : $mf),
            'tarde_inicio' => ($ti === '' ? null : $ti),
            'tarde_fim'    => ($tf === '' ? null : $tf),
            'ativo'        => true,
        ];

        // 1) Atualiza e ativa os dias selecionados
        ConfiguracaoHorario::whereIn('dia_semana', $selecionados)
            ->update($horasSelecionados);

        // 2) Desativa e LIMPA completamente os dias NÃO selecionados
        ConfiguracaoHorario::whereNotIn('dia_semana', $selecionados)
            ->update([
                'manha_inicio' => null,
                'manha_fim'    => null,
                'tarde_inicio' => null,
                'tarde_fim'    => null,
                'ativo'        => false,
            ]);

        return redirect()->route('admin.horarios.index')
            ->with('success', 'Horários atualizados!');
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
