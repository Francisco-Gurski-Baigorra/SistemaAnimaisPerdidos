<?php
session_start();
include('conecta.php');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Mapa - Animais Perdidos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <style>
    body, html { height:100%; margin:0; background-color:#81d181; display:flex; flex-direction:column; }
    .filter-container { background:white; padding:10px 15px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.08); margin-bottom:8px; }
    #map { flex:1; width:100%; border-radius:10px; box-shadow:0 0 8px rgba(0,0,0,0.1); }
    .popup-img { width:150px; height:150px; object-fit:cover; border-radius:8px; margin-bottom:5px; }
    .info-popup { font-size:14px; line-height:1.4; max-width:260px; }
  </style>
</head>
<body>
  <div class="container-fluid py-3">
    <div class="text-center mb-2">
      <h2 class="fw-bold">🐾 Mapa de Animais Perdidos</h2>
      <p class="text-muted mb-2">Use os filtros abaixo para refinar sua busca:</p>
    </div>

    <div class="filter-container mb-3">
      <div class="d-flex flex-wrap justify-content-center align-items-center gap-2">
        <a href="index.php" class="btn btn-outline-success btn-sm">⬅️ Voltar</a>

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
  </div>

  <div id="map"></div>

  <!-- Modal para mostrar contato / aviso -->
  <div class="modal fade" id="contatoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title">Contato do Responsável</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="contatoModalBody">
          <!-- preenchido dinamicamente -->
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Variáveis vindas do PHP
    const isLogged = <?= isset($_SESSION['usuario_id']) ? 'true' : 'false' ?>;
    const currentUserId = <?= isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 'null' ?>;

    const map = L.map('map').setView([-29.78126, -57.10689], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

    let marcadores = [];

    async function carregarAnimais() {
      try {
        const res = await fetch('carregar_perdidos.php');
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

          // Monta conteúdo do popup com botão para ver contato
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

    // Função para abrir modal com contato (faz fetch para endpoint seguro)
    async function verContato(usuarioId) {
      if (!isLogged) {
        // mostra modal pedindo cadastro/login
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
      document.getElementById('contatoModalBody').innerHTML = html;
      const modal = new bootstrap.Modal(document.getElementById('contatoModal'));
      modal.show();
    }

    // utilitários
    function escapeHtml(unsafe) {
      if (unsafe === null || unsafe === undefined) return '';
      return String(unsafe)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
    }

    function formatDate(dateString) {
      if (!dateString) return 'Não informado';
      // espera 'YYYY-MM-DD' ou null
      const parts = dateString.split('-');
      if (parts.length === 3 && parts[0].length === 4) {
        return `${parts[2]}-${parts[1]}-${parts[0]}`;
      }
      return dateString;
    }
  </script>
</body>
</html>
