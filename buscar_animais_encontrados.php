<?php
session_start();
include('conecta.php');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>

<!-- Pacote de emojis -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 

<!-- Bootstrap e ícones -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<meta charset="UTF-8">
<title>Mapa - Animais Encontrados</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<style>
body, html {
    height: 100%;
    margin: 0;
    background-color: #ffffffff;
    display: flex;
    flex-direction: column;
}

/* Barra superior */
.navbar {
    background-color: #179e46ff;
    padding: 1rem;
    border-bottom: 3px solid #2e3531ff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

/* Filtros */
.filter-container {
    background: #ffffffff;
    padding: 12px 20px;
    margin: 8px auto;
    width: 96%;
    max-width: 1200px;
    position: relative;
    z-index: 999;
}

/* Mapa */
#map {
    flex: 1;
    width: 100%;
    margin: 0;
    border-radius: 10px;
    border: 3px solid #2e3531ff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

/* Popups */
.popup-img {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 5px;
    border: 2px solid #2e3531ff;
}

.info-popup {
    font-size: 14px;
    line-height: 1.4;
    max-width: 260px;
    padding: 5px 2px;
}

.leaflet-popup-content-wrapper {
    border: 2px solid #2e3531ff;
    border-radius: 12px;
    background-color: #ffffff;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}

.leaflet-popup-tip {
    background-color: #ffffff;
    border: 2px solid #2e3531ff;
}
</style>

</head>
<body>

<!-- 🔵 NAVBAR PADRÃO -->
<nav class="navbar navbar-expand-lg">
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
                    <i class="fa-solid fa-paw"></i> Meus Animais
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


<!-- 🔵 FILTROS PADRÃO -->
<div class="filter-container mb-3">
  <div class="d-flex flex-wrap justify-content-center align-items-center gap-2">

    <select id="filtroEspecie" class="form-select form-select-sm w-auto">
      <option value="">Espécie</option>
      <option value="cachorro">Cachorro</option>
      <option value="gato">Gato</option>
      <option value="outros">Outro</option>
    </select>

    <select id="filtroRaca" class="form-select form-select-sm w-auto">
      <option value="">Raça</option>
      <option value="vira-lata">Vira-lata</option>
      <option value="poodle">Poodle</option>
      <option value="pitbull">Pitbull</option>
      <option value="labrador">Labrador</option>
      <option value="siamês">Siamês</option>
      <option value="persa">Persa</option>
      <option value="outros">Outros</option>
    </select>

    <select id="filtroGenero" class="form-select form-select-sm w-auto">
      <option value="">Gênero</option>
      <option value="macho">Macho</option>
      <option value="femea">Fêmea</option>
      <option value="nao_informado">Não informado</option>
    </select>

    <select id="filtroPorte" class="form-select form-select-sm w-auto">
      <option value="">Porte</option>
      <option value="pequeno">Pequeno</option>
      <option value="medio">Médio</option>
      <option value="grande">Grande</option>
    </select>

    <select id="filtroCor" class="form-select form-select-sm w-auto">
      <option value="">Cor</option>
      <option value="preto">Preto</option>
      <option value="branco">Branco</option>
      <option value="marrom">Marrom</option>
      <option value="cinza">Cinza</option>
      <option value="caramelo">Caramelo</option>
      <option value="preto e branco">Preto e Branco</option>
      <option value="outros">Outros</option>
    </select>

    <select id="filtroIdade" class="form-select form-select-sm w-auto">
      <option value="">Idade</option>
      <option value="Filhote">Filhote</option>
      <option value="Adulto">Adulto</option>
      <option value="Idoso">Idoso</option>
    </select>

    <button id="btnFiltrar" class="btn btn-success btn-sm">🔍 Filtrar</button>

  </div>
</div>

<!-- MAPA -->
<div id="map"></div>

<!-- MODAL DE CONTATO -->
<div class="modal fade" id="contatoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Contato do Responsável</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contatoModalBody"></div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>


<!-- SCRIPTS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
const isLogged = <?= isset($_SESSION['usuario_id']) ? 'true' : 'false' ?>;

const map = L.map('map').setView([-29.78126, -57.10689], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

let marcadores = [];

// PADRÃO N/A
function info(valor) {
    return valor && valor.trim() !== "" ? escapeHtml(valor) : "N/A";
}

async function carregarAnimais() {
    try {
        const res = await fetch('carregar_encontrados.php');
        const animais = await res.json();

        marcadores.forEach(m => map.removeLayer(m));
        marcadores = [];

        const especie = filtro('filtroEspecie');
        const raca    = filtro('filtroRaca');
        const genero  = filtro('filtroGenero');
        const porte   = filtro('filtroPorte');
        const cor     = filtro('filtroCor');
        const idade   = filtro('filtroIdade');

        const filtrados = animais.filter(a => {
            return (!especie || a.especie?.toLowerCase() === especie) &&
                   (!raca || (a.raca_nome || "").toLowerCase().includes(raca)) &&
                   (!genero || a.genero?.toLowerCase() === genero) &&
                   (!porte || a.porte?.toLowerCase() === porte) &&
                   (!cor || (a.cor_predominante || "").toLowerCase().includes(cor)) &&
                   (!idade || a.idade?.toLowerCase() === idade);
        });

        if (filtrados.length === 0) {
            const alertBox = document.createElement("div");
            alertBox.className = "alert alert-warning text-center mt-2 w-75 mx-auto";
            alertBox.innerHTML = "<b>Nenhum animal encontrado com esses filtros.</b>";
            document.body.prepend(alertBox);
            setTimeout(() => alertBox.remove(), 4000);
            return;
        }

        filtrados.forEach(a => {
            if (!a.latitude || !a.longitude) return;

            const icone = L.icon({
                iconUrl: 'https://cdn-icons-png.flaticon.com/512/616/616408.png',
                iconSize: [36,36]
            });

            const popupHtml = `
                <div class="info-popup">
                  <b>${info(a.nome)}</b><br>
                  <b>Espécie:</b> ${info(a.especie)}<br>
                  <b>Raça:</b> ${info(a.raca_nome)}<br>
                  <b>Cor:</b> ${info(a.cor_predominante)}<br>
                  <b>Gênero:</b> ${info(a.genero)}<br>
                  <b>Idade:</b> ${info(a.idade)}<br>
                  <b>Porte:</b> ${info(a.porte)}<br>
                  <b>Data:</b> ${a.data_ocorrido ? formatDate(a.data_ocorrido) : "N/A"}<br>
                  <b>Descrição:</b> ${info(a.descricao)}<br><br>

                  ${a.foto ? `<img src="uploads/${encodeURI(a.foto)}" class="popup-img" alt="foto">` : '<span class="text-muted">Sem foto</span>'}

                  <div class="d-grid mt-2">
                    <button class="btn btn-sm btn-outline-primary" onclick="verContato(${a.usuario_id})">
                      Ver contato do responsável
                    </button>
                  </div>
                </div>
            `;

            const marker = L.marker([a.latitude, a.longitude], { icon: icone })
                            .addTo(map)
                            .bindPopup(popupHtml);

            marcadores.push(marker);
        });

    } catch (err) {
        console.error(err);
        alert('Erro ao carregar animais');
    }
}

function filtro(id) {
    return document.getElementById(id).value.toLowerCase();
}

document.getElementById('btnFiltrar').addEventListener('click', carregarAnimais);
carregarAnimais();

async function verContato(usuarioId) {
    if (!isLogged) {
        showContatoModal(`
            <p>Você precisa estar <strong>logado</strong> para visualizar o contato.</p>
            <div class="d-flex justify-content-center gap-2">
                <a href="login.php" class="btn btn-primary">Entrar</a>
                <a href="cadastro.php" class="btn btn-success">Cadastrar</a>
            </div>
        `);
        return;
    }

    try {
        const res = await fetch(`owner_info.php?usuario_id=${usuarioId}`);

        if (res.status === 200) {
            const data = await res.json();

            showContatoModal(`
                <p><strong>Nome:</strong> ${info(data.nome)}</p>
                <p><strong>Telefone:</strong> ${info(data.telefone)}</p>
                <p><strong>Email:</strong> ${info(data.email)}</p>
                <div class="text-muted small">Respeite a privacidade ao contatar o responsável.</div>
            `);
        } else {
            showContatoModal('<p>Erro ao buscar dados.</p>');
        }

    } catch {
        showContatoModal('<p>Erro ao buscar contato.</p>');
    }
}

function showContatoModal(html) {
    document.getElementById('contatoModalBody').innerHTML = html;
    new bootstrap.Modal(document.getElementById('contatoModal')).show();
}

function escapeHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

function formatDate(dateString) {
  if (!dateString) return 'N/A';
  const p = dateString.split('-');
  return `${p[2]}-${p[1]}-${p[0]}`;
}
</script>

</body>
</html>
