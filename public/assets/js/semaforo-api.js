const luzes = { vermelho: "luzVermelho", amarelo: "luzAmarelo", verde: "luzVerde" };

// Escreve texto num elemento; se ele não existir na tela, ignora.
const texto = (id, valor) => {
  const el = document.getElementById(id);
  if (el) el.textContent = valor;
};

function aplicar(log) {
  const cor = String(log.cor || "").trim().toLowerCase();

  // Só a luz da cor recebida fica acesa, e só se estado = true.
  Object.entries(luzes).forEach(([nome, id]) => {
    document.getElementById(id)?.classList.toggle("ligado", log.estado === true && nome === cor);
  });

  const hora = new Date(log.recebido_em ?? log.created_at);
  texto("infoBruto", "Recebido: " + JSON.stringify(log));
  texto("infoComando", `Último evento: ${log.cor} = ${log.estado ? "ligado" : "desligado"} (${isNaN(hora) ? "—" : hora.toLocaleTimeString("pt-BR")})`);
  texto("infoModo", "Ciclo: " + (log.ciclo ? "ligado" : "desligado"));
}

async function atualizar() {
  try {
    const resposta = await fetch("/api/logs/ultimo", { headers: { Accept: "application/json" } });

    if (resposta.status === 404) {
      texto("statusConexao", "Conectado à API (sem registros)");
    } else if (resposta.ok) {
      texto("statusConexao", "Conectado à API");
      aplicar(await resposta.json());
    } else {
      throw new Error("HTTP " + resposta.status);
    }
  } catch (erro) {
    texto("statusConexao", "API indisponível, tentando de novo...");
  }
  setTimeout(atualizar, 1000);
}

// Contador de recarregamentos (só aparece onde existe o elemento infoReload).
const recarregamentos = (Number(sessionStorage.getItem("sfReload")) || 0) + 1;
sessionStorage.setItem("sfReload", recarregamentos);
texto("infoReload", "Recarregamentos: " + recarregamentos);

atualizar();
