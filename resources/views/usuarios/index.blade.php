@extends('layouts.app')

@section('title', 'AdminHMD | Usuários')
@section('description', 'Cadastro de usuários')

@section('content')
  <div class="container-fluid px-3 px-lg-4 py-4">
    <div class="page-heading">
      <div class="page-heading-copy">
        <span class="page-icon"><i class="bi bi-people" aria-hidden="true"></i></span>
        <div>
          <p class="eyebrow mb-1">Administração</p>
          <h1 class="h3 mb-1">Usuários</h1>
          <p class="text-muted mb-0">
            Crie, consulte, atualize e remova usuários do sistema.
          </p>
        </div>
      </div>
    </div>

    <div id="feedback" class="alert {{ session('sucesso') ? 'alert-success' : 'd-none' }}" role="status"
      aria-live="polite">{{ session('sucesso') }}</div>

    <section class="row g-3 mt-1 cadastro-metrics" aria-label="Resumo dos usuários">
      <div class="col-12 col-sm-6">
        <article class="metric-card metric-primary">
          <div class="metric-top">
            <span class="metric-label">Usuários</span><span class="metric-icon"><i class="bi bi-people"
                aria-hidden="true"></i></span>
          </div>
          <div class="metric-value" id="totalUsers">{{ $total }}</div>
          <div class="metric-meta"><span>cadastrados</span></div>
        </article>
      </div>
      <div class="col-12 col-sm-6">
        <article class="metric-card metric-success">
          <div class="metric-top">
            <span class="metric-label">Ativos</span><span class="metric-icon"><i class="bi bi-person-check"
                aria-hidden="true"></i></span>
          </div>
          <div class="metric-value" id="activeUsers">{{ $ativos }}</div>
          <div class="metric-meta">
            <span>com acesso liberado</span>
          </div>
        </article>
      </div>
    </section>

    <section class="row g-3 mt-1">
      <div class="heading-actions cadastro-actions">
        <a class="btn btn-primary btn-sm" href="{{ route('usuarios.create') }}"><i class="bi bi-person-plus"
            aria-hidden="true"></i> Novo
          usuário</a>
      </div>
      <div class="col-12">
        <div class="panel h-100">
          <div class="panel-header">
            <div>
              <h2 class="h5 mb-1 section-title">
                <i class="bi bi-table" aria-hidden="true"></i><span>Usuários
                  cadastrados</span>
              </h2>
              <p class="text-muted mb-0">
                <span id="tableCount">{{ $total }}</span> usuários encontrados.
              </p>
            </div>
            <form method="POST" action="{{ route('usuarios.limpar') }}"
              onsubmit="return confirm('Excluir todos os usuários?')">
              @csrf
              @method('DELETE')
              <button class="btn btn-outline-secondary btn-sm" type="submit">
                <i class="bi bi-trash3" aria-hidden="true"></i> Limpar
                todos
              </button>
            </form>
          </div>
          <div class="table-responsive">
            <table class="table align-middle mb-0" id="usuariosTable">
              <thead>
                <tr>
                  <th>Usuário</th>
                  <th>E-mail</th>
                  <th>Perfil</th>
                  <th>Status</th>
                  <th class="text-end">Ações</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($usuarios as $usuario)
                  <tr>
                    <td>
                      <strong>{{ trim($usuario->name . ' ' . $usuario->sobrenome) }}</strong>
                    </td>
                    <td>{{ $usuario->email }}</td>
                    <td>{{ $usuario->perfil }}</td>
                    <td>
                      <span
                        class="badge text-bg-{{ $usuario->ativo ? 'success' : 'secondary' }}">{{ $usuario->ativo ? 'Ativo' : 'Inativo' }}</span>
                    </td>
                    <td class="text-end">
                      <div class="btn-group btn-group-sm" role="group">
                        <a class="btn btn-outline-primary" href="{{ route('usuarios.edit', $usuario) }}"
                          aria-label="Editar {{ $usuario->name }}" title="Editar"><i class="bi bi-pencil"
                            aria-hidden="true"></i></a>
                        <button class="btn btn-outline-danger" type="button" data-delete="{{ $usuario->id }}"
                          data-nome="{{ $usuario->name }}" aria-label="Excluir {{ $usuario->name }}"
                          title="Excluir"><i class="bi bi-trash3" aria-hidden="true"></i></button>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="empty-state {{ $usuarios->isEmpty() ? '' : 'd-none' }}" id="emptyState">
            <i class="bi bi-inbox" aria-hidden="true"></i>
            <p class="mb-1 fw-semibold">Nenhum usuário encontrado</p>
            <p class="text-muted small mb-0">
              Crie um usuário para começar.
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
              Excluir usuário?
            </h2>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body">
            Tem certeza de que deseja excluir
            <strong id="deleteUserName"></strong>?
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
          document.getElementById('deleteUserName').textContent = botao.dataset.nome;
          formExcluir.action = '{{ route('usuarios.index') }}/' + botao.dataset.delete;
          bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteModal')).show();
        });
      });
    })();
  </script>
@endpush
