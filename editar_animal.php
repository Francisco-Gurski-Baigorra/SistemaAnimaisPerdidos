<?php
session_start();
include('conecta.php');

// Verifica se está logado
if (!isset($_SESSION['usuario_id'])) {
    echo "<script>alert('Você precisa estar logado para editar.'); window.location='login.php';</script>";
    exit;
}

// Pega o ID via GET de forma básica (sem ??)
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
} else {
    $id = 0;
}
$usuario_id = (int)$_SESSION['usuario_id'];

// BUSCAR O ANIMAL (SQL Direto)
$sql_animal = "SELECT * FROM animais WHERE id = $id AND usuario_id = $usuario_id";
$resultado_animal = mysqli_query($conexao, $sql_animal);

if (mysqli_num_rows($resultado_animal) === 0) {
    echo "<script>alert('Animal não encontrado.'); window.location='perfil_animais.php';</script>";
    exit;
}
$animal = mysqli_fetch_assoc($resultado_animal);

// BUSCAR TODAS AS RAÇAS
$sql_racas = "SELECT id, racas FROM racas ORDER BY racas ASC";
$lista_racas = mysqli_query($conexao, $sql_racas);

// PROCESSAR ATUALIZAÇÃO (POST)
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Pegando os dados e limpando contra erros de aspas (mysqli_real_escape_string)
    $nome             = mysqli_real_escape_string($conexao, $_POST['nome']);
    $situacao         = mysqli_real_escape_string($conexao, $_POST['situacao']);
    $especie          = mysqli_real_escape_string($conexao, $_POST['especie']);
    $genero           = mysqli_real_escape_string($conexao, $_POST['genero']);
    $raca_id          = (int)$_POST['raca_id'];
    $cor_predominante = mysqli_real_escape_string($conexao, $_POST['cor_predominante']);
    $idade            = mysqli_real_escape_string($conexao, $_POST['idade']);
    $descricao        = mysqli_real_escape_string($conexao, $_POST['descricao']);
    $telefone_contato = mysqli_real_escape_string($conexao, $_POST['telefone_contato']);
    $data_ocorrido    = mysqli_real_escape_string($conexao, $_POST['data_ocorrido']);
    $latitude         = $_POST['latitude'];
    $longitude        = $_POST['longitude'];

    // Tratamento básico para Raça e Data (se vazio vira NULL no SQL)
    $raca_sql = ($raca_id > 0) ? $raca_id : "NULL";
    $data_sql = (!empty($data_ocorrido)) ? "'$data_ocorrido'" : "NULL";

    // FOTO (Lógica mantida)
    if (!empty($_FILES["foto"]["name"])) {
        $foto_nome = uniqid() . "_" . basename($_FILES["foto"]["name"]);
        move_uploaded_file($_FILES["foto"]["tmp_name"], "uploads/" . $foto_nome);
    } else {
        $foto_nome = $animal['foto'];
    }

    // UPDATE DIRETO (Sem prepared statements)
    $sql_update = "UPDATE animais SET 
                    nome = '$nome', 
                    situacao = '$situacao', 
                    especie = '$especie', 
                    genero = '$genero', 
                    raca_id = $raca_sql,
                    cor_predominante = '$cor_predominante', 
                    idade = '$idade', 
                    descricao = '$descricao', 
                    telefone_contato = '$telefone_contato', 
                    data_ocorrido = $data_sql,
                    latitude = '$latitude', 
                    longitude = '$longitude', 
                    foto = '$foto_nome'
                  WHERE id = $id AND usuario_id = $usuario_id";

    if (mysqli_query($conexao, $sql_update)) {
        echo "<script>alert('Animal atualizado com sucesso!'); window.location='perfil_animais.php';</script>";
    } else {
        echo "Erro ao atualizar: " . mysqli_error($conexao);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Animal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body { background-color: #ffffff; margin: 0; font-family: Arial, sans-serif; }
        .navbar { background-color: #179e46; padding: 1rem; border-bottom: 3px solid #2e3531; box-shadow: 0 2px 6px rgba(54, 51, 51, 0.15); }
        .navbar-brand { font-weight: bold; font-size: 1.7rem; color: #2b2b2b !important; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; transition: 0.2s; }
        .navbar-brand:hover { transform: translateY(-2px) scale(1.04); opacity: 0.9; }
        .card { border-radius: 20px; box-shadow: 0 6px 14px rgba(0,0,0,0.2); }
        #map { height: 300px; border-radius: 12px; border: 2px solid #2f8f46; }
        .footer-rastreia { background-color: #179e46ff; color: #333; text-align: center; padding: 12px; font-size: 0.95rem; font-weight: 600; width: 100%; border-top: 2px solid #2e3531ff; margin-top: 20px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg"> 
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fa-solid fa-paw me-2"></i> RASTREIA BICHO</a>
        <div class="ms-auto">
            <a href="registrar_animal.php" class="btn btn-dark me-2"><i class="bi bi-plus-circle"></i> Registrar Animal</a>
            <a href="perfil.php" class="btn btn-dark me-2"><i class="bi bi-person-circle"></i> Perfil</a>
            <a href="perfil_animais.php" class="btn btn-dark me-2"><i class="fa-solid fa-paw"></i> Meus Animais</a>
            <a href="logout.php" class="btn btn-danger"><i class="bi bi-box-arrow-right"></i> Sair</a>
        </div>
    </div>
</nav>

<div class="container mt-4 mb-5">
    <div class="card p-4">
        <h3 class="text-center mb-3 text-success fw-bold">Editar Animal</h3>

        <form method="post" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Situação</label>
                    <select class="form-select" name="situacao" required>
                        <option value="perdido" <?php if($animal['situacao']=="perdido") echo "selected"; ?>>Perdido</option>
                        <option value="encontrado" <?php if($animal['situacao']=="encontrado") echo "selected"; ?>>Encontrado</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Nome</label>
                    <input type="text" class="form-control" name="nome" value="<?php echo $animal['nome']; ?>" required maxlength="50">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Espécie</label>
                    <select class="form-select" name="especie" required>
                        <option value="cachorro" <?php if($animal['especie']=="cachorro") echo "selected"; ?>>Cachorro</option>
                        <option value="gato" <?php if($animal['especie']=="gato") echo "selected"; ?>>Gato</option>
                        <option value="outros" <?php if($animal['especie']=="outros") echo "selected"; ?>>Outro</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Gênero</label>
                    <select class="form-select" name="genero" required>
                        <option value="macho" <?php if($animal['genero']=="macho") echo "selected"; ?>>Macho</option>
                        <option value="femea" <?php if($animal['genero']=="femea") echo "selected"; ?>>Fêmea</option>
                        <option value="nao_informado" <?php if($animal['genero']=="nao_informado") echo "selected"; ?>>Não informado</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Raça</label>
                    <select class="form-select" name="raca_id">
                        <option value="">Selecione...</option>
                        <?php while ($r = mysqli_fetch_assoc($lista_racas)): ?>
                            <option value="<?php echo $r['id']; ?>" <?php if($animal['raca_id']==$r['id']) echo 'selected'; ?>>
                                <?php echo $r['racas']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Cor Predominante</label>
                    <select class="form-select" name="cor_predominante">
                        <option value="preto" <?php if($animal['cor_predominante']=='preto') echo 'selected'; ?>>Preto</option>
                        <option value="branco" <?php if($animal['cor_predominante']=='branco') echo 'selected'; ?>>Branco</option>
                        <option value="marrom" <?php if($animal['cor_predominante']=='marrom') echo 'selected'; ?>>Marrom</option>
                        <option value="cinza" <?php if($animal['cor_predominante']=='cinza') echo 'selected'; ?>>Cinza</option>
                        <option value="caramelo" <?php if($animal['cor_predominante']=='caramelo') echo 'selected'; ?>>Caramelo</option>
                        <option value="preto e branco" <?php if($animal['cor_predominante']=='preto e branco') echo 'selected'; ?>>Preto e Branco</option>
                        <option value="outros" <?php if($animal['cor_predominante']=='outros') echo 'selected'; ?>>Outros</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Idade</label>
                    <select class="form-select" name="idade">
                        <option value="Filhote" <?php if($animal['idade']=='Filhote') echo 'selected'; ?>>Filhote</option>
                        <option value="Adulto" <?php if($animal['idade']=='Adulto') echo 'selected'; ?>>Adulto</option>
                        <option value="Idoso" <?php if($animal['idade']=='Idoso') echo 'selected'; ?>>Idoso</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Telefone para contato</label>
                    <input type="text" class="form-control" name="telefone_contato" id="telefone_contato" maxlength="15" required value="<?php echo $animal['telefone_contato']; ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Data do ocorrido</label>
                    <input type="date" class="form-control" name="data_ocorrido" value="<?php echo $animal['data_ocorrido']; ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Foto (opcional)</label>
                    <input type="file" class="form-control" name="foto" accept="image/*">
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">Descrição</label>
                    <textarea class="form-control" name="descricao" rows="3" maxlength="150"><?php echo $animal['descricao']; ?></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">Localização do animal (Arraste o marcador)</label>
                    <div id="map"></div>
                    <input type="hidden" name="latitude" id="latitude" value="<?php echo $animal['latitude']; ?>">
                    <input type="hidden" name="longitude" id="longitude" value="<?php echo $animal['longitude']; ?>">
                </div>

                <div class="col-12 text-center mt-3">
                    <button type="submit" class="btn btn-success px-4">Salvar Alterações</button>
                    <a href="perfil_animais.php" class="btn btn-secondary px-4 ms-2">Voltar</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// Mapa Leaflet
let lat = parseFloat("<?php echo $animal['latitude']; ?>") || -29.78;
let lng = parseFloat("<?php echo $animal['longitude']; ?>") || -57.10;

const map = L.map('map').setView([lat, lng], 14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

let marker = L.marker([lat, lng], { draggable: true }).addTo(map);

marker.on("dragend", function(e) {
    const pos = e.target.getLatLng();
    document.getElementById("latitude").value = pos.lat;
    document.getElementById("longitude").value = pos.lng;
});

// Máscara Telefone
document.getElementById('telefone_contato').addEventListener('input', function (e) {
    let v = e.target.value.replace(/\D/g, '').slice(0, 11);
    if (v.length <= 2) e.target.value = '(' + v;
    else if (v.length <= 7) e.target.value = '(' + v.slice(0, 2) + ') ' + v.slice(2);
    else e.target.value = '(' + v.slice(0, 2) + ') ' + v.slice(2, 7) + '-' + v.slice(7);
});
</script>

<footer class="footer-rastreia">
    © 2025 Rastreia Bicho
</footer>

</body>
</html>