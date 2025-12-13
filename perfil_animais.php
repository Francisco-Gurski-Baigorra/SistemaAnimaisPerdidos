<?php
session_start();
include('conecta.php');

// Se não estiver logado, redireciona
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$id_usuario = $_SESSION['usuario_id'];

// Buscar informações do usuário
$sqlUsuario = "SELECT nome, email FROM usuarios WHERE id = ?";
$stmt = $conexao->prepare($sqlUsuario);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

// Buscar animais com JOIN nas raças
$sqlAnimais = "
    SELECT 
        a.id, a.nome, a.situacao, a.especie, a.genero, a.cor_predominante, 
        a.idade, a.descricao, a.data_ocorrido, a.foto,
        a.latitude, a.longitude, a.porte, 
        r.racas AS nome_raca
    FROM animais a
    LEFT JOIN racas r ON a.raca_id = r.id
    WHERE a.usuario_id = ?
";
$stmt2 = $conexao->prepare($sqlAnimais);
$stmt2->bind_param("i", $id_usuario);
$stmt2->execute();
$resultAnimais = $stmt2->get_result();

// Função para preencher N/A automaticamente
function mostrar($valor) {
    return (!empty($valor) ? htmlspecialchars($valor) : "N/A");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Animais Registrados - Rastreia Bicho 🐾</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

 body {
    background-color: #ffffff;
    min-height: 100vh;
    margin: 0;
    padding-top: 0;
    font-family: Arial, sans-serif;
    display: flex;
    flex-direction: column;
}

/* ======= Navbar ======= */
.navbar {
    background-color: #179e46;
    padding: 1rem;
    border-bottom: 3px solid #2e3531;
    box-shadow: 0 2px 6px rgba(54, 51, 51, 0.15);
    width: 100%;
}

.navbar-brand {
    font-weight: bold;
    font-size: 1.7rem;
    color: #2b2b2b !important;
}

/* ===============================
   CARDS DOS ANIMAIS
================================*/
.card {
    border-radius: 12px;
    border: 2px solid #2e3531;
    background: #ffffff;
    box-shadow: 0 4px 10px rgba(0,0,0,0.12);
    transition: .2s;
}

.card:hover {
    transform: translateY(-6px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.16);
}

/* Imagem */
.card img {
    height: 200px;
    object-fit: cover;
    border-radius: 12px 12px 0 0;
}

[id^="map"] {
    height: 200px;
    border-radius: 10px;
    border: 1px solid #ccc;
}

</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg fixed-top">
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

            <a href="logout.php" class="btn btn-danger me-2">
                <i class="bi bi-box-arrow-right"></i> Sair
            </a>
        </div>
    </div>
</nav>

<div class="container mb-5" style="padding-top: 90px;">
    <div class="text-center mb-4">
        <h2 class="fw-bold text-dark">Olá, <?= htmlspecialchars($usuario['nome']) ?>!</h2>
        <p class="text-muted">Aqui estão os animais que você registrou:</p>
    </div>

    <?php if ($resultAnimais->num_rows > 0): ?>
    <div class="row justify-content-center g-4">

        <?php while ($animal = $resultAnimais->fetch_assoc()): ?>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex justify-content-center">

            <div class="card" style="width: 18rem;">

                <?php if (!empty($animal['foto'])): ?>
                    <img src="uploads/<?= $animal['foto'] ?>" class="card-img-top">
                <?php else: ?>
                    <div class="bg-light text-center py-5 text-muted">Sem foto</div>
                <?php endif; ?>

                <div class="card-body text-center">
                    <h5 class="card-title"><?= mostrar($animal['nome']) ?></h5>
                    <p class="text-muted mb-2"><?= mostrar($animal['situacao']) ?></p>

                    <button class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#modal<?= $animal['id'] ?>">
                        <i class="bi bi-info-circle"></i> Ver Detalhes
                    </button>

                    <a href="editar_animal.php?id=<?= $animal['id'] ?>" class="btn btn-outline-primary w-100 mb-2">
                        <i class="bi bi-pencil"></i> Editar
                    </a>

                    <button class="btn btn-outline-success w-100 mb-2" onclick="mostrarConfirmacaoResgate(<?= $animal['id'] ?>)">
                        <i class="bi bi-check2-circle"></i> Animal Resgatado
                    </button>

                    <div id="confirmarResgate<?= $animal['id'] ?>" class="d-none">
                        <p class="text-success fw-bold mt-2">Confirmar Resgate?</p>
                        <a href="excluir_animal.php?id=<?= $animal['id'] ?>&resgate=1" class="btn btn-success btn-sm">Sim</a>
                        <button class="btn btn-secondary btn-sm" onclick="cancelarConfirmacaoResgate(<?= $animal['id'] ?>)">Cancelar</button>
                    </div>

                    <button class="btn btn-outline-danger w-100 mt-2" onclick="mostrarConfirmacaoExclusao(<?= $animal['id'] ?>)">
                        <i class="bi bi-trash"></i> Excluir
                    </button>

                    <div id="confirmar<?= $animal['id'] ?>" class="d-none">
                        <p class="text-danger fw-bold mt-2">Deseja excluir?</p>
                        <a href="excluir_animal.php?id=<?= $animal['id'] ?>" class="btn btn-danger btn-sm">Sim</a>
                        <button class="btn btn-secondary btn-sm" onclick="cancelarConfirmacaoExclusao(<?= $animal['id'] ?>)">Cancelar</button>
                    </div>

                </div>
            </div>
        </div>

        <!-- MODAL DETALHES -->
        <div class="modal fade" id="modal<?= $animal['id'] ?>">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">

                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Detalhes do Animal</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <?php if (!empty($animal['foto'])): ?>
                            <img src="uploads/<?= $animal['foto'] ?>" class="img-fluid rounded mb-3">
                        <?php endif; ?>

                        <ul class="list-group mb-3">
                            <li class="list-group-item"><strong>Nome:</strong> <?= mostrar($animal['nome']) ?></li>
                            <li class="list-group-item"><strong>Situação:</strong> <?= mostrar($animal['situacao']) ?></li>
                            <li class="list-group-item"><strong>Espécie:</strong> <?= mostrar($animal['especie']) ?></li>
                            <li class="list-group-item"><strong>Gênero:</strong> <?= mostrar($animal['genero']) ?></li>
                            <li class="list-group-item"><strong>Raça:</strong> <?= mostrar($animal['nome_raca']) ?></li>
                            <li class="list-group-item"><strong>Porte:</strong> <?= mostrar($animal['porte']) ?></li>
                            <li class="list-group-item"><strong>Cor:</strong> <?= mostrar($animal['cor_predominante']) ?></li>
                            <li class="list-group-item"><strong>Idade:</strong> <?= mostrar($animal['idade']) ?></li>
                            <li class="list-group-item"><strong>Descrição:</strong><br><?= mostrar($animal['descricao']) ?></li>
                            <li class="list-group-item"><strong>Data do ocorrido:</strong> <?= mostrar($animal['data_ocorrido']) ?></li>
                        </ul>

                        <?php if ($animal['latitude'] && $animal['longitude']): ?>
                            <div id="map<?= $animal['id'] ?>" data-lat="<?= $animal['latitude'] ?>" data-lng="<?= $animal['longitude'] ?>"></div>
                        <?php endif; ?>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>

        <?php endwhile; ?>

    </div>

    <?php else: ?>
    <div class="alert alert-warning text-center">
        Nenhum animal registrado.
    </div>
    <?php endif; ?>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
function mostrarConfirmacaoExclusao(id) {
    document.getElementById("confirmar" + id).classList.remove("d-none");
}
function cancelarConfirmacaoExclusao(id) {
    document.getElementById("confirmar" + id).classList.add("d-none");
}

function mostrarConfirmacaoResgate(id) {
    document.getElementById("confirmarResgate" + id).classList.remove("d-none");
}
function cancelarConfirmacaoResgate(id) {
    document.getElementById("confirmarResgate" + id).classList.add("d-none");
}

document.addEventListener('shown.bs.modal', function (e) {
    const mapDiv = e.target.querySelector('[id^="map"]');
    if (mapDiv && !mapDiv.dataset.loaded) {
        const lat = mapDiv.dataset.lat;
        const lng = mapDiv.dataset.lng;
        const map = L.map(mapDiv).setView([lat, lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        L.marker([lat, lng]).addTo(map);
        mapDiv.dataset.loaded = true;
    }
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
