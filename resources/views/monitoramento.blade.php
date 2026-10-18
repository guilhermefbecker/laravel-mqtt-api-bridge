@extends('layouts.app')

@section('title', 'AdminHMD | Monitoramento')
@section('description', 'Monitoramento de semÃ¡foro')

@section('content')
<!-- =====================================================
           CONTEÚDO DA PÁGINA — monitoramento ao vivo do semáforo.
           ===================================================== -->
        <div class="container-fluid px-3 px-lg-4 py-4" id="monitoramento">
          <div class="page-heading">
            <div class="page-heading-copy">
              <span class="page-icon"><i class="bi bi-activity" aria-hidden="true"></i></span>
              <div>
                <p class="eyebrow mb-1">Controle de tráfego</p>
                <h1 class="h3 mb-1">Monitoramento de Semáforo</h1>
                <p class="text-muted mb-0">
                  Estado ao vivo do semáforo, atualizado pelas mensagens MQTT recebidas do MQTTX.
                </p>
              </div>
            </div>
          </div>

          <section class="row g-3 mt-1">
            <div class="col-12 col-md-6 col-lg-5 col-xl-4">
              <div class="panel sf-panel">
                <div class="panel-header">
                  <div>
                    <h2 class="h6 mb-1">
                      <i class="bi bi-stoplights" aria-hidden="true"></i>
                      <span>Semáforo</span>
                    </h2>
                  </div>
                </div>

                <div class="sf-visual">
                  <div class="sf-poste"></div>
                  <div class="sf-caixa">
                    <div id="luzVermelho" class="sf-luz sf-luz-vermelha" aria-label="Luz vermelha"></div>
                    <div id="luzAmarelo" class="sf-luz sf-luz-amarela" aria-label="Luz amarela"></div>
                    <div id="luzVerde" class="sf-luz sf-luz-verde" aria-label="Luz verde"></div>
                  </div>
                </div>

                <div class="sf-status">
                  <span id="infoModo">Modo: parado</span>
                  <span id="infoComando">Último comando: —</span>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-6 col-lg-7 col-xl-8">
              <div class="panel h-100">
                <div class="panel-header">
                  <div>
                    <h2 class="h5 mb-1 section-title">
                      <i class="bi bi-activity" aria-hidden="true"></i><span>Status em tempo real</span>
                    </h2>
                    <p class="text-muted mb-0">
                      Telemetria recebida no tópico MQTT "semaforo/estado".
                    </p>
                  </div>
                </div>

                <div class="activity-list monitor-list">
                  <div class="activity-item">
                    <span class="activity-dot bg-success"></span>
                    <div>
                      <p class="mb-1 fw-semibold">Conexão com o broker</p>
                      <p class="text-muted small mb-0" id="statusConexao">Conectando...</p>
                    </div>
                  </div>
                  <div class="activity-item">
                    <span class="activity-dot bg-primary"></span>
                    <div>
                      <p class="mb-1 fw-semibold">Última mensagem recebida</p>
                      <p class="text-muted small mb-0" id="infoBruto">—</p>
                    </div>
                  </div>
                  <div class="activity-item">
                    <span class="activity-dot bg-warning"></span>
                    <div>
                      <p class="mb-1 fw-semibold">Diagnóstico</p>
                      <p class="text-muted small mb-0" id="infoReload">Recarregamentos: 0</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>
@endsection

@push('scripts')
  <script src="{{ asset('assets/js/semaforoMQTT.js') }}"></script>
@endpush
