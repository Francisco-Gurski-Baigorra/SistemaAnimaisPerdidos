<?php
session_start();
include('conecta.php');

if (!isset($_SESSION['usuario_id'])) {
    echo "<script>alert('Você precisa estar logado para registrar um animal.'); window.location='login.php';</script>";
    exit;
}

$racas = [];
$sql = "SELECT id, racas FROM racas ORDER BY racas";
$res = $conexao->query($sql);
while ($row = $res->fetch_assoc()) {
    $racas[] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cadastro de Animal no Mapa</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<style>
  html, body {
    height: 100%;
    margin: 0;
    background-color: #e9f7ef;
    font-family: 'Segoe UI', sans-serif;
    display: flex;
    flex-direction: column;
  }

  h2 { text-align: center; margin-top: 10px; color: #2e7d32; font-weight: 700; }
  p { text-align: center; color: #4b4b4b; margin-bottom: 10px; }

  #map {
    flex: 1;
    width: 96%;
    margin: 0 auto 15px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
  }

  .leaflet-popup-content-wrapper {
    border-radius: 12px;
    background: #ffffff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.25);
  }

  .popup-form {
    max-height: 400px;
    overflow-y: auto;
  }

  .popup-form label {
    font-weight: 600;
    margin-top: 6px;
    display: block;
  }

  .popup-form input,
  .popup-form select,
  .popup-form textarea {
    width: 100%;
    padding: 6px 8px;
    border: 1px solid #ccc;
    border-radius: 6px;
    margin-bottom: 8px;
    font-size: 14px;
  }

  .popup-form select[name="raca_id"] {
    max-height: 120px;
    overflow-y: auto;
    display: block;
    position: relative;
    z-index: 10000;
    background-color: white;
  }

  .popup-form button {
    background: #4CAF50;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    width: 100%;
    font-weight: 600;
    transition: background 0.3s;
  }

  .popup-form button:hover { background: #388e3c; }

  .back-btn {
    display: block;
    width: fit-content;
    margin: 10px auto 0;
    text-decoration: none;
    color: #2e7d32;
    border: 1px solid #2e7d32;
    border-radius: 8px;
    padding: 5px 10px;
    transition: all 0.3s;
  }

  .back-btn:hover {
    background: #2e7d32;
    color: white;
  }
</style>
</head>
<body>

<a href="index.php" class="back-btn">Voltar</a>
<h2>Cadastro de Animal Perdido ou Encontrado</h2>
<p>Clique no mapa para marcar o local e cadastrar o animal.</p>

<div id="map"></div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
const map = L.map('map').setView([-29.78126, -57.10689], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

const racas = <?php echo json_encode($racas, JSON_UNESCAPED_UNICODE); ?>;
const usuario_id = <?php echo $_SESSION['usuario_id']; ?>;

map.on('click', function(e) {
    const lat = e.latlng.lat.toFixed(6);
    const lng = e.latlng.lng.toFixed(6);

    let racasOptions = '<option value="">-- Selecione a raça --</option>';
    racas.forEach(r => {
        racasOptions += `<option value="${r.id}">${r.racas}</option>`;
    });

    const formHtml = `
    <form id="formAnimal" class="popup-form">
        <input type="hidden" name="latitude" value="${lat}">
        <input type="hidden" name="longitude" value="${lng}">
        <input type="hidden" name="usuario_id" value="${usuario_id}">

        <label>Nome do animal: <span style="color:red;">*</span></label>
        <input name="nome" type="text" placeholder="Ex: Rex, Mia...">

        <label>Situação: <span style="color:red;">*</span></label>
        <select name="situacao">
            <option value="">Selecione</option>
            <option value="perdido">Perdido</option>
            <option value="encontrado">Encontrado</option>
        </select>

        <label>Espécie: <span style="color:red;">*</span></label>
        <select name="especie">
            <option value="">Selecione</option>
            <option value="cachorro">Cachorro</option>
            <option value="gato">Gato</option>
            <option value="outros">Outros</option>
        </select>

        <label>Gênero: <span style="color:red;">*</span></label>
        <select name="genero">
            <option value="">Selecione</option>
            <option value="macho">Macho</option>
            <option value="femea">Fêmea</option>
            <option value="nao_informado">Não informado</option>
        </select>

        <label>Raça: <span style="color:red;">*</span></label>
        <select name="raca_id">${racasOptions}</select>

        <label>Data que foi visto: <span style="color:red;">*</span></label>
        <input name="data_ocorrido" type="date" value="<?php echo date('Y-m-d'); ?>">

        <label>Seu telefone (com DDD): <span style="color:red;">*</span></label>
        <input name="telefone_contato" id="tel" type="text" placeholder="(55) 99999-9999" maxlength="15">

        <label>Foto do animal: <span style="color:red;">*</span></label>
        <input name="foto" type="file" accept="image/*">

        <label>Porte:</label>
        <select name="porte">
            <option value="">Selecione</option>
            <option value="pequeno">Pequeno</option>
            <option value="medio">Médio</option>
            <option value="grande">Grande</option>
        </select>

        <label>Cor Predominante:</label>
        <select name="cor_predominante">
            <option value="">Selecione</option>
            <option value="preto">Preto</option>
            <option value="branco">Branco</option>
            <option value="marrom">Marrom</option>
            <option value="cinza">Cinza</option>
            <option value="caramelo">Caramelo</option>
            <option value="tricolor">Tricolor</option>
        </select>

        <label>Idade aproximada:</label>
        <select name="idade">
            <option value="">Selecione</option>
            <option value="filhote">Filhote</option>
            <option value="adulto">Adulto</option>
            <option value="idoso">Idoso</option>
        </select>

        <label>Descrição (opcional):</label>
        <textarea name="descricao" rows="3" placeholder="Detalhes que ajudem na identificação..."></textarea>

        <button type="button" onclick="salvarAnimal()">Salvar e Publicar</button>
    </form>`;

    L.popup({ maxWidth: 340, closeOnClick: false, autoClose: false })
        .setLatLng(e.latlng)
        .setContent(formHtml)
        .openOn(map);

    // Máscara do telefone
    setTimeout(() => {
        const telInput = document.getElementById('tel');
        if (telInput) {
            telInput.addEventListener('input', function(e) {
                let v = e.target.value.replace(/\D/g, '');
                if (v.length > 11) v = v.substr(0, 11);
                v = v.replace(/^(\d{2})(\d)/g, '($1) $2');
                v = v.replace(/(\d{5})(\d)/, '$1-$2');
                e.target.value = v;
            });
        }
    }, 100);
});

function salvarAnimal() {
    const form = document.getElementById('formAnimal');

    const obrigatorios = {
        nome: "Nome do animal",
        situacao: "Situação",
        especie: "Espécie",
        genero: "Gênero",
        raca_id: "Raça",
        data_ocorrido: "Data que foi visto",
        telefone_contato: "Telefone de contato"
    };

    for (let campo in obrigatorios) {
        if (!form[campo].value || form[campo].value.trim() === '') {
            alert(`Preencha o campo obrigatório: ${obrigatorios[campo]}`);
            form[campo].focus();
            return;
        }
    }

    // Validação do telefone
    const tel = form.telefone_contato.value.replace(/\D/g, '');
    if (tel.length !== 11) {
        alert("Telefone deve ter 11 dígitos (ex: 55999999999)");
        form.telefone_contato.focus();
        return;
    }

    // Validação da foto
    if (!form.foto.files || form.foto.files.length === 0) {
        alert("Selecione uma foto do animal");
        return;
    }

    const formData = new FormData(form);

    fetch('salvar_local.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.text())
    .then(msg => {
        alert(msg.trim());
        if (msg.includes('sucesso') || msg.includes('cadastrado')) {
            location.reload();
        }
    })
    .catch(() => alert('Erro de conexão'));
}
</script>
</body>
</html>