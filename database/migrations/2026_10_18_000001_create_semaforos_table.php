<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semaforos', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 60);
            $table->unsignedInteger('tempo_vermelho');
            $table->unsignedInteger('tempo_amarelo');
            $table->unsignedInteger('tempo_verde');
            $table->string('latitude_longitude')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        // Registro padrão que existia no localStorage (Avenida Central).
        DB::table('semaforos')->insert([
            'nome' => 'Avenida Central',
            'tempo_vermelho' => 5000,
            'tempo_amarelo' => 2000,
            'tempo_verde' => 6000,
            'latitude_longitude' => null,
            'ativo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('semaforos');
    }
};
