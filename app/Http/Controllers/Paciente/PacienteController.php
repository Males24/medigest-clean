<?php

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class PacienteController extends Controller
{
    public function index()
    {
        $pacienteId = Auth::id();

        $proximas = Consulta::with(['medico','especialidade','tipo'])
            ->where('paciente_id', $pacienteId)
            ->ativas()
            ->futuras()                          // << só futuras (data + hora)
            ->orderBy('data')->orderBy('hora')
            ->limit(8)
            ->get();

        $stats = [
            'futuras'   => $proximas->count(),
            'pendentes' => Consulta::where('paciente_id', $pacienteId)
                ->whereIn('estado', ['pendente','pendente_medico'])
                ->count(),
        ];

        return view('paciente.home', compact('proximas','stats'));
    }

    public function dismissAlert(DatabaseNotification $notification)
    {
        abort_unless($notification->notifiable_id === auth()->id(), 403);
        $notification->markAsRead();

        return back();
    }
}
