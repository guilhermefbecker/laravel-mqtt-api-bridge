<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
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

        return redirect()
            ->route('usuarios.index')
            ->with('sucesso', 'Usuário salvo com sucesso.')
            ->with('log', "Criou novo usuário: {$usuario->name}");
    }

    public function edit(User $usuario): View
    {
        return view('usuarios.edit', ['usuario' => $usuario]);
    }

    public function update(UsuarioRequest $request, User $usuario): RedirectResponse
    {
        $dados = $request->validated();
        $dados['name'] = $dados['nome'];
        $dados['ativo'] = $request->boolean('ativo');
        unset($dados['nome']);

        // Senha vazia na edição mantém a atual.
        if (empty($dados['senha'])) {
            unset($dados['senha']);
        }

        $usuario->update($dados);

        return redirect()
            ->route('usuarios.index')
            ->with('sucesso', 'Usuário atualizado com sucesso.')
            ->with('log', "Atualizou usuário: {$usuario->name}");
    }

    public function destroy(User $usuario): RedirectResponse
    {
        $usuario->delete();

        return redirect()
            ->route('usuarios.index')
            ->with('sucesso', 'Usuário excluído com sucesso.')
            ->with('log', "Excluiu usuário: {$usuario->name}");
    }

    public function limpar(): RedirectResponse
    {
        User::query()->truncate();

        return redirect()
            ->route('usuarios.index')
            ->with('sucesso', 'Todos os usuários foram excluídos.')
            ->with('log', 'Excluiu todos os usuários cadastrados');
    }
}
