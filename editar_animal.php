<?php
session_start();
include('conecta.php');

if (!isset($_SESSION['usuario_id'])) {
    echo "<script>alert('Você precisa estar logado para editar.'); window.location='login.php';</script>";
    exit;
}

$id = (int)($_GET['id'] ?? 0);
$usuario_id = (int)$_SESSION['usuario_id'];

// Buscar o animal
$sql = "SELECT * FROM animais WHERE id = ? AND usuario_id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("ii", $id, $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo "<script>alert('Animal não encontrado.'); window.location='perfil_animais.php';</script>";
    exit;
}

$animal = $resultado->fetch_assoc();

// Buscar todas as raças
$lista_racas = $conexao->query("SELECT id, racas FROM racas ORDER BY racas ASC");

// Atualização
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = $_POST['nome'] ?? '';
    $situacao = $_POST['situacao'] ?? '';
    $especie = $_POST['especie'] ?? '';
    $genero = $_POST['genero'] ?? '';
    // receber raca_id como int ou 0 (0 -> NULL no banco via NULLIF)
    $raca_id = isset($_POST['raca_id']) && $_POST['raca_id'] !== '' ? (int)$_POST['raca_id'] : 0;
    $cor = $_POST['cor_predominante'] ?? '';
    $idade = $_POST['idade'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $telefone = $_POST['telefone_contato'] ?? '';
    $data_ocorrido = $_POST['data_ocorrido'] ?? ''; // '' vai virar NULL via NULLIF
    $latitude = isset($_POST['latitude']) && $_POST['latitude'] !== '' ? (float)$_POST['latitude'] : null;
    $longitude = isset($_POST['longitude']) && $_POST['longitude'] !== '' ? (float)$_POST['longitude'] : null;

    // Foto (opcional)
    if (!empty($_FILES["foto"]["name"]) && $_FILES["foto"]["error"] === UPLOAD_ERR_OK) {
        $foto_nome = uniqid() . "_" . basename($_FILES["foto"]["name"]);
        move_uploaded_file($_FILES["foto"]["tmp_name"], __DIR__ . "/uploads/" . $foto_nome);
    } else {
        $foto_nome = $animal['foto'];
    }

    // Query: usamos NULLIF para raca_id (0 -> NULL) e para data_ocorrido ('' -> NULL)
    $sql = "UPDATE animais SET 
        nome = ?, situacao = ?, especie = ?, genero = ?, raca_id = NULLIF(?,0),
        cor_predominante = ?, idade = ?, descricao = ?, telefone_contato = ?, data_ocorrido = NULLIF(?, ''),
        latitude = ?, longitude = ?, foto = ?
        WHERE id = ? AND usuario_id = ?";

    $stmt = $conexao->prepare($sql);
    if (!$stmt) {
        die("Erro ao preparar query: " . $conexao->error);
    }

    // Tipos (15 parâmetros): 
    // nome(s), situacao(s), especie(s), genero(s), raca_id(i), cor(s), idade(s), descricao(s), telefone(s), data_ocorrido(s), latitude(d), longitude(d), foto(s), id(i), usuario_id(i)
    $types = "ssssisssssddsii";

    // Para latitude/longitude: mysqli espera floats para 'd'; se forem null, convertemos para 0.0 (ou você pode garantir que venham preenchidos)
    $lat_bind = $latitude === null ? 0.0 : $latitude;
    $lng_bind = $longitude === null ? 0.0 : $longitude;

    $bind_ok = $stmt->bind_param(
        $types,
        $nome,
        $situacao,
        $especie,
        $genero,
        $raca_id,
        $cor,
        $idade,
        $descricao,
        $telefone,
        $data_ocorrido,
        $lat_bind,
        $lng_bind,
        $foto_nome,
        $id,
        $usuario_id
    );

    if (!$bind_ok) {
        die("Erro no bind_param: " . $stmt->error);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Animal atualizado com sucesso!'); window.location='perfil_animais.php';</script>";
    } else {
        echo "Erro ao atualizar: " . $stmt->error;
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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">



<style>
body {
    background-color: #ffffff;
    margin: 0;
    font-family: Arial, sans-serif;
}



/* ======= Navbar ======= */
.navbar {
    background-color: #179e46;
    padding: 1rem;
    border-bottom: 3px solid #2e3531;
    box-shadow: 0 2px 6px rgba(54, 51, 51, 0.15);
}



.navbar-brand {
    font-weight: bold;
    font-size: 1.7rem;
    color: #2b2b2b !important;
}
.card {
    border-radius: 20px;
    box-shadow: 0 6px 14px rgba(0,0,0,0.2);
}
#map {
    height: 300px;
    border-radius: 12px;
    border: 2px solid #2f8f46;
}
</style>
</head>

<body>


<nav class="navbar navbar-expand-lg"> 
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fa-solid fa-paw me-2"></i> RASTREIA BICHO
        </a>

        <div class="ms-auto">
            <a href="registrar_animal.php" class="btn btn-dark me-2">
                <i class="bi bi-plus-circle"></i> Registrar Animal
            </a>

            <a href="perfil.php" class="btn btn-dark me-2">
                <i class="bi bi-person-circle"></i> Perfil
            </a>

            <a href="perfil_animais.php" class="btn btn-dark me-2">
                <i class="fa-solid fa-paw"></i> Meus Animais
            </a>

            <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'administrador'): ?>
    <a href="admin.php" class="btn btn-primary me-2">
        <i class="bi bi-gear-fill"></i> Administrador
    </a>
<?php endif; ?>

<a href="logout.php" class="btn btn-danger me-2">
    <i class="bi bi-box-arrow-right"></i> Sair
</a>
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
                        <option value="perdido" <?= $animal['situacao']=="perdido"?"selected":"" ?>>Perdido</option>
                        <option value="encontrado" <?= $animal['situacao']=="encontrado"?"selected":"" ?>>Encontrado</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Nome</label>
                    <input type="text" class="form-control" name="nome" value="<?= htmlspecialchars($animal['nome'] ?? '') ?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Espécie</label>
                    <select class="form-select" name="especie" required>
                        <option value="cachorro" <?= $animal['especie']=="cachorro"?"selected":"" ?>>Cachorro</option>
                        <option value="gato" <?= $animal['especie']=="gato"?"selected":"" ?>>Gato</option>
                        <option value="outros" <?= $animal['especie']=="outros"?"selected":"" ?>>Outro</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Gênero</label>
                    <select class="form-select" name="genero" required>
                        <option value="macho" <?= $animal['genero']=="macho"?"selected":"" ?>>Macho</option>
                        <option value="femea" <?= $animal['genero']=="femea"?"selected":"" ?>>Fêmea</option>
                        <option value="nao_informado" <?= $animal['genero']=="nao_informado"?"selected":"" ?>>Não informado</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Raça</label>
                    <select class="form-select" name="raca_id">
                        <option value="">Selecione...</option>
                        <?php while ($r = $lista_racas->fetch_assoc()): ?>
                            <option value="<?= $r['id'] ?>" <?= ($animal['raca_id']==$r['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($r['racas']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Cor Predominante</label>
                    <select class="form-select" name="cor_predominante">
                        <option value="">--</option>
                        <option value="preto" <?= $animal['cor_predominante']=='preto'?'selected':'' ?>>Preto</option>
                        <option value="branco" <?= $animal['cor_predominante']=='branco'?'selected':'' ?>>Branco</option>
                        <option value="marrom" <?= $animal['cor_predominante']=='marrom'?'selected':'' ?>>Marrom</option>
                        <option value="cinza" <?= $animal['cor_predominante']=='cinza'?'selected':'' ?>>Cinza</option>
                        <option value="caramelo" <?= $animal['cor_predominante']=='caramelo'?'selected':'' ?>>Caramelo</option>
                        <option value="preto e branco" <?= $animal['cor_predominante']=='preto e branco'?'selected':'' ?>>Preto e Branco</option>
                        <option value="outros" <?= $animal['cor_predominante']=='outros'?'selected':'' ?>>Outros</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Idade</label>
                    <select class="form-select" name="idade">
                        <option value="">--</option>
                        <option value="Filhote" <?= $animal['idade']=='Filhote'?'selected':'' ?>>Filhote</option>
                        <option value="Adulto" <?= $animal['idade']=='Adulto'?'selected':'' ?>>Adulto</option>
                        <option value="Idoso" <?= $animal['idade']=='Idoso'?'selected':'' ?>>Idoso</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Telefone para contato</label>
                    <input type="text" class="form-control" name="telefone_contato" value="<?= htmlspecialchars($animal['telefone_contato'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Data do ocorrido</label>
                    <input type="date" class="form-control" name="data_ocorrido" value="<?= htmlspecialchars($animal['data_ocorrido'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Foto (opcional)</label>
                    <input type="file" class="form-control" name="foto" accept="image/*">
                </div>

                <div class="col-12">
    <label class="form-label fw-bold">Descrição</label>
    <textarea
        class="form-control"
        name="descricao"
        rows="3"
        maxlength="150"
        placeholder="Máx. 100 caracteres"><?= htmlspecialchars($animal['descricao'] ?? '') ?></textarea>
</div>


                <div class="col-12">
                    <label class="form-label fw-bold">Localização do animal</label>
                    <div id="map"></div>

                    <input type="hidden" name="latitude" id="latitude" value="<?= htmlspecialchars($animal['latitude'] ?? '') ?>">
                    <input type="hidden" name="longitude" id="longitude" value="<?= htmlspecialchars($animal['longitude'] ?? '') ?>">
                </div>

                <div class="col-12 text-center mt-3">
                    <button class="btn btn-success px-4">Salvar Alterações</button>
                    <a href="perfil_animais.php" class="btn btn-secondary px-4 ms-2">Voltar</a>
                </div>

            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// Inicializa o mapa
let lat = parseFloat("<?= $animal['latitude'] ?? '0' ?>");
let lng = parseFloat("<?= $animal['longitude'] ?? '0' ?>");

// se lat/lng zero, centraliza em coordenada padrão
if (!lat || !lng) {
  lat = -29.78;
  lng = -57.10;
}

const map = L.map('map').setView([lat, lng], 14);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18
}).addTo(map);

let marker = L.marker([lat, lng], { draggable: true }).addTo(map);

// Atualiza ao mover o marcador
marker.on("dragend", function(e) {
    const pos = e.target.getLatLng();
    document.getElementById("latitude").value = pos.lat;
    document.getElementById("longitude").value = pos.lng;
});
</script>



<footer class="footer-rastreia">
    © 2025 Rastreia Bicho
</footer>

<style>
.footer-rastreia {
    background-color: #179e46ff;
    color: #333;
    text-align: center;
    padding: 12px;
    font-size: 0.95rem;
    font-weight: 600;
    width: 100%;
    border-top: 2px solid #2e3531ff;
}


</style>

</body>
</html>