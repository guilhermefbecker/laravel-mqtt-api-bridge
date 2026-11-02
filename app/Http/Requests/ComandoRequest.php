<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ComandoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'estado' => ['required', 'string', 'in:vermelho,amarelo,verde,intermitente,ciclo,desligado'],
            'cor' => ['nullable', 'string', 'required_if:estado,vermelho,amarelo,verde', 'same:estado'],
            'tempoVermelho' => ['nullable', 'required_if:estado,ciclo', 'integer', 'min:1', 'max:3600'],
            'tempoAmarelo' => ['nullable', 'required_if:estado,ciclo', 'integer', 'min:1', 'max:3600'],
            'tempoVerde' => ['nullable', 'required_if:estado,ciclo', 'integer', 'min:1', 'max:3600'],
        ];
    }

    public function messages(): array
    {
        return [
            'estado.required' => 'Informe o estado.',
            'estado.in' => 'Estado inválido. Use vermelho, amarelo, verde, intermitente, ciclo ou desligado.',
            'cor.required_if' => 'Informe a cor.',
            'cor.same' => 'A cor deve ser igual ao estado.',
            'tempoVermelho.required_if' => 'Informe o tempo vermelho.',
            'tempoVermelho.integer' => 'Informe o tempo vermelho em segundos.',
            'tempoVermelho.min' => 'O tempo vermelho deve ser de no mínimo 1 segundo.',
            'tempoVermelho.max' => 'O tempo vermelho deve ser de no máximo 3600 segundos.',
            'tempoAmarelo.required_if' => 'Informe o tempo amarelo.',
            'tempoAmarelo.integer' => 'Informe o tempo amarelo em segundos.',
            'tempoAmarelo.min' => 'O tempo amarelo deve ser de no mínimo 1 segundo.',
            'tempoAmarelo.max' => 'O tempo amarelo deve ser de no máximo 3600 segundos.',
            'tempoVerde.required_if' => 'Informe o tempo verde.',
            'tempoVerde.integer' => 'Informe o tempo verde em segundos.',
            'tempoVerde.min' => 'O tempo verde deve ser de no mínimo 1 segundo.',
            'tempoVerde.max' => 'O tempo verde deve ser de no máximo 3600 segundos.',
        ];
    }

    // Mantém o contrato da API: 422 com {ok: false, erros: {...}}.
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'ok' => false,
            'erros' => $validator->errors(),
        ], 422));
    }
}
