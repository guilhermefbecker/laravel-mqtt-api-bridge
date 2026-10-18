@extends('layouts.app')

@section('title', 'AdminHMD | Logs de alteração')
@section('description', 'Logs de alteração do sistema')

@section('content')
  <div class="container-fluid px-3 px-lg-4 py-4">
    <div class="page-heading">
      <div class="page-heading-copy">
        <span class="page-icon"><i class="bi bi-clock-history" aria-hidden="true"></i></span>
        <div>
          <p class="eyebrow mb-1">Relatórios</p>
          <h1 class="h3 mb-1">Logs de alteração</h1>
          <p class="text-muted mb-0">Histórico de ações realizadas no sistema.</p>
        </div>
      </div>
      <form method="POST" action="{{ route('logs-alteracao.limpar') }}"
        onsubmit="return confirm('Deseja limpar todo o histórico de logs?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-outline-secondary btn-sm" type="submit">
          <i class="bi bi-trash3" aria-hidden="true"></i> Limpar logs
        </button>
      </form>
    </div>

    <div id="feedback" class="alert {{ session('sucesso') ? 'alert-success' : 'd-none' }}" role="status"
      aria-live="polite">{{ session('sucesso') }}</div>

    <section class="panel mt-3">
      <div class="panel-header">
        <div>
          <h2 class="h5 mb-1 section-title">
            <i class="bi bi-list-ul" aria-hidden="true"></i><span>Atividades recentes</span>
          </h2>
          <p class="text-muted mb-0">Cada registro mostra horário, usuário e a alteração realizada.</p>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead>
            <tr>
              <th scope="col">Horário</th>
              <th scope="col">Usuário</th>
              <th scope="col">Alteração</th>
              <th scope="col">Detalhes</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($registros as $registro)
              <tr>
                <td>
                  <span
                    class="fw-semibold">{{ $registro->created_at->format('d/m/Y H:i:s') }}</span>
                </td>
                <td>
                  <span class="text-primary">{{ $registro->usuario }}</span>
                </td>
                <td>{{ $registro->descricao }}</td>
                <td>
                  @if ($registro->dados_antes || $registro->dados_depois)
                    <button class="btn btn-outline-secondary btn-sm" type="button"
                      data-detalhes="{{ $registro->id }}" aria-label="Ver detalhes" title="Ver detalhes">
                      <i class="bi bi-eye" aria-hidden="true"></i>
                    </button>
                  @else
                    <span class="text-muted small">—</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center text-muted py-4">
                  Nenhuma alteração registrada. Ações realizadas aparecerão aqui.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="empty-state {{ $registros->isEmpty() ? '' : 'd-none' }}" id="emptyLogsState">
        <i class="bi bi-info-circle" aria-hidden="true"></i>
        <p class="mb-1 fw-semibold">Nenhuma alteração registrada</p>
        <p class="text-muted small mb-0">Ações realizadas aparecerão aqui.</p>
      </div>
    </section>

    <div class="modal fade" id="detalhesModal" tabindex="-1" aria-labelledby="detalhesModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h2 class="modal-title h5" id="detalhesModalLabel">Detalhes da alteração</h2>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-12 col-md-6">
                <h3 class="h6">Dados antes</h3>
                <pre id="detalhesAntes" class="bg-light border rounded p-2 small mb-0"
                  style="white-space: pre-wrap"></pre>
              </div>
              <div class="col-12 col-md-6">
                <h3 class="h6">Dados depois</h3>
                <pre id="detalhesDepois" class="bg-light border rounded p-2 small mb-0"
                  style="white-space: pre-wrap"></pre>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
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

      // Modal de detalhes: mostra dados antes/depois (JSON do banco).
      var dadosPorId = @json($detalhes);

      document.querySelectorAll('[data-detalhes]').forEach(function(botao) {
        botao.addEventListener('click', function() {
          var dados = dadosPorId[botao.dataset.detalhes] || {};
          document.getElementById('detalhesAntes').textContent = dados.antes
            ? JSON.stringify(dados.antes, null, 2) : '(não havia dados)';
          document.getElementById('detalhesDepois').textContent = dados.depois
            ? JSON.stringify(dados.depois, null, 2) : '(registro removido)';
          bootstrap.Modal.getOrCreateInstance(document.getElementById('detalhesModal')).show();
        });
      });
    })();
  </script>
@endpush
