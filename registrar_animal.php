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

  h2 {
    text-align: center;
    margin-top: 10px;
    color: #2e7d32;
    font-weight: 700;
  }

  p {
    text-align: center;
    color: #4b4b4b;
    margin-bottom: 10px;
  }

  #map {
    flex: 1; /* Ocupa o resto da tela */
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
    max-height: 400px; /* controla o tamanho total do popup */
    overflow-y: auto; /* permite rolar se houver muitas opções */
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

  /* Corrige o select de raça dentro do popup */
.popup-form select[name="raca_id"] {
  max-height: 120px;        /* altura interna do menu */
  overflow-y: auto;         /* ativa rolagem */
  display: block;
  position: relative;
  z-index: 10000;           /* garante que o menu fique sobre o mapa */
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

  .popup-form button:hover {
    background: #388e3c;
  }

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

<a href="index.php" class="back-btn">⬅️ Voltar</a>
<h2>🐾 Cadastro de Animal Perdido ou Encontrado</h2>
<p>Clique no mapa para marcar o local e cadastrar o animal.</p>

<div id="map"></div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
var map = L.map('map').setView([-29.78126, -57.10689], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

var racas = <?php echo json_encode($racas, JSON_UNESCAPED_UNICODE); ?>;

map.on('click', function(e){
    var lat = e.latlng.lat;
    var lng = e.latlng.lng;

    var racasOptions = '<option value="">Selecione</option>';
    racas.forEach(r => { racasOptions += `<option value="${r.id}">${r.racas}</option>`; });

    var formHtml = `
    <form id="formAnimal" class="popup-form" enctype="multipart/form-data">
        <input type="hidden" name="lat" value="${lat}">
        <input type="hidden" name="lng" value="${lng}">
        <label>Nome:</label><input name="nome" type="text" placeholder="Ex: Thor">
        <label>Espécie:</label>
        <select name="especie" required>
            <option value="">Selecione</option>
            <option value="cachorro">Cachorro</option>
            <option value="gato">Gato</option>
            <option value="outros">Outros</option>
        </select>
        <label>Gênero:</label>
        <select name="genero" required>
            <option value="">Selecione</option>
            <option value="macho">Macho</option>
            <option value="femea">Fêmea</option>
            <option value="nao_informado">Não informado</option>
        </select>
        <label>Raça:</label>
        <select name="raca_id">${racasOptions}</select>
        <label>Porte:</label>
        <select name="porte" required>
            <option value="">Selecione</option>
            <option value="pequeno">Pequeno</option>
            <option value="medio">Médio</option>
            <option value="grande">Grande</option>
        </select>
        <label>Cor predominante:</label>
        <select name="cor_predominante">
            <option value="">Selecione</option>
            <option value="preto">Preto</option>
            <option value="branco">Branco</option>
            <option value="marrom">Marrom</option>
            <option value="cinza">Cinza</option>
            <option value="caramelo">Caramelo</option>
            <option value="preto e branco">Preto e Branco</option>
            <option value="outros">Outros</option>
        </select>
        <label>Idade:</label>
        <select name="idade" required>
            <option value="">Selecione</option>
            <option value="Filhote">Filhote</option>
            <option value="Adulto">Adulto</option>
            <option value="Idoso">Idoso</option>
        </select>
        <label>Situação:</label>
        <select name="situacao" required>
            <option value="">Selecione</option>
            <option value="perdido">Perdido</option>
            <option value="encontrado">Encontrado</option>
        </select>
        <label>Data do ocorrido:</label><input name="data_ocorrido" type="date">
        <label>Descrição:</label><textarea name="descricao" rows="2" placeholder="Informações adicionais..."></textarea>
        <label>Telefone para contato:</label><input name="telefone_contato" type="text" placeholder="(XX) XXXXX-XXXX" required>
        <label>Foto:</label><input name="foto" type="file" accept="image/*">
        <button type="button" onclick="salvarAnimal()">📍 Salvar Animal</button>
    </form>
    `;

    L.popup({ maxWidth: 320, maxHeight: 420, closeButton: true })
      .setLatLng(e.latlng)
      .setContent(formHtml)
      .openOn(map);
});

function salvarAnimal(){
    var form = document.getElementById('formAnimal');
    var formData = new FormData(form);

    fetch('salvar_local.php', { method: 'POST', body: formData })
    .then(res => res.text())
    .then(txt => {
        alert(txt);
        location.reload();
    })
    .catch(err => alert('Erro: ' + err));
}
</script>
</body>
</html>
