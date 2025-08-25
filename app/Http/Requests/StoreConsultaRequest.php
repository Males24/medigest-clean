<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreConsultaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Se quiseres apertar: return auth()->check() && auth()->user()->role === 'paciente';
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $role = auth()->user()?->role;

        return [
            // Paciente só não envia paciente_id; admin/médico têm de escolher um paciente
            'paciente_id'      => [$role === 'paciente' ? 'sometimes' : 'required', 'integer', 'exists:users,id'],

            'especialidade_id' => ['required','integer','exists:especialidades,id'],
            'medico_id'        => ['required','integer','exists:users,id'],  // users.id do médico
            'tipo_slug'        => ['required', Rule::in(['normal','prioritaria','urgente'])],
            'data'             => ['required','date_format:Y-m-d','after_or_equal:today'],
            'hora'             => ['required','date_format:H:i'],

            // opcional (é calculada pelo tipo mas pode vir do form)
            'duracao'          => ['sometimes','integer','min:10','max:180'],

            // textarea
            'descricao'        => ['nullable','string','max:400'],
        ];
    }

    public function messages(): array
    {
        return [
            'paciente_id.required'      => 'Selecione o paciente.',
            'paciente_id.exists'        => 'O paciente selecionado não existe.',

            'especialidade_id.required' => 'Selecione a especialidade.',
            'especialidade_id.exists'   => 'A especialidade selecionada não existe.',

            'medico_id.required'        => 'Selecione um médico.',
            'medico_id.exists'          => 'O médico selecionado não existe.',

            'tipo_slug.required'        => 'Selecione o tipo de consulta.',
            'tipo_slug.in'              => 'Tipo de consulta inválido.',

            'data.required'             => 'Escolha a data da consulta.',
            'data.after_or_equal'       => 'A data deve ser hoje ou futura.',

            'hora.required'             => 'Escolha o horário da consulta.',
        ];
    }
}
