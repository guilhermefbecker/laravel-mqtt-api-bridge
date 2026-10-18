<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logs_alteracao', function (Blueprint $table) {
            $table->id();
            $table->string('usuario')->default('Administrador');
            $table->string('acao', 20);
            $table->string('tabela', 40);
            $table->unsignedBigInteger('registro_id')->nullable();
            $table->json('dados_antes')->nullable();
            $table->json('dados_depois')->nullable();
            $table->string('descricao');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs_alteracao');
    }
};
