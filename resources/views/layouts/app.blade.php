<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="@yield('description', 'Painel de monitoramento de semáforo')" />
  <title>@yield('title', 'Semáforo')</title>

  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />
  @stack('styles')
</head>

<body>
  <div class="admin-shell">
    <div class="sidebar-backdrop" data-sidebar-close></div>

    <aside class="admin-sidebar" id="adminSidebar" aria-label="Navegação principal">
      <div class="sidebar-header">
        <a class="brand-mark" href="{{ route('home') }}" aria-label="Painel adminHMD">
          <span class="brand-icon"><i class="bi bi-grid-1x2-fill" aria-hidden="true"></i></span>
          <span class="brand-copy">
            <span class="brand-title">adminHMD</span>
            <span class="brand-subtitle">Painel administrativo</span>
          </span>
        </a>
      </div>

      <nav class="sidebar-nav">
        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
          <span class="nav-icon"><i class="bi bi-speedometer2" aria-hidden="true"></i></span>
          <span class="nav-text">Dashboard</span>
        </a>
        <button class="nav-link nav-accordion-toggle {{ request()->routeIs('simulador', 'monitoramento', 'semaforos.*') ? 'active' : '' }}"
          type="button" aria-expanded="true" aria-controls="semaforo-submenu">
          <span class="nav-icon"><i class="bi bi-stoplights" aria-hidden="true"></i></span>
          <span class="nav-text">Semáforo</span>
          <i class="bi bi-chevron-down nav-chevron" aria-hidden="true"></i>
        </button>
        <div class="nav-submenu" id="semaforo-submenu">
          <a class="nav-sublink {{ request()->routeIs('semaforos.*') ? 'active' : '' }}"
            href="{{ route('semaforos.index') }}">Cadastros</a>
          <a class="nav-sublink {{ request()->routeIs('simulador') ? 'active' : '' }}"
            href="{{ route('simulador') }}">Simulador</a>
          <a class="nav-sublink {{ request()->routeIs('monitoramento') ? 'active' : '' }}"
            href="{{ route('monitoramento') }}">Monitoramento</a>
        </div>
        <button class="nav-link nav-accordion-toggle {{ request()->routeIs('logs', 'logs-alteracao') ? 'active' : '' }}"
          type="button" aria-expanded="false" aria-controls="relatorios-submenu">
          <span class="nav-icon"><i class="bi bi-file-earmark-bar-graph" aria-hidden="true"></i></span>
          <span class="nav-text">Relatórios</span>
          <i class="bi bi-chevron-down nav-chevron" aria-hidden="true"></i>
        </button>
        <div class="nav-submenu" id="relatorios-submenu" hidden>
          <a class="nav-sublink {{ request()->routeIs('logs-alteracao') ? 'active' : '' }}"
            href="{{ route('logs-alteracao') }}">Logs de alterações</a>
          <a class="nav-sublink {{ request()->routeIs('logs') ? 'active' : '' }}" href="{{ route('logs') }}">Logs</a>
        </div>
        <a class="nav-link" href="{{ asset('semaforo/application/cadastros_usuarios/index.html') }}">
          <span class="nav-icon"><i class="bi bi-people" aria-hidden="true"></i></span>
          <span class="nav-text">Usuários</span>
        </a>
      </nav>

      <div class="sidebar-user">
        <img class="avatar-img avatar-md sidebar-user-avatar" src="{{ asset('assets/images/avatar/avatar.jpg') }}"
          alt="Administrador" />
        <strong>Administrador</strong>
        <small>Workspace ativo</small>
      </div>

      <div class="sidebar-footer">
        <span class="status-dot"></span>
        <span class="sidebar-footer-text">Sistema funcionando normalmente</span>
      </div>
    </aside>

    <div class="admin-main">
      <nav class="navbar admin-navbar navbar-expand bg-white">
        <div class="container-fluid px-3 px-lg-4">
          <button class="sidebar-toggle" type="button" data-sidebar-toggle aria-controls="adminSidebar"
            aria-expanded="true" aria-label="Alternar menu lateral">
            <span></span>
            <span></span>
            <span></span>
          </button>
          <a class="btn btn-outline-secondary btn-sm ms-3" href="{{ route('home') }}"><i class="bi bi-arrow-left"
              aria-hidden="true"></i> Voltar</a>
          <form class="d-none d-md-flex ms-3 flex-grow-1" role="search" onsubmit="return false">
            <input class="form-control search-input" type="search" placeholder="Buscar configurações"
              aria-label="Buscar configurações" @yield('busca_tabela') />
          </form>

          <div class="navbar-actions ms-auto">
            <button class="icon-button theme-toggle" type="button" data-theme-toggle aria-label="Alternar tema"
              title="Alternar tema">
              <i class="bi bi-moon-stars" data-theme-icon aria-hidden="true"></i>
            </button>

            <div class="dropdown">
              <button class="profile-button dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                <img class="avatar-img avatar-sm" src="{{ asset('assets/images/avatar/avatar.jpg') }}"
                  alt="Administrador" />
                <span class="profile-name d-none d-sm-inline">Administrador</span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li>
                  <a class="dropdown-item" href="{{ route('semaforos.index') }}">Cadastros</a>
                </li>
                <li>
                  <a class="dropdown-item" href="{{ route('simulador') }}">Simulador</a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </nav>

      <main class="dashboard-content">
        @yield('content')
      </main>
    </div>
  </div>

  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
  @stack('scripts')
</body>

</html>
