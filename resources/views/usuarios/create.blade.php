@extends('layouts.app')

@section('title', 'AdminHMD | Novo usuário')
@section('description', 'Cadastro de usuários')

@section('content')
  <div class="container-fluid px-3 px-lg-4 py-4">
    <div class="page-heading">
      <div class="page-heading-copy">
        <span class="page-icon"><i class="bi bi-person-plus" aria-hidden="true"></i></span>
        <div>
          <p class="eyebrow mb-1">Administração</p>
          <h1 class="h3 mb-1">Novo usuário</h1>
          <p class="text-muted mb-0">
            Preencha os dados para cadastrar um usuário.
          </p>
        </div>
      </div>
    </div>

    @include('usuarios._form')
  </div>
@endsection
