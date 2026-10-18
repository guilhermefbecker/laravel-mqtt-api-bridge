@extends('layouts.app')

@section('title', 'AdminHMD | Logs de alteraÃ§Ã£o')
@section('description', 'Logs de alteraÃ§Ã£o do sistema')

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
            <button class="btn btn-outline-secondary btn-sm" type="button" id="clearLogs">
              <i class="bi bi-trash3" aria-hidden="true"></i> Limpar logs
            </button>
          </div>

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
                  </tr>
                </thead>
                <tbody id="logsTableBody"></tbody>
              </table>
            </div>

            <div class="empty-state d-none" id="emptyLogsState">
              <i class="bi bi-info-circle" aria-hidden="true"></i>
              <p class="mb-1 fw-semibold">Nenhuma alteração registrada</p>
              <p class="text-muted small mb-0">Ações realizadas aparecerão aqui.</p>
            </div>
          </section>
        </div>
@endsection

@push('scripts')
  <script>
    (function () {
      var logsStorageKey = "adminHMD.logsAlteracao";
      var logsTableBody = document.getElementById("logsTableBody");
      var emptyLogsState = document.getElementById("emptyLogsState");

      function getLogs() {
        try {
          return JSON.parse(localStorage.getItem(logsStorageKey) || "[]");
        } catch (error) {
          return [];
        }
      }

      function formatDate(value) {
        if (!value) return "-";

        var date = new Date(value);
        if (Number.isNaN(date.getTime())) return value;

        return new Intl.DateTimeFormat("pt-BR", {
          dateStyle: "short",
          timeStyle: "medium",
        }).format(date);
      }

      function renderLogs() {
        var logs = getLogs();

        if (!logsTableBody) return;

        logsTableBody.innerHTML = logs
          .map(function (log) {
            return "<tr>" +
              "<td><span class=\"fw-semibold\">" + formatDate(log.horario) + "</span></td>" +
              "<td><span class=\"text-primary\">" + (log.usuario || "Administrador") + "</span></td>" +
              "<td>" + (log.alteracao || "Alteração registrada") + "</td>" +
              "</tr>";
          })
          .join("");

        emptyLogsState.classList.toggle("d-none", logs.length > 0);
      }

      document.getElementById("clearLogs").addEventListener("click", function () {
        if (window.confirm("Deseja limpar todo o histórico de logs?")) {
          localStorage.removeItem(logsStorageKey);
          renderLogs();
        }
      });

      renderLogs();
    })();
  </script>
@endpush
