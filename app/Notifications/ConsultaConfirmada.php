<?php

namespace App\Notifications;

use App\Models\Consulta;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ConsultaConfirmada extends Notification
{
    use Queueable;

    public function __construct(public Consulta $consulta) {}

    public function via(object $notifiable): array
    {
        // só BD; podes adicionar 'mail' se quiseres enviar e-mail
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $c = $this->consulta->loadMissing('medico','especialidade');
        return [
            'consulta_id'   => $c->id,
            'data'          => $c->data instanceof \Carbon\Carbon ? $c->data->toDateString() : (string)$c->data,
            'hora'          => (string)($c->hora ?? ''),
            'medico_nome'   => $c->medico->name ?? '',
            'especialidade' => $c->especialidade->nome ?? '',
        ];
    }
}
