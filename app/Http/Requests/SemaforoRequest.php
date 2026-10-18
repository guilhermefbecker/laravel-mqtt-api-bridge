<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SemaforoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:60'],
            'tempo_vermelho' => ['required', 'integer', 'min:100'],
            'tempo_amarelo' => ['required', 'integer', 'min:100'],
            'tempo_verde' => ['required', 'integer', 'min:100'],
            'latitude_longitude' => ['nullable', 'string', 'max:255'],
            'ativo' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nome' => 'nome',
            'tempo_vermelho' => 'tempo do vermelho',
            'tempo_amarelo' => 'tempo do amarelo',
            'tempo_verde' => 'tempo do verde',
            'latitude_longitude' => 'latitude e longitude',
            'ativo' => 'cadastro ativo',
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'Informe um nome.',
            'nome.max' => 'O nome pode ter no máximo 60 caracteres.',
            'tempo_vermelho.required' => 'Use um valor a partir de 100 ms.',
            'tempo_vermelho.min' => 'Use um valor a partir de 100 ms.',
            'tempo_vermelho.integer' => 'Use um valor a partir de 100 ms.',
            'tempo_amarelo.required' => 'Use um valor a partir de 100 ms.',
            'tempo_amarelo.min' => 'Use um valor a partir de 100 ms.',
            'tempo_amarelo.integer' => 'Use um valor a partir de 100 ms.',
            'tempo_verde.required' => 'Use um valor a partir de 100 ms.',
            'tempo_verde.min' => 'Use um valor a partir de 100 ms.',
            'tempo_verde.integer' => 'Use um valor a partir de 100 ms.',
        ];
    }
}
