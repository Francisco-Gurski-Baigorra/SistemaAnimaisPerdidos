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

<a href="gerenciar_animais.php" class="btn btn-secondary mb-3">⬅ Voltar</a>

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
            <select name="situacao" class="form-control" required>
                <option value="perdido"    <?= $animal['situacao']=='perdido' ? 'selected' : '' ?>>Perdido</option>
                <option value="encontrado" <?= $animal['situacao']=='encontrado' ? 'selected' : '' ?>>Encontrado</option>
                <option value="encontrado" <?= $animal['situacao']=='resgatado' ? 'selected' : '' ?>>Resgatado</option>
            </select>

            <label class="form-label mt-2">Espécie:</label>
            <select name="especie" class="form-control" required>
                <option value="cachorro" <?= $animal['especie']=='cachorro' ? 'selected' : '' ?>>Cachorro</option>
                <option value="gato"     <?= $animal['especie']=='gato' ? 'selected' : '' ?>>Gato</option>
                <option value="outros"   <?= $animal['especie']=='outros' ? 'selected' : '' ?>>Outros</option>
            </select>

            <label class="form-label mt-2">Gênero:</label>
            <select name="genero" class="form-control" required>
                <option value="macho"          <?= $animal['genero']=='macho' ? 'selected' : '' ?>>Macho</option>
                <option value="femea"          <?= $animal['genero']=='femea' ? 'selected' : '' ?>>Fêmea</option>
                <option value="nao_informado" <?= $animal['genero']=='nao_informado' ? 'selected' : '' ?>>Não informado</option>
            </select>

            <label class="form-label mt-2">Raça:</label>
        <select name="raca_id" class="form-control" required>
    <option value="">-- Selecione a raça --</option>
    <?php
    // Busca todas as raças ordenadas
    $sqlRacas = "SELECT id, racas FROM racas ORDER BY racas";
    $resultRacas = $conexao->query($sqlRacas);

    while ($row = $resultRacas->fetch_assoc()) {
        $selected = ($row['id'] == $animal['raca_id']) ? 'selected' : '';
        echo '<option value="' . $row['id'] . '" ' . $selected . '>' . htmlspecialchars($row['racas']) . '</option>';
    }
    ?>
        </select>

            <label class="form-label mt-2">Porte:</label>
            <select name="porte" class="form-control" required>
                <option value="pequeno" <?= $animal['porte']=='pequeno' ? 'selected' : '' ?>>Pequeno</option>
                <option value="medio"   <?= $animal['porte']=='medio' ? 'selected' : '' ?>>Médio</option>
                <option value="grande"  <?= $animal['porte']=='grande' ? 'selected' : '' ?>>Grande</option>
            </select>

            <label class="form-label mt-2">Cor Predominante:</label>
            <select name="cor_predominante" class="form-control">
                <option value="preto"     <?= $animal['cor_predominante']=='preto' ? 'selected' : '' ?>>Preto</option>
                <option value="branco"    <?= $animal['cor_predominante']=='branco' ? 'selected' : '' ?>>Branco</option>
                <option value="marrom"    <?= $animal['cor_predominante']=='marrom' ? 'selected' : '' ?>>Marrom</option>
                <option value="cinza"     <?= $animal['cor_predominante']=='cinza' ? 'selected' : '' ?>>Cinza</option>
                <option value="caramelo"  <?= $animal['cor_predominante']=='caramelo' ? 'selected' : '' ?>>Caramelo</option>
                <option value="tricolor"  <?= $animal['cor_predominante']=='tricolor' ? 'selected' : '' ?>>Tricolor</option>
            </select>

            <label class="form-label mt-2">Idade:</label>
            <select name="idade" class="form-control" required>
                <option value="filhote" <?= $animal['idade']=='filhote' ? 'selected' : '' ?>>Filhote</option>
                <option value="adulto"  <?= $animal['idade']=='adulto' ? 'selected' : '' ?>>Adulto</option>
                <option value="idoso"   <?= $animal['idade']=='idoso' ? 'selected' : '' ?>>Idoso</option>
            </select>

            <label class="form-label mt-2">Telefone de Contato:</label>
<input
    type="text"
    name="telefone_contato"
    id="telefone_contato"
    class="form-control"
    maxlength="15"
    placeholder="(99) 99999-9999"
    value="<?= htmlspecialchars($animal['telefone_contato']) ?>"
    required
>


        
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

<script>
document.getElementById('telefone_contato').addEventListener('input', function (e) {
    let valor = e.target.value.replace(/\D/g, '');

    // Limita a 11 dígitos
    if (valor.length > 11) {
        valor = valor.slice(0, 11);
    }

    // Aplica máscara
    if (valor.length <= 2) {
        valor = '(' + valor;
    } 
    else if (valor.length <= 7) {
        valor = '(' + valor.slice(0, 2) + ') ' + valor.slice(2);
    } 
    else {
        valor = '(' + valor.slice(0, 2) + ') ' + valor.slice(2, 7) + '-' + valor.slice(7);
    }

    e.target.value = valor;
});
</script>


</body>
</html>
