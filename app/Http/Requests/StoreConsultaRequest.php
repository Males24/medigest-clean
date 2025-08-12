<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsultaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Garante que apenas utilizadores autenticados podem criar consulta
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'medico_id'      => ['required', 'exists:users,id'],
            'data'           => ['required', 'date', 'after_or_equal:today'],
            'hora'           => ['required', 'date_format:H:i'],
            'tipo'           => ['required', 'in:normal,prioritaria'],
            'duracao'        => ['required', 'integer', 'min:15', 'max:120'],
            'especialidade_id' => ['nullable', 'exists:especialidades,id'],
            'motivo'         => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'medico_id.required' => 'Selecione um médico.',
            'medico_id.exists'   => 'O médico selecionado não existe.',
            'data.required'      => 'Escolha a data da consulta.',
            'data.after_or_equal'=> 'A data deve ser hoje ou futura.',
            'hora.required'      => 'Escolha o horário da consulta.',
            'tipo.in'            => 'O tipo de consulta deve ser normal ou prioritária.',
        ];
    }
}
