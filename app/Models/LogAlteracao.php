<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAlteracao extends Model
{
    // Nome fora da convenção (plural daria "log_alteracaos").
    protected $table = 'logs_alteracao';

    protected $fillable = [
        'usuario',
        'acao',
        'tabela',
        'registro_id',
        'dados_antes',
        'dados_depois',
        'descricao',
    ];

    protected $casts = [
        'dados_antes' => 'array',
        'dados_depois' => 'array',
    ];

    /**
     * Registra uma ação de cadastro. $antes/$depois já devem vir sem
     * campos sensíveis (senha) — quem chama garante isso.
     */
    public static function registrar(
        string $acao,
        string $tabela,
        ?int $registroId,
        ?array $antes,
        ?array $depois,
        string $descricao,
    ): self {
        return self::create([
            // Sem sistema de login: mesmo fallback do projeto original.
            'usuario' => auth()->user()?->name ?? 'Administrador',
            'acao' => $acao,
            'tabela' => $tabela,
            'registro_id' => $registroId,
            'dados_antes' => $antes,
            'dados_depois' => $depois,
            'descricao' => $descricao,
        ]);
    }
}
