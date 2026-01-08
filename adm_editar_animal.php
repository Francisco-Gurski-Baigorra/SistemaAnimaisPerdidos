<?php
session_start();
require 'conecta.php';

if (isset($_SESSION['usuario_id'])) {
    if ($_SESSION['tipo_usuario'] != 'administrador') {
        header("Location: login.php");
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}

$animal_id = $_GET['id'];

$sql = "SELECT * FROM animais WHERE id = $animal_id";
$result = mysqli_query($conexao, $sql);
$animal = mysqli_fetch_assoc($result);

$sqlRacas = "SELECT id, racas FROM racas ORDER BY racas";
$resRacas = mysqli_query($conexao, $sqlRacas);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Animal - Rastreia Bicho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        
     html, body 
     { height: 100%; margin: 0; }

    body 
    { display: flex; flex-direction: column; background-color: #ffffff; font-family: Arial, sans-serif; }
        
    .conteudo-principal 
    { flex: 1; padding-bottom: 50px; }
        
    .navbar-custom { background-color: #179e46; padding: 1rem 0; border-bottom: 3px solid #2e3531; }
    .navbar-brand { font-weight: bold; font-size: 1.7rem; color: #2b2b2b !important; text-decoration: none; }
    .footer-rastreia { background-color: #179e46; color: #2b2b2b; text-align: center; padding: 15px; font-weight: bold; border-top: 3px solid #2e3531; }
        
        
    #map { height: 300px; border-radius: 8px; border: 2px solid #ccc; }
    .preview { width: 100%; max-width: 260px; height: 260px; object-fit: cover; border-radius: 12px; border: 2px solid #ccc; }

    </style>
</head>
<body>

<nav class="navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="admin.php">
            <i class="fa-solid fa-paw"></i> RASTREIA BICHO
        </a>
    </div>
</nav>

<div class="container mt-4 conteudo-principal">
    <h2 class="mb-3">Editar Animal</h2>
    <a href="gerenciar_animais.php" class="btn btn-secondary mb-4">Voltar</a>

<form action="adm_salvar_edicao_animal.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $animal['id']; ?>">

<div class="row">
    <div class="col-md-4 text-center">
    <label class="fw-bold">Foto Atual</label><br>
    <?php if ($animal['foto'] != "") { ?>
        <img src="uploads/<?php echo $animal['foto']; ?>" class="preview mb-2">
    <?php } else { ?>
        <p>Sem foto cadastrada</p>
    <?php } ?>
    <input type="file" name="foto" class="form-control mt-2">   
</div>

<div class="col-md-8">
    <label>Nome do animal</label>
    <input type="text" name="nome" class="form-control" value="<?php echo $animal['nome']; ?>" required>
    <label class="mt-2">Situação</label>
<select name="situacao" class="form-control" required>
    <option value="perdido" <?php if($animal['situacao'] == "perdido") { echo "selected"; } ?>>Perdido</option>
    <option value="encontrado" <?php if($animal['situacao'] == "encontrado") { echo "selected"; } ?>>Encontrado</option>
    <option value="resgatado" <?php if($animal['situacao'] == "resgatado") { echo "selected"; } ?>>Resgatado</option>
</select>

    <label class="mt-2">Espécie</label>
<select name="especie" class="form-control" required>
    <option value="cachorro" <?php if($animal['especie'] == "cachorro") { echo "selected"; } ?>>Cachorro</option>
    <option value="gato" <?php if($animal['especie'] == "gato") { echo "selected"; } ?>>Gato</option>
    <option value="outros" <?php if($animal['especie'] == "outros") { echo "selected"; } ?>>Outros</option> 
</select>

    <label class="mt-2">Gênero</label>
<select name="genero" class="form-control" required>
    <option value="macho" <?php if($animal['genero'] == "macho") { echo "selected"; } ?>>Macho</option>
    <option value="femea" <?php if($animal['genero'] == "femea") { echo "selected"; } ?>>Fêmea</option>
    <option value="nao_informado" <?php if($animal['genero'] == "nao_informado") { echo "selected"; } ?>>Não informado</option>
</select>

    <label class="mt-2">Raça</label>
<select name="raca_id" class="form-control" required>
    <?php while ($r = mysqli_fetch_assoc($resRacas)) { ?>
    <option value="<?php echo $r['id']; ?>" <?php if($animal['raca_id'] == $r['id']) { echo "selected"; } ?>>
    <?php echo $r['racas']; ?> </option>
    <?php } ?>
</select>

    <label class="mt-2">Porte</label>
<select name="porte" class="form-control">
    <option value="">Selecione</option>
    <option value="pequeno" <?php if($animal['porte'] == "pequeno") { echo "selected"; } ?>>Pequeno</option>
    <option value="medio" <?php if($animal['porte'] == "medio") { echo "selected"; } ?>>Médio</option>
    <option value="grande" <?php if($animal['porte'] == "grande") { echo "selected"; } ?>>Grande</option>
</select>

    <label class="mt-2">Cor Predominante</label>
<select name="cor_predominante" class="form-control">
    <option value="">Selecione</option>
    <option value="preto" <?php if($animal['cor_predominante'] == "preto") { echo "selected"; } ?>>Preto</option>
    <option value="branco" <?php if($animal['cor_predominante'] == "branco") { echo "selected"; } ?>>Branco</option>
    <option value="marrom" <?php if($animal['cor_predominante'] == "marrom") { echo "selected"; } ?>>Marrom</option>
    <option value="cinza" <?php if($animal['cor_predominante'] == "cinza") { echo "selected"; } ?>>Cinza</option>
    <option value="caramelo" <?php if($animal['cor_predominante'] == "caramelo") { echo "selected"; } ?>>Caramelo</option>
</select>

    <label class="mt-2">Idade Aproximada</label>
<select name="idade" class="form-control">
    <option value="">Selecione</option>
    <option value="filhote" <?php if($animal['idade'] == "filhote") { echo "selected"; } ?>>Filhote</option>
    <option value="adulto" <?php if($animal['idade'] == "adulto") { echo "selected"; } ?>>Adulto</option>
    <option value="idoso" <?php if($animal['idade'] == "idoso") { echo "selected"; } ?>>Idoso</option>
</select>

<label class="mt-2">Telefone de Contato</label>
    <input type="text" name="telefone_contato" class="form-control" value="<?php echo $animal['telefone_contato']; ?>" required>
    <label class="mt-2">Descrição</label>
    <textarea name="descricao" class="form-control" rows="3"><?php echo $animal['descricao']; ?></textarea>
            </div>
        </div>

        <h5 class="mt-4">Localização no Mapa (Arraste o marcador)</h5>
        <div id="map"></div>
        
        <input type="hidden" id="latitude" name="latitude" value="<?php echo $animal['latitude']; ?>">
        <input type="hidden" id="longitude" name="longitude" value="<?php echo $animal['longitude']; ?>">

        <button type="submit" class="btn btn-success mt-3 mb-5">
            <i class="fa-solid fa-floppy-disk"></i> Salvar Alterações
        </button>
    </form>
</div>

<footer class="footer-rastreia">
    © 2025 Rastreia Bicho - Painel Administrativo
</footer>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    //corddenadas que estao salvas no banco
    var latInicial = <?php echo $animal['latitude']; ?>;
    var lngInicial = <?php echo $animal['longitude']; ?>;

    var map = L.map('map').setView([latInicial, lngInicial], 14); 
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map); //cria o mapa dentro de uma div e foca a tela nas cordenadas do animal

   
    var marker = L.marker([latInicial, lngInicial], { draggable: true }).addTo(map);  // marcador nas codernadas do animal

    
    marker.on('dragend', function(e) {
        var posicao = marker.getLatLng();
        document.getElementById('latitude').value = posicao.lat;
        document.getElementById('longitude').value = posicao.lng; // quando termina de arrsatar salva as novas cordenadas
    });
</script>
</body>
</html>