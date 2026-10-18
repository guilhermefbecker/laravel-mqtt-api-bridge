@extends('layouts.app')

@section('title', 'Semáforo | Simulador')

@section('content')
  <div class="container-fluid px-3 px-lg-4 py-4">
    <div class="page-heading">
      <div class="page-heading-copy">
        <span class="page-icon"><i class="bi bi-stoplights" aria-hidden="true"></i></span>
        <div>
          <p class="eyebrow mb-1">Controle de tráfego</p>
          <h1 class="h3 mb-1">Simulador de Semáforo</h1>
          <p class="text-muted mb-0">
            Envie comandos pelo painel abaixo ou publique mensagens MQTT pelo
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
    </section>

    <section class="row g-3 mt-1 justify-content-center">
      <div class="col-12 col-sm-9 col-lg-7 col-xl-5">
        <div class="panel p-4">
          <h2 class="h6 section-title mb-3">
            <i class="bi bi-send" aria-hidden="true"></i>
            <span>Comandos</span>
          </h2>
          <p class="text-muted small mb-3">
            Os comandos são enviados à API e publicados no tópico MQTT
            <code>semaforo/comando</code>.
          </p>

          <div id="feedbackComando" class="alert d-none" role="status" aria-live="polite"></div>

          <div class="d-flex flex-wrap gap-2 mb-3">
            <button class="btn btn-outline-danger" type="button" data-comando="vermelho">
              <i class="bi bi-circle-fill me-1" aria-hidden="true"></i> Vermelho
            </button>
            <button class="btn btn-outline-warning" type="button" data-comando="amarelo">
              <i class="bi bi-circle-fill me-1" aria-hidden="true"></i> Amarelo
            </button>
            <button class="btn btn-outline-success" type="button" data-comando="verde">
              <i class="bi bi-circle-fill me-1" aria-hidden="true"></i> Verde
            </button>
          </div>

          <div class="form-check form-switch">
            <input class="form-check-input" id="comandoCiclo" type="checkbox" />
            <label class="form-check-label" for="comandoCiclo">Ciclo ligado</label>
          </div>
        </div>
      </div>
    </section>
  </div>
@endsection

@push('scripts')
  <script src="{{ asset('assets/js/semaforoMQTT.js') }}"></script>
  <script>
    (function() {
      // Estado simulado dos botões (liga/desliga por cor).
      var estadoPorCor = { vermelho: false, amarelo: false, verde: false };
      var feedback = document.getElementById('feedbackComando');

      function mostrarFeedback(mensagem, tipo) {
        feedback.className = 'alert alert-' + tipo;
        feedback.textContent = mensagem;
        window.setTimeout(function() {
          feedback.classList.add('d-none');
        }, 3000);
      }

      async function enviarComando(cor, estado) {
        var ciclo = document.getElementById('comandoCiclo').checked;
        var payload = {
          dispositivo: 'simulador',
          cor: cor,
          estado: estado,
          ciclo: ciclo,
        };

        try {
          var resposta = await fetch('/api/comandos', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
          });
          var corpo = await resposta.json();

          if (!resposta.ok) {
            var mensagens = corpo.erros ? Object.values(corpo.erros).flat().join(' ') : (corpo.mensagem || 'Falha ao enviar comando.');
            mostrarFeedback(mensagens, 'danger');
            return;
          }

          mostrarFeedback('Comando enviado: ' + cor + ' = ' + (estado ? 'ligado' : 'desligado'), 'success');
          document.getElementById('infoComando').textContent =
            'Comando enviado: ' + cor + ' = ' + (estado ? 'ligado' : 'desligado');
        } catch (erro) {
          mostrarFeedback('Falha ao enviar comando.', 'danger');
        }
      }

      document.querySelectorAll('[data-comando]').forEach(function(botao) {
        botao.addEventListener('click', function() {
          var cor = botao.dataset.comando;
          estadoPorCor[cor] = !estadoPorCor[cor];
          enviarComando(cor, estadoPorCor[cor]);
        });
      });
    })();
  </script>
@endpush
