<?php
session_start();
include('conecta.php');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>

<!-- Pacote de emojis de anomal -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 

<!-- icone de perfil -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <meta charset="UTF-8">
  <title>Mapa - Animais Encontrados</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <style>

/* --- IMPEDIR QUE A IMAGEM DO POPUP EXPLODA --- */
.popup-img {
    max-width: 250px;    /* largura máxima */
    max-height: 250px;   /* altura máxima */
    width: 100%;         /* ocupa só o espaço possível */
    height: auto;
    border-radius: 10px;
    display: block;
    margin: 10px auto;
    object-fit: contain; /* mantém proporção sem estourar */
}



    /* Remove margem e espaço */
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

        border-bottom: 3px solid #2e3531ff; /* borda mais escura */
        box-shadow: 0 2px 6px rgba(0,0,0,0.15); /* somhra só pra enfeite */
    }
/* Filtros colados ao mapa */
.filter-container {
    background: #ffffffff;
    padding: 12px 20px;
    
    
    margin: 8px auto;          /* aproxima do mapa */
    width: 96%;
    max-width: 1200px;


    position: relative;
    z-index: 999; /* garante que fique sempre acima do mapa */
    
}

/* Mapa colado nos filtros */
#map {
    flex: 1;
    width: 100%;
    margin: 0;
    border-radius: 10px;             /* opcional */
    border: 3px solid #2e3531ff;     /* borda elegante */
    box-shadow: 0 2px 8px rgba(0,0,0,0.15); /* leve sombra */
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

                <a href="registrar_animal.php" class="btn btn-outline-dark me-2">
                    <i class="bi bi-plus-circle"></i> Registrar Animal
                </a>

                <a href="perfil.php" class="btn btn-outline-dark me-2">
                    <i class="bi bi-person-circle"></i> Perfil
                </a>

                <a href="perfil_animais.php" class="btn btn-outline-dark me-2">
                    <i class="fa-solid fa-paw"></i><i class="bi bi-paw-fill me-2"></i> Animais Registrados
                </a>

                <a href="logout.php" class="btn btn-outline-danger me-2">
                    <i class="bi bi-box-arrow-right"></i> Sair
                </a>

            <?php else: ?>

                <a href="login.php" class="btn btn-outline-dark me-2">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </a>

                <a href="cadastro.php" class="btn btn-outline-dark me-2">
                    <i class="bi bi-person-plus"></i> Registrar Conta
                </a>

            <?php endif; ?>
        </div>
    </div>
</nav>
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

<!-- AGORA O MAPA VEM DIRETO AQUI, SEM DIV EXTRA QUEMBRANDO -->
<div id="map"></div>


  <!-- Modal para mostrar contato / aviso -->
  <div class="modal fade" id="contatoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title">Contato do Responsável</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="contatoModalBody"></div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const isLogged = <?= isset($_SESSION['usuario_id']) ? 'true' : 'false' ?>;
    const currentUserId = <?= isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 'null' ?>;

    const map = L.map('map').setView([-29.78126, -57.10689], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

    let marcadores = [];

    async function carregarAnimais() {
      try {
        const res = await fetch('carregar_encontrados.php');
        const animais = await res.json();

        marcadores.forEach(m => map.removeLayer(m));
        marcadores = [];

        const especie = document.getElementById('filtroEspecie').value.toLowerCase();
        const raca = document.getElementById('filtroRaca').value.toLowerCase();
        const genero = document.getElementById('filtroGenero').value.toLowerCase();
        const porte = document.getElementById('filtroPorte').value.toLowerCase();
        const cor = document.getElementById('filtroCor').value.toLowerCase();
        const idade = document.getElementById('filtroIdade').value.toLowerCase();

        animais.filter(a => {
          return (!especie || (a.especie && a.especie.toLowerCase() === especie)) &&
                 (!raca || (a.raca_nome && a.raca_nome.toLowerCase().includes(raca))) &&
                 (!genero || (a.genero && a.genero.toLowerCase() === genero)) &&
                 (!porte || (a.porte && a.porte.toLowerCase() === porte)) &&
                 (!cor || (a.cor_predominante && a.cor_predominante.toLowerCase().includes(cor))) &&
                 (!idade || (a.idade && a.idade.toLowerCase() === idade));
        }).forEach(a => {
          if (!a.latitude || !a.longitude) return;

          const icone = L.icon({ iconUrl: 'https://cdn-icons-png.flaticon.com/512/616/616408.png', iconSize: [36,36] });

          const popupHtml = `
            <div class="info-popup">
              <b>${escapeHtml(a.nome) || 'Sem nome'}</b><br>
              <b>Espécie:</b> ${escapeHtml(a.especie || 'Não informada')}<br>
              <b>Raça:</b> ${escapeHtml(a.raca_nome || 'Não informada')}<br>
              <b>Cor:</b> ${escapeHtml(a.cor_predominante || 'Não informada')}<br>
              <b>Gênero:</b> ${escapeHtml(a.genero || 'Não informado')}<br>
              <b>Idade:</b> ${escapeHtml(a.idade || 'Não informada')}<br>
              <b>Porte:</b> ${escapeHtml(a.porte || 'Não informada')}<br>
              <b>Data:</b> ${formatDate(a.data_ocorrido)}<br>
              <b>Descrição:</b> ${escapeHtml(a.descricao || 'Sem descrição')}<br><br>
              ${a.foto ? `<img src="uploads/${encodeURI(a.foto)}" class="popup-img" alt="foto">` : ''}
              <div class="d-grid gap-2 mt-2">
                <button class="btn btn-sm btn-outline-primary" onclick="verContato(${a.usuario_id})">Ver contato do responsável</button>
              </div>
            </div>`;

          const marker = L.marker([a.latitude, a.longitude], { icon: icone }).addTo(map).bindPopup(popupHtml);
          marcadores.push(marker);
        });

      } catch (err) {
        console.error(err);
        alert('Erro ao carregar animais');
      }
    }

    document.getElementById('btnFiltrar').addEventListener('click', carregarAnimais);
    carregarAnimais();

    async function verContato(usuarioId) {
      if (!isLogged) {
        const body = `
          <p>Você precisa estar <strong>logado</strong> para visualizar o contato do responsável.</p>
          <div class="d-flex justify-content-center gap-2">
            <a href="login.php" class="btn btn-primary">Entrar</a>
            <a href="cadastro.php" class="btn btn-success">Cadastrar</a>
          </div>`;
        showContatoModal(body);
        return;
      }

      try {
        const res = await fetch(`owner_info.php?usuario_id=${encodeURIComponent(usuarioId)}`, { credentials: 'same-origin' });
        if (res.status === 200) {
          const data = await res.json();
          const body = `
            <p><strong>Nome:</strong> ${escapeHtml(data.nome || 'Não informado')}</p>
            <p><strong>Telefone:</strong> ${escapeHtml(data.telefone || 'Não informado')}</p>
            <p><strong>Email:</strong> ${escapeHtml(data.email || 'Não informado')}</p>
            <div class="text-muted small">Respeite a privacidade ao contatar o responsável.</div>
          `;
          showContatoModal(body);
        } else if (res.status === 403) {
          showContatoModal('<p>Acesso negado. Faça login para ver o contato.</p><div class="d-flex justify-content-center gap-2"><a href="login.php" class="btn btn-primary">Entrar</a><a href="cadastro.php" class="btn btn-success">Cadastrar</a></div>');
        } else {
          showContatoModal('<p>Não foi possível obter os dados do responsável.</p>');
        }
      } catch (err) {
        console.error(err);
        showContatoModal('<p>Erro ao buscar contato.</p>');
      }
    }

    function showContatoModal(html) {
      // cria modal se não existir
      let modalEl = document.getElementById('contatoModal');
      if (!modalEl) {
        modalEl = document.createElement('div');
        modalEl.className = 'modal fade';
        modalEl.id = 'contatoModal';
        modalEl.innerHTML = `<div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header bg-success text-white"><h5 class="modal-title">Contato do Responsável</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body" id="contatoModalBody"></div><div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button></div></div></div>`;
        document.body.appendChild(modalEl);
      }
      document.getElementById('contatoModalBody').innerHTML = html;
      const modal = new bootstrap.Modal(modalEl);
      modal.show();
    }

    function escapeHtml(unsafe) {
      if (unsafe === null || unsafe === undefined) return '';
      return String(unsafe)
        .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }
    function formatDate(dateString) {
      if (!dateString) return 'Não informado';
      const parts = dateString.split('-');
      if (parts.length === 3 && parts[0].length === 4) return `${parts[2]}-${parts[1]}-${parts[0]}`;
      return dateString;
    }
  </script>
</body>
</html>
