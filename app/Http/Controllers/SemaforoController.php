<?php

namespace App\Http\Controllers;

use App\Http\Requests\SemaforoRequest;
use App\Models\LogAlteracao;
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

        LogAlteracao::registrar(
            'criou',
            'semaforos',
            $semaforo->id,
            null,
            $semaforo->only(self::camposAuditados()),
            "Criou novo semáforo: {$semaforo->nome}",
        );

        return redirect()
            ->route('semaforos.index')
            ->with('sucesso', 'Cadastro criado com sucesso.');
    }

    public function edit(Semaforo $semaforo): View
    {
        return view('semaforos.edit', ['semaforo' => $semaforo]);
    }

    public function update(SemaforoRequest $request, Semaforo $semaforo): RedirectResponse
    {
        $antes = $semaforo->only(self::camposAuditados());

        $semaforo->update($request->validated() + ['ativo' => $request->boolean('ativo')]);

        LogAlteracao::registrar(
            'atualizou',
            'semaforos',
            $semaforo->id,
            $antes,
            $semaforo->fresh()->only(self::camposAuditados()),
            "Atualizou cadastro do semáforo: {$semaforo->nome}",
        );

        return redirect()
            ->route('semaforos.index')
            ->with('sucesso', 'Cadastro atualizado com sucesso.');
    }

    public function destroy(Semaforo $semaforo): RedirectResponse
    {
        LogAlteracao::registrar(
            'excluiu',
            'semaforos',
            $semaforo->id,
            $semaforo->only(self::camposAuditados()),
            null,
            "Excluiu semáforo: {$semaforo->nome}",
        );

        $semaforo->delete();

        return redirect()
            ->route('semaforos.index')
            ->with('sucesso', 'Cadastro excluído.');
    }

    public function limpar(): RedirectResponse
    {
        Semaforo::query()->truncate();

        LogAlteracao::registrar('limpou', 'semaforos', null, null, null, 'Excluiu todos os semáforos cadastrados');

        return redirect()
            ->route('semaforos.index')
            ->with('sucesso', 'Todos os cadastros foram excluídos.');
    }

    private static function camposAuditados(): array
    {
        return ['nome', 'tempo_vermelho', 'tempo_amarelo', 'tempo_verde', 'latitude_longitude', 'ativo'];
    }
}
