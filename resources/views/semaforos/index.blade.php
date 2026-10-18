@extends('layouts.app')

@section('title', 'Semáforo | Cadastros')
@section('description', 'Cadastro de configurações de semáforo')
@section('busca_tabela', 'data-table-search="semaforosTable"')

@php
    function formatarSegundos($ms)
    {
        $valor = rtrim(number_format($ms / 1000, 1, ',', '.'), '0');

        return rtrim($valor, ',') . 's';
    }
@endphp

@section('content')
  <div class="container-fluid px-3 px-lg-4 py-4">
    <div class="page-heading">
      <div class="page-heading-copy">
        <span class="page-icon"><i class="bi bi-stoplights" aria-hidden="true"></i></span>
        <div>
          <p class="eyebrow mb-1">Gestão operacional</p>
          <h1 class="h3 mb-1">Cadastros de semáforos</h1>
          <p class="text-muted mb-0">
            Crie, consulte, atualize e remova configurações de ciclo.
          </p>
        </div>
      </div>
    </div>

    <div id="feedback" class="alert {{ session('sucesso') ? 'alert-success' : 'd-none' }}" role="status"
      aria-live="polite">{{ session('sucesso') }}</div>

    <section class="row g-3 mt-1 cadastro-metrics" aria-label="Resumo dos cadastros">
      <div class="col-12 col-sm-6">
        <article class="metric-card metric-primary">
          <div class="metric-top">
            <span class="metric-label">Cadastros</span><span class="metric-icon"><i class="bi bi-collection"
                aria-hidden="true"></i></span>
          </div>
          <div class="metric-value" id="totalRecords">{{ $total }}</div>
          <div class="metric-meta">
            <span>configurações salvas</span>
          </div>
        </article>
      </div>
      <div class="col-12 col-sm-6">
        <article class="metric-card metric-success">
          <div class="metric-top">
            <span class="metric-label">Ativos</span><span class="metric-icon"><i class="bi bi-check-circle"
                aria-hidden="true"></i></span>
          </div>
          <div class="metric-value" id="activeRecords">{{ $ativos }}</div>
          <div class="metric-meta"><span>prontos para uso</span></div>
        </article>
      </div>
    </section>

    <section class="row g-3 mt-1" id="cadastro-form-section">
      <div class="heading-actions cadastro-actions">
        <a class="btn btn-primary btn-sm" href="{{ route('semaforos.create') }}" data-new-record><i class="bi bi-plus-lg"
            aria-hidden="true"></i> Novo
          cadastro</a>
      </div>

      <div class="col-12 col-xl-12" id="registros">
        <div class="panel h-100">
          <div class="panel-header">
            <div>
              <h2 class="h5 mb-1 section-title">
                <i class="bi bi-table" aria-hidden="true"></i><span>Configurações cadastradas</span>
              </h2>
              <p class="text-muted mb-0">
                <span id="tableCount">{{ $semaforos->count() }}</span> registros encontrados.
              </p>
            </div>
            <form method="POST" action="{{ route('semaforos.limpar') }}"
              onsubmit="return confirm('Excluir todos os cadastros?')">
              @csrf
              @method('DELETE')
              <button class="btn btn-outline-secondary btn-sm" type="submit">
                <i class="bi bi-trash3" aria-hidden="true"></i> Limpar
                todos
              </button>
            </form>
          </div>
          <div class="table-responsive">
            <table class="table align-middle mb-0" id="semaforosTable">
              <thead>
                <tr>
                  <th scope="col">Semáforo</th>
                  <th scope="col">Ciclo</th>
                  <th scope="col">Status</th>
                  <th scope="col" class="text-end">Ações</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($semaforos as $semaforo)
                  <tr>
                    <td>
                      <strong>{{ $semaforo->nome }}</strong>
                      <small class="d-block text-muted">Atualizado agora</small>
                    </td>
                    <td>
                      <span class="fw-semibold">{{ formatarSegundos($semaforo->ciclo) }}</span>
                      <small class="d-block text-muted">R {{ formatarSegundos($semaforo->tempo_vermelho) }} / A
                        {{ formatarSegundos($semaforo->tempo_amarelo) }} / V
                        {{ formatarSegundos($semaforo->tempo_verde) }}</small>
                    </td>
                    <td>
                      <span
                        class="badge text-bg-{{ $semaforo->ativo ? 'success' : 'secondary' }}">{{ $semaforo->ativo ? 'Ativo' : 'Inativo' }}</span>
                    </td>
                    <td class="text-end">
                      <div class="btn-group btn-group-sm" role="group">
                        <a class="btn btn-outline-primary" href="{{ route('semaforos.edit', $semaforo) }}"
                          aria-label="Editar {{ $semaforo->nome }}" title="Editar"><i class="bi bi-pencil"
                            aria-hidden="true"></i></a>
                        <button class="btn btn-outline-danger" type="button" data-delete="{{ $semaforo->id }}"
                          data-nome="{{ $semaforo->nome }}" aria-label="Excluir {{ $semaforo->nome }}"
                          title="Excluir"><i class="bi bi-trash3" aria-hidden="true"></i></button>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="empty-state {{ $semaforos->isEmpty() ? '' : 'd-none' }}" id="emptyState">
            <i class="bi bi-inbox" aria-hidden="true"></i>
            <p class="mb-1 fw-semibold">Nenhum cadastro encontrado</p>
            <p class="text-muted small mb-0">
              Crie uma configuração para começar.
            </p>
          </div>
        </div>
      </div>
    </section>
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h2 class="modal-title h5" id="deleteModalLabel">
              Excluir semáforo?
            </h2>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body">
            Tem certeza de que deseja excluir
            <strong id="deleteRecordName"></strong>?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
              Cancelar</button>
            <form id="formExcluir" method="POST" action="">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-danger" id="confirmDelete">
                Excluir
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    (function() {
      // Feedback some sozinho apos 3s (comportamento do original).
      var feedback = document.getElementById('feedback');
      if (feedback && !feedback.classList.contains('d-none')) {
        window.setTimeout(function() {
          feedback.classList.add('d-none');
        }, 3000);
      }

      // Modal de exclusao: preenche nome e acao do form DELETE.
      var formExcluir = document.getElementById('formExcluir');
      document.querySelectorAll('[data-delete]').forEach(function(botao) {
        botao.addEventListener('click', function() {
          document.getElementById('deleteRecordName').textContent = botao.dataset.nome;
          formExcluir.action = '{{ route('semaforos.index') }}/' + botao.dataset.delete;
          bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteModal')).show();
        });
      });

      // Registra a alteracao no log (mesma chamada do original).
      @if (session('log'))
        if (typeof window.registrarLog === 'function') {
          window.registrarLog(@json(session('log')));
        }
      @endif
    })();
  </script>
@endpush
