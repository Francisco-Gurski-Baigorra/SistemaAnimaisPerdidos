<?php
session_start();
require 'conecta.php'; // Importante: caminho corrigido, pois admin normalmente está em pasta separada

// Verifica se o usuário é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'administrador') {
    header("Location: login.php");
    exit;
}

// Verifica se o ID foi passado
if (!isset($_GET['id'])) {
    die("ID do animal não informado!");
}

$animal_id = intval($_GET['id']);

// Busca dados do animal
$sql = "SELECT * FROM animais WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $animal_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Animal não encontrado!");
}

$animal = $result->fetch_assoc();

// Busca o nome da raça na tabela racas
$raca_nome = "";
if (!empty($animal['raca_id'])) {
    $sqlRaca = "SELECT racas FROM racas WHERE id = ?";
    $stmtRaca = $conexao->prepare($sqlRaca);
    $stmtRaca->bind_param("i", $animal['raca_id']);
    $stmtRaca->execute();
    $resultRaca = $stmtRaca->get_result();

    if ($resultRaca->num_rows > 0) {
        $rowRaca = $resultRaca->fetch_assoc();
        $raca_nome = $rowRaca['racas'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Animal</title>

<!-- Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

<!-- Leaflet (mapa) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<style>
    #map {
        width: 100%;
        height: 300px;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    img.preview {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #ddd;
        margin-bottom: 10px;
    }
</style>

</head>
<body class="container mt-4">

<h2 class="mb-4">✏️ Editar Animal</h2>

<form action="adm_salvar_edicao_animal.php" method="POST" enctype="multipart/form-data">

    <input type="hidden" name="id" value="<?= $animal['id'] ?>">

    <div class="row">
        <div class="col-md-4">
            <label>Foto Atual:</label><br>
            <?php if (!empty($animal['foto'])): ?>
                <img src="uploads/<?= $animal['foto'] ?>" class="preview">
            <?php else: ?>
                <p class="text-muted">Sem foto</p>
            <?php endif; ?>

            <input type="file" name="foto" class="form-control mt-2">
        </div>

        <div class="col-md-8">
            <label class="form-label">Nome:</label>
            <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($animal['nome']) ?>">

            <label class="form-label mt-2">Situação:</label>
            <select name="situacao" class="form-control">
                <option value="perdido" <?= $animal['situacao']=='perdido'?'selected':'' ?>>Perdido</option>
                <option value="encontrado" <?= $animal['situacao']=='encontrado'?'selected':'' ?>>Encontrado</option>
            </select>

            <label class="form-label mt-2">Espécie:</label>
            <input type="text" name="especie" class="form-control" value="<?= htmlspecialchars($animal['especie']) ?>">

            <label class="form-label mt-2">Gênero:</label>
            <select name="genero" class="form-control">
                <option value="macho" <?= $animal['genero']=='macho'?'selected':'' ?>>Macho</option>
                <option value="femea" <?= $animal['genero']=='femea'?'selected':'' ?>>Fêmea</option>
            </select>

            <label class="form-label mt-2">Raça:</label>
            <input type="text" name="raca" class="form-control" value="<?= htmlspecialchars($raca_nome) ?>">

            <label class="form-label mt-2">Porte:</label>
            <input type="text" name="porte" class="form-control" value="<?= htmlspecialchars($animal['porte']) ?>">

            <label class="form-label mt-2">Cor:</label>
            <input type="text" name="cor" class="form-control" value="<?= htmlspecialchars($animal['cor_predominante']) ?>">

            <label class="form-label mt-2">Idade:</label>
            <input type="text" name="idade" class="form-control" value="<?= htmlspecialchars($animal['idade']) ?>">

            <label class="form-label mt-2">Telefone:</label>
            <input type="text" name="telefone" class="form-control" value="<?= htmlspecialchars($animal['telefone_contato']) ?>">
        </div>
    </div>

    <hr>

    <h5>📍 Localização</h5>

    <div id="map"></div>

    <label>Latitude:</label>
    <input type="text" id="latitude" name="latitude" class="form-control" value="<?= $animal['latitude'] ?>" readonly>

    <label class="mt-2">Longitude:</label>
    <input type="text" id="longitude" name="longitude" class="form-control" value="<?= $animal['longitude'] ?>" readonly>

    <button type="submit" class="btn btn-success mt-3">💾 Salvar Alterações</button>
</form>

<!-- SCRIPT DO MAPA -->
<script>
let lat = parseFloat("<?= $animal['latitude'] ?>");
let lng = parseFloat("<?= $animal['longitude'] ?>");

// Se não tiver localização salva, define um ponto padrão
if (!lat || !lng || lat === 0 || lng === 0) {
    lat = -29.78;
    lng = -57.10;
}

const map = L.map('map').setView([lat, lng], 14);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19
}).addTo(map);

let marker = L.marker([lat, lng], { draggable: true }).addTo(map);

marker.on("dragend", function(e) {
    const pos = e.target.getLatLng();
    document.getElementById("latitude").value = pos.lat;
    document.getElementById("longitude").value = pos.lng;
});
</script>

</body>
</html>
