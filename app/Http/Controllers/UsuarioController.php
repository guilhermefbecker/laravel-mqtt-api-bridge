<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioRequest;
use App\Models\LogAlteracao;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function index(): View
    {
        $usuarios = User::orderBy('id')->get();

        return view('usuarios.index', [
            'usuarios' => $usuarios,
            'total' => $usuarios->count(),
            'ativos' => $usuarios->where('ativo', true)->count(),
        ]);
    }

    public function create(): View
    {
        return view('usuarios.create');
    }

    public function store(UsuarioRequest $request): RedirectResponse
    {
        $dados = $request->validated();
        $dados['name'] = $dados['nome'];
        $dados['ativo'] = $request->boolean('ativo');
        // password recebe texto puro; o cast 'hashed' do User faz o Hash::make.
        $dados['password'] = $dados['senha'];
        unset($dados['nome'], $dados['senha']);

        $usuario = User::create($dados);

        LogAlteracao::registrar(
            'criou',
            'usuarios',
            $usuario->id,
            null,
            $usuario->only(self::camposAuditados()),
            "Criou novo usuário: {$usuario->name}",
        );

        return redirect()
            ->route('usuarios.index')
            ->with('sucesso', 'Usuário salvo com sucesso.');
    }

    public function edit(User $usuario): View
    {
        return view('usuarios.edit', ['usuario' => $usuario]);
    }

    public function update(UsuarioRequest $request, User $usuario): RedirectResponse
    {
        $antes = $usuario->only(self::camposAuditados());

        $dados = $request->validated();
        $dados['name'] = $dados['nome'];
        $dados['ativo'] = $request->boolean('ativo');
        unset($dados['nome']);

        // Senha vazia na edição mantém a atual.
        if (empty($dados['senha'])) {
            unset($dados['senha']);
        }

        $usuario->update($dados);

        LogAlteracao::registrar(
            'atualizou',
            'usuarios',
            $usuario->id,
            $antes,
            $usuario->fresh()->only(self::camposAuditados()),
            "Atualizou usuário: {$usuario->name}",
        );

        return redirect()
            ->route('usuarios.index')
            ->with('sucesso', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $usuario): RedirectResponse
    {
        LogAlteracao::registrar(
            'excluiu',
            'usuarios',
            $usuario->id,
            $usuario->only(self::camposAuditados()),
            null,
            "Excluiu usuário: {$usuario->name}",
        );

        $usuario->delete();

        return redirect()
            ->route('usuarios.index')
            ->with('sucesso', 'Usuário excluído com sucesso.');
    }

    public function limpar(): RedirectResponse
    {
        User::query()->truncate();

        LogAlteracao::registrar('limpou', 'usuarios', null, null, null, 'Excluiu todos os usuários cadastrados');

        return redirect()
            ->route('usuarios.index')
            ->with('sucesso', 'Todos os usuários foram excluídos.');
    }

    private static function camposAuditados(): array
    {
        // password fica de fora de propósito: nunca gravar em log.
        return ['name', 'sobrenome', 'cpf', 'nascimento', 'celular', 'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'estado', 'perfil', 'ativo', 'email'];
    }
}
