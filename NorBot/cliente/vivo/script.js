let grabando = false;
let micBtn = document.getElementById("micBtn");
let micIcon = document.getElementById("micIcon");

const audioReproduccion = new Audio();

function habilitarMicrofono() {
  if (grabando) return;
  grabando = true;
  micBtn.setAttribute('aria-pressed', 'true');
  micIcon.src = '../imagenes/iconos/micro_on.png';

  escuchar().then(texto => {
    mensajeAI(texto);
  }).catch(console.error).finally(deshabilitarMicrofono);
}

function deshabilitarMicrofono() {
  grabando = false;
  micBtn.setAttribute('aria-pressed', 'false');
  micIcon.src = '../imagenes/iconos/micro_off.png';
}

micBtn.addEventListener('click', () => {
  grabando ? deshabilitarMicrofono() : habilitarMicrofono();
});


function escuchar() {
  return new Promise((res, rej) => {
    navigator.mediaDevices.getUserMedia({ audio: true }).then(stream => {
      const rec = new MediaRecorder(stream);
      const chunks = [];
      const audioContext = new AudioContext();
      const source = audioContext.createMediaStreamSource(stream);
      const analyser = audioContext.createAnalyser();
      source.connect(analyser);

      const dataArray = new Uint8Array(analyser.fftSize);
      let silencioDuracion = 0;
      const maxSilencio = 1000;
      const intervaloChequeo = 100;

      rec.ondataavailable = e => chunks.push(e.data);

      rec.onstop = () => {
        const blob = new Blob(chunks, { type: 'audio/webm' });
        const form = new FormData();
        form.append('audio', blob, 'voz.webm');
      
        audioContext.close();

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '../../AI/VoiceText/Whisper/Whisper.php');
        xhr.onload = () => {
          if (xhr.status === 200) {
            let respuesta = JSON.parse(xhr.responseText.trim());
            res(respuesta.text);
          } else {
            rej("Error en servidor");
          }
        };
        xhr.onerror = () => rej("Error de red");
        xhr.send(form);
      };

      rec.start();

      const detectarSilencio = () => {
        analyser.getByteTimeDomainData(dataArray);
        let maxVolumen = 0;

        for (let i = 0; i < dataArray.length; i++) {
          const valor = Math.abs(dataArray[i] - 128);
          if (valor > maxVolumen) maxVolumen = valor;
        }

        if (maxVolumen < 10) { 
          silencioDuracion += intervaloChequeo;
        } else {
          silencioDuracion = 0;
        }

        if (silencioDuracion > maxSilencio) {
          rec.stop();
          stream.getTracks().forEach(t => t.stop());
        } else {
          setTimeout(detectarSilencio, intervaloChequeo);
        }
      };

      detectarSilencio();
    }).catch(err => rej(err));
  });
}



function mensajeAI(mensaje) {
  const xhr = new XMLHttpRequest();
  xhr.open('POST', '../../AI/AaronModel.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onload = function () {
    if (xhr.status === 200) {
      edgePlay(xhr.responseText, "es-PE-CamilaNeural");
    } else {
      console.error('Error en la solicitud a la IA.');
    }
  };

  xhr.send('mensaje=' + encodeURIComponent(mensaje));
}

function edgePlay(text, voice) {
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "../../AI/TextVoice/Edge/Edge.php", true);
  xhr.setRequestHeader("Content-Type", "application/json");
  xhr.responseType = "blob";

  xhr.onload = function () {
    if (xhr.status === 200) {
      const blob = xhr.response;
      const url = URL.createObjectURL(blob);
      audioReproduccion.src = url;
      audioReproduccion.play();
    }
  };

  xhr.send(JSON.stringify({ text: text, voice: voice }));
}
