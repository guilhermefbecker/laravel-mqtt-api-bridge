@extends('layouts.app')

@section('title', 'SemÃ¡foro | Simulador')

@section('content')
<!-- =====================================================
             CONTEÚDO DA PÁGINA — limpo: só o semáforo + status.
             ===================================================== -->
          <div class="container-fluid px-3 px-lg-4 py-4">
            <div class="page-heading">
              <div class="page-heading-copy">
                <span class="page-icon"
                  ><i class="bi bi-stoplights" aria-hidden="true"></i
                ></span>
                <div>
                  <p class="eyebrow mb-1">Controle de tráfego</p>
                  <h1 class="h3 mb-1">Simulador de Semáforo</h1>
                  <p class="text-muted mb-0">
                    O semáforo reage apenas às mensagens MQTT enviadas pelo
                    MQTTX.
                  </p>
                </div>
              </div>
            </div>

            <section class="row g-3 mt-1 justify-content-center">
              <div class="col-12 col-sm-9 col-lg-7 col-xl-5">
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
                      <div
                        id="luzVermelho"
                        class="sf-luz sf-luz-vermelha"
                        aria-label="Luz vermelha"
                      ></div>
                      <div
                        id="luzAmarelo"
                        class="sf-luz sf-luz-amarela"
                        aria-label="Luz amarela"
                      ></div>
                      <div
                        id="luzVerde"
                        class="sf-luz sf-luz-verde"
                        aria-label="Luz verde"
                      ></div>
                    </div>
                  </div>

                  <div class="sf-status">
                    <span id="infoModo">Modo: parado</span>
                    <span id="infoComando">Último comando: —</span>
                  </div>
                </div>
              </div>
            </section>
          </div>
@endsection

@push('scripts')
  <script src="{{ asset('assets/js/semaforoMQTT.js') }}"></script>
@endpush
