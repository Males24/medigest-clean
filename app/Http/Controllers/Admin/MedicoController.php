<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medico;
use App\Models\User;
use App\Models\Especialidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class MedicoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $medicos = Medico::with('user', 'especialidades')->paginate(10);
        return view('admin.medicos.index', compact('medicos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $especialidades = Especialidade::orderBy('nome')->get();
        return view('admin.medicos.create', compact('especialidades'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'crm' => 'required|string|max:50|unique:medicos,crm',
            'bio' => 'nullable|string',
            'especialidades'   => 'nullable|array',
            'especialidades.*' => 'exists:especialidades,id'
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'medico',
        ]);

        $medico = Medico::create([
            'user_id' => $user->id,
            'crm'     => $data['crm'],
            'bio'     => $data['bio'] ?? null,
        ]);

        if (!empty($data['especialidades'])) {
            $medico->especialidades()->sync($data['especialidades']);
        }

        return redirect()->route('admin.medicos.index')->with('success', 'Médico criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $medico = Medico::with('user', 'especialidades')->findOrFail($id);
        $especialidades = Especialidade::orderBy('nome')->get();
        return view('admin.medicos.edit', compact('medico', 'especialidades'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $medico = Medico::with('user')->findOrFail($id);

        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => ['required','email', Rule::unique('users','email')->ignore($medico->user_id)],
            'password' => 'nullable|min:6|confirmed',
            'crm' => ['required','string','max:50', Rule::unique('medicos','crm')->ignore($medico->id)],
            'bio' => 'nullable|string',
            'especialidades'   => 'nullable|array',
            'especialidades.*' => 'exists:especialidades,id'
        ]);

        $medico->user->update([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'] ? Hash::make($data['password']) : $medico->user->password,
        ]);

        $medico->update([
            'crm' => $data['crm'],
            'bio' => $data['bio'] ?? null,
        ]);

        $medico->especialidades()->sync($data['especialidades'] ?? []);

        return redirect()->route('admin.medicos.index')->with('success', 'Médico atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $medico = Medico::with('user')->findOrFail($id);
        $medico->user->delete();
        return redirect()->route('admin.medicos.index')->with('success', 'Médico removido com sucesso.');
    }

    /**
     * AJAX: lista de médicos (users.role=medico) que possuem a especialidade
     */
    public function porEspecialidade(\App\Models\Especialidade $especialidade)
    {
        $medicos = \App\Models\Medico::whereHas('especialidades', function ($q) use ($especialidade) {
                $q->where('especialidades.id', $especialidade->id);
            })
            ->with('user:id,name,email')
            ->get()
            ->map(fn($m) => [
                'id'    => $m->user_id,     // devolve users.id
                'name'  => $m->user->name,
                'email' => $m->user->email,
            ])
            ->values();

        return response()->json($medicos);
    }
}
