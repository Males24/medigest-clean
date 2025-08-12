<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Especialidade;
use Illuminate\Http\Request;

class EspecialidadeController extends Controller
{
    public function index()
    {
        $especialidades = Especialidade::orderBy('nome')->paginate(15);
        return view('admin.especialidades.index', compact('especialidades'));
    }

    public function create()
    {
        return view('admin.especialidades.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => ['required','string','max:120','unique:especialidades,nome'],
        ]);

        Especialidade::create($data);

        return redirect()->route('admin.especialidades.index')
            ->with('success', 'Especialidade criada!');
    }

    public function edit(Especialidade $especialidade)
    {
        return view('admin.especialidades.edit', compact('especialidade'));
    }

    public function update(Request $request, Especialidade $especialidade)
    {
        $data = $request->validate([
            'nome' => ['required','string','max:120','unique:especialidades,nome,'.$especialidade->id],
        ]);

        $especialidade->update($data);

        return redirect()->route('admin.especialidades.index')
            ->with('success', 'Especialidade atualizada!');
    }

    public function destroy(Especialidade $especialidade)
    {
        $especialidade->delete();

        return back()->with('success', 'Especialidade removida!');
    }
}
