<?php

namespace App\Services;

use App\Models\Consulta;
use App\Models\ConfiguracaoHorario; // horário GLOBAL
use App\Models\Horario;             // horário por médico (fallback)
use App\Models\ParametrosAgendamento;
use App\Models\ConsultaTipo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ConsultaService
{
    /** Normaliza '09:00:00' -> '09:00' */
    protected function hm(string $t = null): ?string
    {
        if (!$t) return null;
        // aceita 'HH:MM' ou 'HH:MM:SS'
        if (strlen($t) >= 5) return substr($t, 0, 5);
        return $t;
    }

    protected function loadAgendaGlobals(): array
    {
        $c = ParametrosAgendamento::first();
        return [
            'buffer_min' => (int)($c->buffer_min ?? 0),
            'passo_min'  => (int)($c->passo_min  ?? 30),
        ];
    }

    protected function getTipo(string $slug = 'normal'): ConsultaTipo
    {
        return ConsultaTipo::where('slug', $slug)->where('ativo', true)->firstOrFail();
    }

    protected function dentroFaixa(Carbon $inicio, Carbon $fim, ?string $faixaInicio, ?string $faixaFim): bool
    {
        $faixaInicio = $this->hm($faixaInicio);
        $faixaFim    = $this->hm($faixaFim);
        if (!$faixaInicio || !$faixaFim) return false;

        $fIni = Carbon::createFromFormat('H:i', $faixaInicio);
        $fFim = Carbon::createFromFormat('H:i', $faixaFim);
        return $inicio->gte($fIni) && $fim->lte($fFim);
    }

    /**
     * Verifica disponibilidade de um slot específico.
     * Respeita: data futura, lead_minutos, horizonte_horas, faixas horárias e conflitos com buffer.
     */
    public function verificarDisponibilidade(
        int $medicoId,
        string $dataYmd,
        string $horaInicioHm,
        ?int $duracaoMin = null,
        string $tipoSlug = 'normal'
    ): bool {
        $globals = $this->loadAgendaGlobals();
        $tipo    = $this->getTipo($tipoSlug);

        $duracao = (int)($duracaoMin ?: $tipo->duracao_min);
        $data    = Carbon::createFromFormat('Y-m-d', $dataYmd);
        $inicio  = Carbon::createFromFormat('H:i', $this->hm($horaInicioHm));
        $fim     = (clone $inicio)->addMinutes($duracao);

        // Janela temporal (mínimo e máximo)
        $minStart = now()->addMinutes((int)$tipo->lead_minutos);
        $maxDate  = now()->addHours((int)$tipo->horizonte_horas)->endOfDay();

        $inicioAbs = Carbon::create($data->year, $data->month, $data->day, $inicio->hour, $inicio->minute);

        // nunca deixar marcar antes do "agora + lead"
        if ($inicioAbs->lt($minStart)) return false;
        // nunca deixar marcar para além do horizonte
        if ($data->gt($maxDate)) return false;

        // 1) Faixas do dia (global → fallback do médico)
        $faixas = [];
        $ch = ConfiguracaoHorario::where('dia_semana', $data->dayOfWeek)->where('ativo', true)->first();
        if ($ch) {
            $mi = $this->hm($ch->manha_inicio); $mf = $this->hm($ch->manha_fim);
            $ti = $this->hm($ch->tarde_inicio); $tf = $this->hm($ch->tarde_fim);
            if ($mi && $mf) $faixas[] = [$mi, $mf];
            if ($ti && $tf) $faixas[] = [$ti, $tf];
        } else {
            $horariosMed = Horario::where('medico_id', $medicoId)
                ->where('dia_semana', $data->dayOfWeek)
                ->where('disponivel', true)
                ->get();
            foreach ($horariosMed as $h) {
                $faixas[] = [$this->hm($h->hora_inicio), $this->hm($h->hora_fim)];
            }
        }
        if (!$faixas) return false;

        // slot precisa caber integralmente numa das faixas
        $okFaixa = false;
        foreach ($faixas as [$ini, $fimFaixa]) {
            if ($this->dentroFaixa($inicio, $fim, $ini, $fimFaixa)) { $okFaixa = true; break; }
        }
        if (!$okFaixa) return false;

        // 2) Conflitos com buffer
        $buffer = (int) $globals['buffer_min'];
        // Novo intervalo com buffer aplicado
        $inicioBuf = (clone $inicio)->subMinutes($buffer)->format('H:i');
        $fimBuf    = (clone $fim)->addMinutes($buffer)->format('H:i');
        // Conflito se: existing_start < new_end  E  existing_end > new_start
        $existeConflito = Consulta::where('medico_id', $medicoId)
            ->where('data', $data->toDateString())
            ->whereIn('estado', ['agendada','confirmada','pendente_medico'])
            ->where(function ($q) use ($inicioBuf, $fimBuf) {
                $q->whereRaw('hora < ? AND ADDTIME(hora, SEC_TO_TIME(duracao * 60)) > ?', [$fimBuf, $inicioBuf]);
            })
            ->exists();

        return !$existeConflito;
    }

    /**
     * Devolve lista de horas HH:MM disponíveis para um dia.
     * Aplica lead/horizonte e ignora automaticamente horas já passadas no próprio dia.
     */
    public function gerarSlotsDisponiveis(
        int $medicoId,
        string $dataYmd,
        ?int $duracaoMin = null,
        string $tipoSlug = 'normal'
    ): array {
        $globals = $this->loadAgendaGlobals();
        $tipo    = $this->getTipo($tipoSlug);
        $duracao = (int)($duracaoMin ?: $tipo->duracao_min);

        $data     = Carbon::createFromFormat('Y-m-d', $dataYmd);
        $minStart = now()->addMinutes((int)$tipo->lead_minutos);
        $maxDate  = now()->addHours((int)$tipo->horizonte_horas)->endOfDay();

        // fora da janela -> nada
        if ($data->gt($maxDate)) return [];
        // se a "data" ainda é anterior à data do minStart, também nada
        if ($data->lt($minStart->copy()->startOfDay())) return [];

        // Faixas (global → fallback médico)
        $faixas = [];
        $ch = ConfiguracaoHorario::where('dia_semana', $data->dayOfWeek)->where('ativo', true)->first();
        if ($ch) {
            $mi = $this->hm($ch->manha_inicio); $mf = $this->hm($ch->manha_fim);
            $ti = $this->hm($ch->tarde_inicio); $tf = $this->hm($ch->tarde_fim);
            if ($mi && $mf) $faixas[] = [$mi, $mf];
            if ($ti && $tf) $faixas[] = [$ti, $tf];
        } else {
            $horariosMed = Horario::where('medico_id', $medicoId)
                ->where('dia_semana', $data->dayOfWeek)
                ->where('disponivel', true)
                ->get();
            foreach ($horariosMed as $h) {
                $faixas[] = [$this->hm($h->hora_inicio), $this->hm($h->hora_fim)];
            }
        }
        if (!$faixas) return [];

        $slots = [];
        $passo = (int) ($globals['passo_min'] ?? 0);
        if ($passo < 1) { // 0 ou negativo -> usa a duração do tipo
            $passo = $duracao;
        }

        foreach ($faixas as [$ini, $fim]) {
            // se o formato vier 'HH:MM:SS', corta
            $ini = $this->hm($ini);
            $fim = $this->hm($fim);

            $cursor = Carbon::createFromFormat('H:i', $ini);
            $limite = Carbon::createFromFormat('H:i', $fim);

            while ($cursor->lt($limite)) {
                $inicio  = $cursor->copy();
                $termino = $cursor->copy()->addMinutes($duracao);
                if ($termino->gt($limite)) break;

                // 1) nunca propor horas "para trás" no dia de hoje (ou no dia de minStart)
                $inicioAbs = Carbon::create($data->year, $data->month, $data->day, $inicio->hour, $inicio->minute);
                if ($inicioAbs->lt($minStart)) { $cursor->addMinutes($passo); continue; }

                // 2) valida slot com todas as regras
                if ($this->verificarDisponibilidade($medicoId, $dataYmd, $inicio->format('H:i'), $duracao, $tipoSlug)) {
                    $slots[] = $inicio->format('H:i');
                }

                $cursor->addMinutes($passo);
            }
        }

        return $slots;
    }

    public function agendarComTransacao(array $dados): ?int
    {
        return DB::transaction(function () use ($dados) {
            $tipoSlug = $dados['tipo_slug'] ?? ($dados['tipo'] ?? 'normal');

            $ok = $this->verificarDisponibilidade(
                (int)$dados['medico_id'],
                (string)$dados['data'],
                (string)$dados['hora'],
                isset($dados['duracao']) ? (int)$dados['duracao'] : null,
                $tipoSlug
            );
            if (!$ok) return null;

            // garantir duracao/tipo coerentes
            $tipo = $this->getTipo($tipoSlug);
            $dados['duracao']   = isset($dados['duracao']) ? (int)$dados['duracao'] : (int)$tipo->duracao_min;
            $dados['tipo_slug'] = $tipo->slug;

            $consulta = Consulta::create($dados);
            return $consulta->id;
        });
    }
}
