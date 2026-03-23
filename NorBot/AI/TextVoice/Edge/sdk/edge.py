from flask import Flask, request, send_file
import asyncio
import edge_tts
import tempfile

app = Flask(__name__)

@app.route('/generate', methods=['POST'])
def generate():
    data = request.json
    texto = data.get('text', '')
    voz = data.get('voice', '')
    if not texto:
        return {"error": "No text provided"}, 400

    async def generar():
        with tempfile.NamedTemporaryFile(delete=False, suffix=".mp3") as f:
            await edge_tts.Communicate(texto, voz).save(f.name)
            return f.name

    archivo = asyncio.run(generar())
    return send_file(archivo, mimetype="audio/mpeg")

if __name__ == '__main__':
    app.run(port=5005)
