@extends('layouts.app')

@section('title', 'AdminHMD | Logs')
@section('description', 'Logs recebidos do ESP32')

@push('styles')
  <style>
    /* Bolinha que representa a cor do semáforo na tabela. */
    .log-cor-dot {
      display: inline-block;
      width: 0.8rem;
      height: 0.8rem;
      border-radius: 50%;
      margin-right: 0.45rem;
      vertical-align: -0.05rem;
      box-shadow: 0 0 0 1px rgba(15, 23, 42, 0.18);
    }

    /* Bloco do payload bruto no modal. */
    .log-payload {
      max-height: 320px;
      overflow: auto;
      margin: 0;
      padding: 0.85rem 1rem;
      border: 1px solid var(--admin-border);
      border-radius: 8px;
      background: var(--admin-surface-soft);
      color: var(--admin-text);
      font-size: 0.85rem;
      white-space: pre-wrap;
      word-break: break-word;
    }

    .log-detalhe-rotulo {
      display: block;
      color: var(--admin-muted);
      font-size: 0.72rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.02rem;
    }
  </style>
@endpush


@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
          <div class="page-heading">
            <div class="page-heading-copy">
              <span class="page-icon"><i class="bi bi-journal-text" aria-hidden="true"></i></span>
              <div>
                <p class="eyebrow mb-1">Relatórios</p>
                <h1 class="h3 mb-1">Logs</h1>
                <p class="text-muted mb-0">Registros recebidos do ESP32 via MQTT, consultados na API.</p>
              </div>
            </div>
            <div class="heading-actions">
              <div class="form-check form-switch d-flex align-items-center gap-2 mb-0">
                <input class="form-check-input mt-0" type="checkbox" role="switch" id="switchAuto" checked />
                <label class="form-check-label text-nowrap" for="switchAuto">Atualizar automaticamente</label>
              </div>
              <button class="btn btn-outline-secondary btn-sm" type="button" id="btnAtualizar">
                <i class="bi bi-arrow-clockwise" aria-hidden="true"></i> Atualizar
              </button>
            </div>
          </div>

          <section class="panel mt-1">
            <div class="panel-header">
              <div>
                <h2 class="h5 mb-1 section-title">
                  <i class="bi bi-funnel" aria-hidden="true"></i><span>Filtros</span>
                </h2>
                <p class="text-muted mb-0">Os filtros são aplicados na consulta à API.</p>
              </div>
            </div>

            <form id="formFiltros" class="row g-3 align-items-end" novalidate>
              <div class="col-12 col-md-6 col-lg-2">
                <label class="form-label" for="filtroDispositivo">Dispositivo</label>
                <input type="text" class="form-control" id="filtroDispositivo" placeholder="Ex.: semaforo" />
              </div>
              <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label" for="filtroCor">Cor</label>
                <select class="form-select" id="filtroCor">
                  <option value="">Todas</option>
                  <option value="vermelho">Vermelho</option>
                  <option value="amarelo">Amarelo</option>
                  <option value="verde">Verde</option>
                </select>
              </div>
              <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label" for="filtroEstado">Estado</label>
                <select class="form-select" id="filtroEstado">
                  <option value="">Todos</option>
                  <option value="true">Ligado</option>
                  <option value="false">Desligado</option>
                </select>
              </div>
              <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label" for="filtroCiclo">Ciclo</label>
                <select class="form-select" id="filtroCiclo">
                  <option value="">Todos</option>
                  <option value="true">Sim</option>
                  <option value="false">Não</option>
                </select>
              </div>
              <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label" for="filtroDe">Data inicial</label>
                <input type="date" class="form-control" id="filtroDe" />
              </div>
              <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label" for="filtroAte">Data final</label>
                <input type="date" class="form-control" id="filtroAte" />
              </div>
              <div class="col-12 d-flex flex-wrap gap-2 justify-content-end">
                <button class="btn btn-primary btn-sm" type="submit">
                  <i class="bi bi-search" aria-hidden="true"></i> Filtrar
                </button>
                <button class="btn btn-outline-secondary btn-sm" type="button" id="btnLimpar">
                  <i class="bi bi-x-lg" aria-hidden="true"></i> Limpar
                </button>
              </div>
            </form>
          </section>

          <section class="panel mt-3">
            <div class="panel-header">
              <div>
                <h2 class="h5 mb-1 section-title">
                  <i class="bi bi-table" aria-hidden="true"></i><span>Registros</span>
                </h2>
                <p class="text-muted mb-0" id="infoPaginacao">Carregando...</p>
              </div>
            </div>

            <div class="table-responsive" id="tabelaContainer">
              <table class="table align-middle mb-0" id="tabelaLogs">
                <thead>
                  <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Dispositivo</th>
                    <th scope="col">Cor</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Ciclo</th>
                    <th scope="col">Data e hora</th>
                    <th scope="col" class="text-end">Ações</th>
                  </tr>
                </thead>
                <tbody id="tabelaLogsCorpo"></tbody>
              </table>
            </div>

            <div class="text-center py-5 d-none" id="estadoCarregando">
              <div class="spinner-border text-primary" role="status" aria-hidden="true"></div>
              <p class="text-muted mt-3 mb-0">Carregando logs...</p>
            </div>

            <div class="text-center py-5 d-none" id="estadoVazio">
              <i class="bi bi-inbox fs-2 text-muted" aria-hidden="true"></i>
              <p class="fw-semibold mt-3 mb-1">Nenhum log encontrado</p>
              <p class="text-muted small mb-0">Ajuste os filtros ou aguarde novas mensagens do dispositivo.</p>
            </div>

            <div class="text-center py-5 d-none" id="estadoErro">
              <i class="bi bi-wifi-off fs-2 text-danger" aria-hidden="true"></i>
              <p class="fw-semibold mt-3 mb-1">Não foi possível carregar os logs</p>
              <p class="text-muted small mb-3" id="mensagemErro">Verifique se a API está ligada.</p>
              <button class="btn btn-outline-primary btn-sm" type="button" id="btnTentarNovamente">
                <i class="bi bi-arrow-clockwise" aria-hidden="true"></i> Tentar novamente
              </button>
            </div>

            <nav class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3 d-none"
              id="paginacaoContainer" aria-label="Paginação dos logs">
              <ul class="pagination pagination-sm mb-0" id="paginacaoLogs"></ul>
            </nav>
          </section>
        </div>

        <div class="modal fade" id="modalDetalhes" tabindex="-1" aria-labelledby="modalDetalhesLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
              <div class="modal-header">
                <h2 class="modal-title h5" id="modalDetalhesLabel">
                  <i class="bi bi-journal-text" aria-hidden="true"></i> Detalhes do log
                </h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
              </div>
              <div class="modal-body">
                <div class="row g-3">
                  <div class="col-6 col-md-3">
                    <span class="log-detalhe-rotulo">ID</span>
                    <strong id="detalheId">—</strong>
                  </div>
                  <div class="col-6 col-md-5">
                    <span class="log-detalhe-rotulo">Dispositivo</span>
                    <strong id="detalheDispositivo">—</strong>
                  </div>
                  <div class="col-6 col-md-4">
                    <span class="log-detalhe-rotulo">Tópico</span>
                    <strong id="detalheTopico">—</strong>
                  </div>
                  <div class="col-6 col-md-3">
                    <span class="log-detalhe-rotulo">Cor</span>
                    <strong id="detalheCor">—</strong>
                  </div>
                  <div class="col-6 col-md-3">
                    <span class="log-detalhe-rotulo">Estado</span>
                    <strong id="detalheEstado">—</strong>
                  </div>
                  <div class="col-6 col-md-3">
                    <span class="log-detalhe-rotulo">Ciclo</span>
                    <strong id="detalheCiclo">—</strong>
                  </div>
                  <div class="col-6 col-md-3">
                    <span class="log-detalhe-rotulo">Porta</span>
                    <strong id="detalhePorta">—</strong>
                  </div>
                  <div class="col-12 col-md-6">
                    <span class="log-detalhe-rotulo">Comando</span>
                    <strong id="detalheComando">—</strong>
                  </div>
                  <div class="col-12 col-md-6">
                    <span class="log-detalhe-rotulo">Recebido em</span>
                    <strong id="detalheRecebido">—</strong>
                  </div>
                </div>

                <hr />

                <span class="log-detalhe-rotulo mb-1">Payload bruto</span>
                <pre class="log-payload" id="detalhePayload">—</pre>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
              </div>
            </div>
          </div>
        </div>
@endsection

@push('scripts')
  <script src="{{ asset('assets/js/logs.js') }}"></script>
@endpush
