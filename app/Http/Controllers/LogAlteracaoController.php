<?php

namespace App\Http\Controllers;

use App\Models\LogAlteracao;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LogAlteracaoController extends Controller
{
    public function index(): View
    {
        $registros = LogAlteracao::orderByDesc('id')->limit(200)->get();

        return view('logs-alteracao', [
            'registros' => $registros,
            // Detalhes para o modal (evita closure multiline dentro de @json).
            'detalhes' => $registros->mapWithKeys(fn ($r) => [
                $r->id => ['antes' => $r->dados_antes, 'depois' => $r->dados_depois],
            ]),
        ]);
    }

    public function limpar(): RedirectResponse
    {
        LogAlteracao::query()->truncate();

        return redirect()
            ->route('logs-alteracao')
            ->with('sucesso', 'Histórico de logs limpo.');
    }
}
