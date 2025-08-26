<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Especialidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * CRUD de Especialidades (admin) com suporte a capa (upload/URL).
 */
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
            'nome'       => ['required','string','max:120','unique:especialidades,nome'],
            'cover'      => ['nullable','image','mimes:jpg,jpeg,png,webp,avif','max:4096'], // upload
            'cover_path' => ['nullable','string','max:2048'], // URL absoluta (/img.jpg ou http...) ou caminho relativo
        ]);

        // cria a especialidade
        $esp = Especialidade::create(['nome' => $data['nome']]);

        // decide a fonte da capa: upload tem prioridade sobre cover_path textual
        $coverPath = null;
        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('especialidades', 'public'); // ex: especialidades/cardiologia.jpg
        } elseif (!empty($data['cover_path'])) {
            $coverPath = $data['cover_path']; // pode ser /cardiologia.jpg ou https://…
        }

        if ($coverPath) {
            $esp->update(['cover_path' => $coverPath]);
        }

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
            'nome'       => ['required','string','max:120','unique:especialidades,nome,'.$especialidade->id],
            'cover'      => ['nullable','image','mimes:jpg,jpeg,png,webp,avif','max:4096'],
            'cover_path' => ['nullable','string','max:2048'],
        ]);

        // atualiza nome
        $especialidade->update(['nome' => $data['nome']]);

        $newCover = null;

        // Se for feito upload, substitui a capa atual
        if ($request->hasFile('cover')) {
            // apaga a antiga se era um ficheiro local no disco 'public' (não começa por http/https/ /)
            $old = $especialidade->cover_path;
            if ($old && !Str::startsWith($old, ['http://', 'https://', '/'])) {
                Storage::disk('public')->delete($old);
            }
            $newCover = $request->file('cover')->store('especialidades', 'public');
        }
        // Se veio cover_path explícito no request, usa-o (pode ser vazio para limpar)
        elseif ($request->has('cover_path')) {
            $newCover = $data['cover_path'] ?: null;
            // se o novo valor limpar e o antigo era local, apaga-o
            if ($newCover === null) {
                $old = $especialidade->cover_path;
                if ($old && !Str::startsWith($old, ['http://', 'https://', '/'])) {
                    Storage::disk('public')->delete($old);
                }
            }
        }

        if ($newCover !== null) {
            $especialidade->update(['cover_path' => $newCover]);
        }

        return redirect()->route('admin.especialidades.index')
            ->with('success', 'Especialidade atualizada!');
    }

    public function destroy(Especialidade $especialidade)
    {
        // limpeza do ficheiro local se existir e for local
        $old = $especialidade->cover_path;
        if ($old && !Str::startsWith($old, ['http://', 'https://', '/'])) {
            Storage::disk('public')->delete($old);
        }

        $especialidade->delete();

        return back()->with('success', 'Especialidade removida!');
    }
}
