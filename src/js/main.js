AOS.init();

function verificarHorario() {
  const agora = new Date();
  const horarios = [
    { inicio: "06:00", fim: "07:20" },
    { inicio: "07:30", fim: "08:50" },
    { inicio: "09:00", fim: "10:20" },
    { inicio: "10:15", fim: "11:35" },
    { inicio: "12:00", fim: "13:20" },
    { inicio: "15:00", fim: "16:20" },
    { inicio: "16:00", fim: "17:20" },
    { inicio: "17:30", fim: "18:50" },
    { inicio: "19:00", fim: "20:20" },
  ];

  let aulaAtual = null;
  let proximaAula = null;

  for (let i = 0; i < horarios.length; i++) {
    const [inicioHoras, inicioMinutos] = horarios[i].inicio
      .split(":")
      .map(Number);
    const [fimHoras, fimMinutos] = horarios[i].fim.split(":").map(Number);

    const inicioAula = new Date(
      agora.getFullYear(),
      agora.getMonth(),
      agora.getDate(),
      inicioHoras,
      inicioMinutos
    );
    const fimAula = new Date(
      agora.getFullYear(),
      agora.getMonth(),
      agora.getDate(),
      fimHoras,
      fimMinutos
    );

    if (agora >= inicioAula && agora <= fimAula) {
      aulaAtual = { inicio: horarios[i].inicio, fim: horarios[i].fim };
      break;
    } else if (agora < inicioAula) {
      proximaAula = {
        inicio: horarios[i].inicio,
        fim: horarios[i].fim,
        inicioAula,
      };
      break;
    }
  }

  const statusAula = document.getElementById("status-aula");
  const proximaAulaDisplay = document.getElementById("proximaAula");
  const section = document.getElementById("proxima-aula");

  if (aulaAtual) {
    statusAula.innerText = `Aula em andamento!`;
    proximaAulaDisplay.innerText = `${aulaAtual.inicio} - ${aulaAtual.fim}`;
    section.style.backgroundColor = "orange";
  } else if (proximaAula) {
    const tempoRestante = proximaAula.inicioAula - agora;
    const horas = Math.floor((tempoRestante % 86400000) / 3600000);
    const minutos = Math.floor(((tempoRestante % 86400000) % 3600000) / 60000);
    const segundos = Math.floor(
      (((tempoRestante % 86400000) % 3600000) % 60000) / 1000
    );

    statusAula.innerText = `Próxima aula em:`;
    proximaAulaDisplay.innerText = `${horas}h ${minutos}m ${segundos}s`;
    section.style.backgroundColor = "green";
  } else {
    statusAula.innerText = `Não há mais aulas hoje`;
    proximaAulaDisplay.innerText = ``;
    section.style.backgroundColor = "green";
  }
}

setInterval(verificarHorario, 1000);
verificarHorario();
