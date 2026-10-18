<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Na edição a senha é opcional: se vazia, mantém a atual.
        $senha = $this->isMethod('POST') ? ['required', 'string', 'min:6', 'confirmed'] : ['nullable', 'string', 'min:6', 'confirmed'];

        return [
            'nome' => ['required', 'string', 'max:60'],
            'sobrenome' => ['required', 'string', 'max:80'],
            'cpf' => ['nullable', 'string', 'max:14'],
            'nascimento' => ['required', 'date'],
            'email' => ['required', 'email', 'max:120', Rule::unique('users', 'email')->ignore($this->route('usuario'))],
            'celular' => ['required', 'string', 'max:15'],
            'cep' => ['nullable', 'string', 'max:9'],
            'logradouro' => ['nullable', 'string', 'max:120'],
            'numero' => ['nullable', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:80'],
            'bairro' => ['nullable', 'string', 'max:80'],
            'cidade' => ['nullable', 'string', 'max:80'],
            'estado' => ['nullable', 'string', 'max:2'],
            'senha' => $senha,
            'senha_confirmation' => ($this->isMethod('POST') ? ['required_with:senha', 'same:senha'] : ['nullable', 'same:senha']),
            'perfil' => ['nullable', 'string', 'max:20'],
            'ativo' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nome' => 'nome',
            'sobrenome' => 'sobrenome',
            'cpf' => 'CPF',
            'nascimento' => 'data de nascimento',
            'email' => 'e-mail',
            'celular' => 'celular',
            'cep' => 'CEP',
            'logradouro' => 'logradouro',
            'numero' => 'número',
            'complemento' => 'complemento',
            'bairro' => 'bairro',
            'cidade' => 'cidade',
            'estado' => 'UF',
            'senha' => 'senha',
            'perfil' => 'perfil',
            'ativo' => 'cadastro ativo',
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'Informe o nome.',
            'nome.max' => 'O nome pode ter no máximo 60 caracteres.',
            'sobrenome.required' => 'Informe o sobrenome.',
            'sobrenome.max' => 'O sobrenome pode ter no máximo 80 caracteres.',
            'cpf.max' => 'O CPF pode ter no máximo 14 caracteres.',
            'nascimento.required' => 'Informe a data de nascimento.',
            'nascimento.date' => 'Informe a data de nascimento.',
            'email.required' => 'Informe um e-mail válido.',
            'email.email' => 'Informe um e-mail válido.',
            'email.max' => 'O e-mail pode ter no máximo 120 caracteres.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'celular.required' => 'Informe o celular.',
            'celular.max' => 'O celular pode ter no máximo 15 caracteres.',
            'senha.required' => 'Informe uma senha com pelo menos 6 caracteres.',
            'senha.min' => 'Informe uma senha com pelo menos 6 caracteres.',
            'senha.confirmed' => 'As senhas devem ser iguais.',
            'senha_confirmation.same' => 'As senhas devem ser iguais.',
        ];
    }
}
