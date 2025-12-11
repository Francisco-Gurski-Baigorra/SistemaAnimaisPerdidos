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

// Buscar animais
$sqlAnimais = "SELECT id, nome, situacao, especie, genero, cor_predominante, idade, descricao, data_ocorrido, foto, latitude, longitude 
               FROM animais WHERE usuario_id = ?";
$stmt2 = $conexao->prepare($sqlAnimais);
$stmt2->bind_param("i", $id_usuario);
$stmt2->execute();
$resultAnimais = $stmt2->get_result();
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
    background-color: #ffffffff;
    min-height: 100vh;
    margin: 0;
    padding-top: 24px;
    font-family: Arial, sans-serif;
    padding-top: 0;
    
    display: flex;
    flex-direction: column;
}




/* ======= Navbar igual ao index.php ======= */
.navbar {
    background-color: #179e46ff;
    padding: 1rem;
    border-bottom: 3px solid #2e3531ff;
    box-shadow: 0 2px 6px rgba(54, 51, 51, 0.15);
    width: 100%; /* garante largura total */
}

.navbar-brand {
    font-weight: bold;
    font-size: 1.7rem;
    color: #2b2b2b !important;
}

.navbar-brand i {
    font-size: 1.8rem;
    color: #2b2b2b;
}

.navbar .container {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.navbar .btn {
    padding: 7px 14px;
    border-radius: 8px;
    font-weight: 500;
    transition: 0.2s;
}

.navbar .btn:hover {
    transform: translateY(-2px);
}

@media (max-width: 480px) {
    .card-perfil {
        padding: 18px;
        margin: 0 12px;
    }
}

/* ===============================
   CARDS DOS ANIMAIS (com hover suave)
================================*/
.card {
    border-radius: 10px;
    border: 1px solid #e6e6e6;          /* borda mais leve por padrão */
    background: #ffffff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease;
    will-change: transform;
    overflow: hidden;                   /* garante que a imagem não "vaze" ao fazer transform */
    cursor: default;
}

.card:hover {
    transform: translateY(-6px);        /* leve "lift" */
    box-shadow: 0 8px 20px rgba(0,0,0,0.12); /* sombra mais pronunciada */
    border-color: #d0d0d0;              /* borda um pouco mais escura ao hover */
}

/* imagem do card (mantém comportamento atual) */
.card img {
    height: 200px;
    object-fit: cover;
    border-radius: 10px 10px 0 0;
    display: block;
}

/* botões (mantém estilo) */
.btn-success {
    background-color: #179e46ff;
    border: none;
}
.btn-success:hover {
    background-color: #12843b;
}

/* Mapa */
[id^="map"] {
    height: 200px;
    border-radius: 10px;
    border: 1px solid #ccc;
}

.botoes-confirmar-resgate {
    margin-bottom: 25px; /* 🔥 Ajuste aqui a distância */
}



</style>
</head>
<body>

<!-- ===============================
     NAVBAR
================================-->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fa-solid fa-paw me-2"></i> RASTREIA BICHO
        </a>

        <div class="ms-auto">
            <?php if (isset($_SESSION['usuario_id'])): ?>

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

            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- ===============================
     CONTEÚDO CENTRAL
================================-->
<div class="container mb-5">
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
                    <img src="uploads/<?= htmlspecialchars($animal['foto']) ?>" class="card-img-top">
                <?php else: ?>
                    <div class="bg-light text-center py-5 text-muted">Sem foto</div>
                <?php endif; ?>

                <div class="card-body text-center">
                    <h5 class="card-title"><?= htmlspecialchars($animal['nome']) ?></h5>
                    <p class="text-muted mb-2"><?= htmlspecialchars($animal['situacao']) ?></p>

                    <button class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#modal<?= $animal['id'] ?>">
                        <i class="bi bi-info-circle"></i> Ver Detalhes
                    </button>

                    <a href="editar_animal.php?id=<?= $animal['id'] ?>" class="btn btn-outline-primary w-100 mb-2">
                        <i class="bi bi-pencil"></i> Editar
                    </a>


                    <button class="btn btn-outline-success w-100 mb-2 botao-resgatado" 
        onclick="mostrarConfirmacaoResgate(<?= $animal['id'] ?>)">
    <i class="bi bi-check2-circle"></i> Animal Resgatado
</button>


<div id="confirmarResgate<?= $animal['id'] ?>" class="mt-2 d-none">
    <p class="text-success fw-bold">Confirmar Resgate?</p>

    <!-- Div para criar distância dos botões -->
    <div class="mb-3">
        <a href="excluir_animal.php?id=<?= $animal['id'] ?>" class="btn btn-success btn-sm">Sim</a>
        <button class="btn btn-secondary btn-sm" onclick="cancelarConfirmacaoResgate(<?= $animal['id'] ?>)">Cancelar</button>
    </div>
</div>

                    
                    <button class="btn btn-outline-danger w-100" onclick="mostrarConfirmacaoExclusao(<?= $animal['id'] ?>)">
                        <i class="bi bi-trash"></i> Excluir
                    </button>

                    <div id="confirmar<?= $animal['id'] ?>" class="mt-2 d-none">
                        <p class="text-danger fw-bold">Deseja excluir?</p>

                       <a href="excluir_animal.php?id=<?= $animal['id'] ?>&resgate=1" class="btn btn-success btn-sm">Sim</a>
                        <button class="btn btn-secondary btn-sm" onclick="cancelarConfirmacaoExclusao(<?= $animal['id'] ?>)">Cancelar</button>
                    </div>

                </div>
            </div>
        </div>

        <!-- MODAL -->
        <div class="modal fade" id="modal<?= $animal['id'] ?>">
            <div class="modal-dialog modal-dialog-centered">
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
                            <li class="list-group-item"><strong>Nome:</strong> <?= $animal['nome'] ?></li>
                            <li class="list-group-item"><strong>Situação:</strong> <?= $animal['situacao'] ?></li>
                            <li class="list-group-item"><strong>Espécie:</strong> <?= $animal['especie'] ?></li>
                            <li class="list-group-item"><strong>Gênero:</strong> <?= $animal['genero'] ?></li>
                            <li class="list-group-item"><strong>Cor:</strong> <?= $animal['cor_predominante'] ?></li>
                            <li class="list-group-item"><strong>Idade:</strong> <?= $animal['idade'] ?></li>
                            <li class="list-group-item"><strong>Descrição:</strong><br><?= nl2br($animal['descricao']) ?></li>
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

<!-- RODAPÉ -->
<footer class="text-center mt-auto py-3 bg-light">
    <p class="text-muted mb-0">&copy; <?= date('Y') ?> Rastreia Bicho 🐾</p>
</footer>

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
<script>
function mostrarConfirmacaoResgate(id) {
    document.getElementById("confirmarResgate" + id).classList.remove("d-none");
}

function cancelarConfirmacaoResgate(id) {
    document.getElementById("confirmarResgate" + id).classList.add("d-none");
}
</script>

</body>
</html>
