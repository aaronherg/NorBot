const btnEnviar = document.getElementById('btnEnviar');
const mensaje = document.getElementById('mensaje');
const log = document.getElementById('log');

btnEnviar.onclick = function () {
  const texto = mensaje.value.trim();
  if (texto === "") {
    alert("Escribe tu mensaje antes de enviar...");
    return;
  } else {
    mostrarMensaje('right', texto);
    mensaje.value = "";

    const msgIA = document.createElement('div');
    msgIA.classList.add('message', 'left');

    const typing = document.createElement('div');
    typing.classList.add('typing');
    typing.innerHTML = `
      <span></span><span></span><span></span>
    `;

    msgIA.appendChild(typing);
    log.appendChild(msgIA);
    log.scrollTop = log.scrollHeight;

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../../AI/AaronModel.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
      if (xhr.status === 200) {
        msgIA.innerHTML = xhr.responseText;
      } else {
        msgIA.innerHTML = 'Error en la solicitud.';
      }
    };

    xhr.send('mensaje=' + encodeURIComponent(texto));
  }
};

mensaje.addEventListener('keydown', function (e) {
  if (e.key === 'Enter') btnEnviar.click();
});

function mostrarMensaje(clase, texto) {
  const msg = document.createElement('div');
  msg.classList.add('message', clase);
  msg.textContent = texto;
  log.appendChild(msg);
  log.scrollTop = log.scrollHeight;
}
                     