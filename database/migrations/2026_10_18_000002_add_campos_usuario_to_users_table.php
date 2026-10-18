<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('sobrenome', 80)->nullable()->after('name');
            $table->string('cpf', 14)->nullable()->after('sobrenome');
            $table->date('nascimento')->nullable()->after('cpf');
            $table->string('celular', 15)->nullable()->after('nascimento');
            $table->string('cep', 9)->nullable()->after('celular');
            $table->string('logradouro', 120)->nullable()->after('cep');
            $table->string('numero', 20)->nullable()->after('logradouro');
            $table->string('complemento', 80)->nullable()->after('numero');
            $table->string('bairro', 80)->nullable()->after('complemento');
            $table->string('cidade', 80)->nullable()->after('bairro');
            $table->string('estado', 2)->nullable()->after('cidade');
            $table->string('perfil')->default('Operador')->after('estado');
            $table->boolean('ativo')->default(true)->after('perfil');
        });

        // Admin padrão que existia no localStorage (admin@adminhmd.com).
        DB::table('users')->insert([
            'name' => 'Administrador',
            'sobrenome' => '',
            'email' => 'admin@adminhmd.com',
            'password' => Hash::make('password'),
            'perfil' => 'Administrador',
            'ativo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'sobrenome', 'cpf', 'nascimento', 'celular', 'cep', 'logradouro',
                'numero', 'complemento', 'bairro', 'cidade', 'estado', 'perfil', 'ativo',
            ]);
        });
    }
};
