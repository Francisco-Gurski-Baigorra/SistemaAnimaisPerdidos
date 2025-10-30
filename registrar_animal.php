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
<title>Mapa de Animais Perdidos</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
#map { height: 600px; width: 100%; }
.popup-form input, .popup-form select, .popup-form textarea { width: 100%; margin-bottom: 6px; }
.popup-form button { background: #4CAF50; color: white; border: none; padding: 8px 12px; cursor: pointer; }
.popup-form button:hover { background: #45a049; }
</style>
</head>
<body>

<h2>🐶 Mapa de Animais Perdidos / Encontrados</h2>
<p>Clique no mapa para cadastrar um animal.</p>

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
        <label>Nome:</label><input name="nome" type="text">
        <label>Espécie:</label>
        <select name="especie" required>
            <option value="cachorro">Cachorro</option>
            <option value="gato">Gato</option>
            <option value="outros">Outros</option>
        </select>
        <label>Gênero:</label>
        <select name="genero" required>
            <option value="macho">Macho</option>
            <option value="femea">Fêmea</option>
            <option value="nao_informado">Não informado</option>
        </select>
        <label>Raça:</label><select name="raca_id">${racasOptions}</select>
        <label>Porte:</label>
        <select name="porte" required>
            <option value="pequeno">Pequeno</option>
            <option value="medio">Médio</option>
            <option value="grande">Grande</option>
        </select>
        <label>Cor predominante:</label>
        <select name="cor_predominante">
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
            <option value="Filhote">Filhote</option>
            <option value="Adulto">Adulto</option>
            <option value="Idoso">Idoso</option>
        </select>
        <label>Situação:</label>
        <select name="situacao" required>
            <option value="perdido">Perdido</option>
            <option value="encontrado">Encontrado</option>
        </select>
        <label>Data do ocorrido:</label><input name="data_ocorrido" type="date">
        <label>Descrição:</label><textarea name="descricao" rows="2"></textarea>
        <label>Telefone:</label><input name="telefone_contato" type="text" required>
        <label>Foto:</label><input name="foto" type="file" accept="image/*">
        <button type="button" onclick="salvarAnimal()">Salvar</button>
    </form>
    `;

    L.popup().setLatLng(e.latlng).setContent(formHtml).openOn(map);
});

function salvarAnimal(){
    var form = document.getElementById('formAnimal');
    var formData = new FormData(form);

    fetch('salvar_local.php', { method: 'POST', body: formData })
    .then(res => res.text())
    .then(txt => { alert(txt); location.reload(); })
    .catch(err => alert('Erro: ' + err));
}
</script>

</body>
</html>
