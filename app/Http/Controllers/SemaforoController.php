<?php

namespace App\Http\Controllers;

use App\Http\Requests\SemaforoRequest;
use App\Models\Semaforo;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SemaforoController extends Controller
{
    public function index(): View
    {
        $semaforos = Semaforo::orderBy('id')->get();

        return view('semaforos.index', [
            'semaforos' => $semaforos,
            'total' => $semaforos->count(),
            'ativos' => $semaforos->where('ativo', true)->count(),
            'mediaCiclo' => $semaforos->isEmpty()
                ? 0
                : (int) round($semaforos->avg('ciclo')),
        ]);
    }

    public function create(): View
    {
        return view('semaforos.create');
    }

    public function store(SemaforoRequest $request): RedirectResponse
    {
        $semaforo = Semaforo::create($request->validated() + ['ativo' => $request->boolean('ativo')]);

        return redirect()
            ->route('semaforos.index')
            ->with('sucesso', 'Cadastro criado com sucesso.')
            ->with('log', "Criou novo semáforo: {$semaforo->nome}");
    }

    public function edit(Semaforo $semaforo): View
    {
        return view('semaforos.edit', ['semaforo' => $semaforo]);
    }

    public function update(SemaforoRequest $request, Semaforo $semaforo): RedirectResponse
    {
        $semaforo->update($request->validated() + ['ativo' => $request->boolean('ativo')]);

        return redirect()
            ->route('semaforos.index')
            ->with('sucesso', 'Cadastro atualizado com sucesso.')
            ->with('log', "Atualizou cadastro do semáforo: {$semaforo->nome}");
    }

    public function destroy(Semaforo $semaforo): RedirectResponse
    {
        $semaforo->delete();

        return redirect()
            ->route('semaforos.index')
            ->with('sucesso', 'Cadastro excluído.')
            ->with('log', "Excluiu semáforo: {$semaforo->nome}");
    }

    public function limpar(): RedirectResponse
    {
        Semaforo::query()->truncate();

        return redirect()
            ->route('semaforos.index')
            ->with('sucesso', 'Todos os cadastros foram excluídos.')
            ->with('log', 'Excluiu todos os semáforos cadastrados');
    }
}
