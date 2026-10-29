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

    <section class="row g-4 mt-1 align-items-start">
      <div class="col-12 col-lg-5">
        <div class="panel sf-panel h-100 text-center">
          <div class="panel-header justify-content-center">
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

          <div class="sf-status justify-content-center">
            <span id="infoModo">Modo: parado</span>
            <span id="infoComando">Último comando: —</span>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-7">
        <div class="panel p-4 h-100">
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
            <button class="btn btn-outline-danger" type="button" data-payload='{"cor":"vermelho","estado":"vermelho"}'>
              <i class="bi bi-circle-fill me-1" aria-hidden="true"></i> Vermelho
            </button>
            <button class="btn btn-outline-warning" type="button" data-payload='{"cor":"amarelo","estado":"amarelo"}'>
              <i class="bi bi-circle-fill me-1" aria-hidden="true"></i> Amarelo
            </button>
            <button class="btn btn-outline-success" type="button" data-payload='{"cor":"verde","estado":"verde"}'>
              <i class="bi bi-circle-fill me-1" aria-hidden="true"></i> Verde
            </button>
            <button class="btn btn-outline-secondary" type="button" data-payload='{"estado":"intermitente"}'>
              <i class="bi bi-lightning-charge me-1" aria-hidden="true"></i> Intermitente
            </button>
            <button class="btn btn-outline-dark" type="button" data-payload='{"estado":"desligado"}'>
              <i class="bi bi-power me-1" aria-hidden="true"></i> Desligar
            </button>
          </div>

          <h3 class="h6 section-title mb-3">
            <i class="bi bi-arrow-repeat" aria-hidden="true"></i>
            <span>Ciclo</span>
          </h3>

          <div class="row g-2 mb-3">
            <div class="col-12 col-sm-4">
              <label class="form-label" for="tempoVermelho">Tempo vermelho (s)</label>
              <input type="number" class="form-control" id="tempoVermelho" min="1" step="1"
                placeholder="segundos" />
            </div>
            <div class="col-12 col-sm-4">
              <label class="form-label" for="tempoAmarelo">Tempo amarelo (s)</label>
              <input type="number" class="form-control" id="tempoAmarelo" min="1" step="1"
                placeholder="segundos" />
            </div>
            <div class="col-12 col-sm-4">
              <label class="form-label" for="tempoVerde">Tempo verde (s)</label>
              <input type="number" class="form-control" id="tempoVerde" min="1" step="1"
                placeholder="segundos" />
            </div>
          </div>

          <button class="btn btn-primary" type="button" id="btnCiclo">
            <i class="bi bi-play-fill" aria-hidden="true"></i> Ligar ciclo
          </button>
        </div>
      </div>
    </section>
  </div>
@endsection

@push('scripts')
  <script src="{{ asset('assets/js/semaforo-api.js') }}"></script>
  <script>
    (function() {
      var feedback = document.getElementById('feedbackComando');

      function mostrarFeedback(mensagem, tipo) {
        feedback.className = 'alert alert-' + tipo;
        feedback.textContent = mensagem;
        window.setTimeout(function() {
          feedback.classList.add('d-none');
        }, 3000);
      }

      async function enviarComando(payload) {
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

          mostrarFeedback('Comando enviado: ' + (payload.cor || payload.estado), 'success');
        } catch (erro) {
          mostrarFeedback('Falha ao enviar comando.', 'danger');
        }
      }

      // Um único listener: o payload vem do atributo data-payload do botão.
      document.querySelectorAll('[data-payload]').forEach(function(botao) {
        botao.addEventListener('click', function() {
          enviarComando(JSON.parse(botao.dataset.payload));
        });
      });

      document.getElementById('btnCiclo').addEventListener('click', function() {
        var tempos = {
          tempoVermelho: parseInt(document.getElementById('tempoVermelho').value, 10),
          tempoAmarelo: parseInt(document.getElementById('tempoAmarelo').value, 10),
          tempoVerde: parseInt(document.getElementById('tempoVerde').value, 10),
        };

        var invalido = Object.values(tempos).some(function(numero) {
          return !Number.isInteger(numero) || numero < 1;
        });

        if (invalido) {
          mostrarFeedback('Informe os três tempos em segundos (inteiros maiores que zero).', 'danger');
          return;
        }

        enviarComando({
          estado: 'ciclo',
          tempoVermelho: tempos.tempoVermelho,
          tempoVerde: tempos.tempoVerde,
          tempoAmarelo: tempos.tempoAmarelo,
        });
      });
    })();
  </script>
@endpush
