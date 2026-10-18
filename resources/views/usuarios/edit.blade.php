@extends('layouts.app')

@section('title', 'AdminHMD | Editar usuário')
@section('description', 'Cadastro de usuários')

@section('content')
  <div class="container-fluid px-3 px-lg-4 py-4">
    <div class="page-heading">
      <div class="page-heading-copy">
        <span class="page-icon"><i class="bi bi-pencil" aria-hidden="true"></i></span>
        <div>
          <p class="eyebrow mb-1">Administração</p>
          <h1 class="h3 mb-1">Editar usuário</h1>
          <p class="text-muted mb-0">
            Atualize os dados de <strong>{{ $usuario->name }}</strong>.
          </p>
        </div>
      </div>
    </div>

    @include('usuarios._form')
  </div>
@endsection
