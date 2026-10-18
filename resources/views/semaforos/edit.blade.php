@extends('layouts.app')

@section('title', 'Semáforo | Editar cadastro')
@section('description', 'Editar configuração de semáforo')

@section('content')
  <div class="container-fluid px-3 px-lg-4 py-4">
    <div class="page-heading">
      <div class="page-heading-copy">
        <span class="page-icon"><i class="bi bi-stoplights" aria-hidden="true"></i></span>
        <div>
          <p class="eyebrow mb-1">Gestão operacional</p>
          <h1 class="h3 mb-1">Editar cadastro</h1>
          <p class="text-muted mb-0">
            Atualize as configurações do semáforo.
          </p>
        </div>
      </div>
    </div>

    @if (session('sucesso'))
      <div class="alert alert-success" role="status" aria-live="polite">{{ session('sucesso') }}</div>
    @endif

    <section class="row g-3 mt-1" id="cadastro-form-section">
      <div class="col-12">
        <div class="panel h-100">
          <div class="panel-header">
            <div>
              <h2 class="h5 mb-1 section-title">
                <i class="bi bi-pencil-square" aria-hidden="true"></i><span>Editar cadastro</span>
              </h2>
              <p class="text-muted mb-0">Defina os tempos do ciclo.</p>
            </div>
          </div>

          @include('semaforos._form', ['semaforo' => $semaforo])
        </div>
      </div>
    </section>
  </div>
@endsection
