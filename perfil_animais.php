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

// ✅ Buscar os animais cadastrados pelo usuário (agora incluindo latitude e longitude)
$sqlAnimais = "
SELECT 
    id,
    nome,
    situacao,
    especie,
    genero,
    cor_predominante,
    idade,
    descricao,
    data_ocorrido,
    foto,
    latitude,
    longitude
FROM animais
WHERE usuario_id = ?
";

$stmt2 = $conexao->prepare($sqlAnimais);
$stmt2->bind_param("i", $id_usuario);
$stmt2->execute();
$resultAnimais = $stmt2->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
   <!-- icone de anomal -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 
<meta charset="UTF-8">
<title>Meus Animais - Rastreia Bicho 🐾</title>

<!-- icone de perfil -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
body {
  background-color: #b6e388;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.navbar {
  background-color: #179e46ff;
}
 /* Barra superior */
    .navbar {
        background-color: #179e46ff;
        padding: 1rem;

        border-bottom: 3px solid #2e3531ff; /* borda mais escura */
        box-shadow: 0 2px 6px rgba(0,0,0,0.15); /* somhra só pra enfeite */
    }
    
.navbar-brand {
  font-weight: bold;
  color: #2b2b2b !important;
}

.navbar + * {
    margin-top: 30px; /* ajuste como quiser */
}


.card {
  border-radius: 15px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}

.card img {
  height: 200px;
  object-fit: cover;
  border-radius: 15px 15px 0 0;
}

.btn-success {
  background-color: #179e46ff;
  border: none;
}

.btn-success:hover {
  background-color: #12843b;
}

/* Estilo do mini mapa */
[id^="map"] {
  height: 200px;
  border-radius: 10px;
  border: 1px solid #ccc;
  overflow: hidden;
}
</style>
</head>
<body>
<!-- ✅ Barra superior -->

<nav class="navbar navbar-expand-lg" style="background-color: #179e46ff; padding: 1rem;">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3 text-dark" href="index.php">
            <i class="fa-solid fa-paw"></i><i class="bi bi-paw-fill me-2"></i> RASTREIA BICHO
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
                    <i class="fa-solid fa-paw"></i><i class="bi bi-paw-fill me-2"></i> Animais Registrados
                </a>

                <a href="logout.php" class="btn btn-danger me-2">
                    <i class="bi bi-box-arrow-right"></i> Sair
                </a>

            <?php else: ?>

                <a href="login.php" class="btn btn-dark me-2">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </a>

                <a href="cadastro.php" class="btn btn-dark me-2">
                    <i class="bi bi-person-plus"></i> Registrar Conta
                </a>

            <?php endif; ?>
        </div>
    </div>
</nav>


<!-- 🐾 Conteúdo principal -->
<div class="container mb-5">
  <div class="text-center mb-4">
    <h2 class="fw-bold text-dark">Olá, <?= htmlspecialchars($usuario['nome']) ?>!</h2>
    <p class="text-muted">Aqui estão os animais que você registrou:</p>
  </div>

  <?php if ($resultAnimais->num_rows > 0): ?>
    <div class="row justify-content-center g-4">
      <?php while ($animal = $resultAnimais->fetch_assoc()): ?>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex justify-content-center">
          <div class="card shadow-sm" style="width: 18rem;">
            <?php if (!empty($animal['foto'])): ?>
              <img src="uploads/<?= htmlspecialchars($animal['foto']) ?>" class="card-img-top" alt="Foto do animal">
            <?php else: ?>
              <div class="bg-light text-center py-5 text-muted">Sem foto</div>
            <?php endif; ?>

            <div class="card-body text-center">
              <h5 class="card-title"><?= htmlspecialchars($animal['nome']) ?></h5>
              <p class="card-text mb-3"><?= htmlspecialchars($animal['situacao']) ?></p>
              
              <button class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#detalhesModal<?= $animal['id'] ?>">
                <i class="bi bi-info-circle"></i> Ver Detalhes
              </button>
              <a href="editar_animal.php?id=<?= $animal['id'] ?>" class="btn btn-outline-primary w-100 mb-2">
                <i class="bi bi-pencil"></i> Editar
              </a>
              <!-- Botão que mostra a confirmação -->
<button class="btn btn-outline-danger w-100" 
        onclick="mostrarConfirmacaoExclusao(<?= $animal['id'] ?>)">
  <i class="bi bi-trash"></i> Excluir
</button>

<!-- Área de confirmação (escondida por padrão) -->
<div id="confirmar<?= $animal['id'] ?>" class="mt-2 d-none text-center">
  <p class="text-danger fw-bold mb-2">
    Deseja mesmo excluir este animal?
  </p>

  <a href="excluir_animal.php?id=<?= $animal['id'] ?>" 
     class="btn btn-danger w-45">
      Excluir
  </a>

  <button class="btn btn-secondary w-45 ms-1"
          onclick="cancelarConfirmacaoExclusao(<?= $animal['id'] ?>)">
          Cancelar
  </button>
</div>

            </div>
          </div>
        </div>

        <!-- Modal de Detalhes -->
        <div class="modal fade" id="detalhesModal<?= $animal['id'] ?>" tabindex="-1" aria-labelledby="detalhesLabel<?= $animal['id'] ?>" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="detalhesLabel<?= $animal['id'] ?>">Detalhes do Animal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
              </div>
              <div class="modal-body">
  <div class="text-center mb-3">
    <?php if (!empty($animal['foto'])): ?>
      <img src="uploads/<?= htmlspecialchars($animal['foto']) ?>" class="img-fluid rounded mb-3" style="max-height: 200px; object-fit: cover;">
    <?php else: ?>
      <div class="text-muted fst-italic mb-3">Sem foto disponível</div>
    <?php endif; ?>
  </div>

  <ul class="list-group text-start mb-3">
    <li class="list-group-item"><strong>Nome:</strong> <?= htmlspecialchars($animal['nome'] ?? 'Não informado') ?></li>
    <li class="list-group-item"><strong>Situação:</strong> <?= htmlspecialchars($animal['situacao'] ?? 'Não informado') ?></li>
    <li class="list-group-item"><strong>Espécie:</strong> <?= htmlspecialchars($animal['especie'] ?? 'Não informado') ?></li>
    <li class="list-group-item"><strong>Gênero:</strong> <?= htmlspecialchars($animal['genero'] ?? 'Não informado') ?></li>
    <li class="list-group-item"><strong>Cor predominante:</strong> <?= htmlspecialchars($animal['cor_predominante'] ?? 'Não informado') ?></li>
    <li class="list-group-item"><strong>Idade:</strong> <?= htmlspecialchars($animal['idade'] ?? 'Não informado') ?></li>
   <li class="list-group-item">
  <strong>Data do ocorrido:</strong>
  <?php 
    if (!empty($animal['data_ocorrido']) && $animal['data_ocorrido'] !== '0000-00-00') {
       echo date('d/m/Y', strtotime($animal['data_ocorrido']));

    } else {
        echo 'Não informado';
    }
  ?>
</li>
    <li class="list-group-item"><strong>Descrição:</strong><br><?= nl2br(htmlspecialchars($animal['descricao'] ?? 'Sem descrição')) ?></li>
  </ul>

  <!-- 🗺️ Mini Mapa -->
  <?php if (!empty($animal['latitude']) && !empty($animal['longitude'])): ?>
    <div class="mt-3">
      <h6 class="fw-bold mb-2"><i class="bi bi-geo-alt"></i> Local onde foi visto:</h6>
      <div id="map<?= $animal['id'] ?>" 
           data-lat="<?= htmlspecialchars($animal['latitude']) ?>" 
           data-lng="<?= htmlspecialchars($animal['longitude']) ?>">
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-secondary text-center">Localização não informada.</div>
  <?php endif; ?>
</div>

              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
              </div>
            </div>
          </div>
        </div>

      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <div class="alert alert-warning text-center mt-4">
      <i class="bi bi-info-circle"></i> Você ainda não cadastrou nenhum animal.
    </div>
  <?php endif; ?>
</div>

<!-- 🔙 Rodapé -->
<footer class="text-center mt-auto py-3 bg-light">
  <p class="mb-0 text-muted">&copy; <?= date('Y') ?> Rastreia Bicho 🐾 | Todos os direitos reservados</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- 🌍 Leaflet (Mapas) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('shown.bs.modal', function (event) {
  const modal = event.target;
  const mapDiv = modal.querySelector('div[id^="map"]');

  if (mapDiv && !mapDiv.dataset.mapLoaded) {
    const lat = parseFloat(mapDiv.getAttribute('data-lat'));
    const lng = parseFloat(mapDiv.getAttribute('data-lng'));

    if (!isNaN(lat) && !isNaN(lng)) {
      const map = L.map(mapDiv, { zoomControl: false }).setView([lat, lng], 15);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '© OpenStreetMap'
      }).addTo(map);

      L.marker([lat, lng]).addTo(map);

      setTimeout(() => {
        map.invalidateSize();
      }, 200);

      mapDiv.dataset.mapLoaded = "true";
    } else {
      mapDiv.innerHTML = '<div class="text-muted text-center p-3">Localização inválida.</div>';
    }
  }
});
</script>

<script>
function mostrarConfirmacaoExclusao(id) {
    document.getElementById("confirmar" + id).classList.remove("d-none");
}

function cancelarConfirmacaoExclusao(id) {
    document.getElementById("confirmar" + id).classList.add("d-none");
}
</script>



</body>
</html>
