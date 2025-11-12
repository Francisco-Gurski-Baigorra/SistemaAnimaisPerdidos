<?php
session_start();
include('conecta.php');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Mapa de Animais Perdidos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

  <style>
    body, html {
      height: 100%;
      margin: 0;
      background-color: #81d181;
      display: flex;
      flex-direction: column;
    }

    .filter-container {
      background: white;
      padding: 10px 15px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      margin-bottom: 8px;
    }

    #map {
      flex: 1;
      width: 100%;
      border-radius: 10px;
      box-shadow: 0 0 8px rgba(0,0,0,0.1);
    }

    .popup-img {
      width: 150px;
      height: 150px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 5px;
    }

    .info-popup {
      font-size: 14px;
      line-height: 1.4;
    }
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
          <option value="outro">Outro</option>
        </select>

        <select id="filtroRaca" class="form-select form-select-sm w-auto">
          <option value="">Raça</option>
          <option value="vira-lata">Vira-lata</option>
          <option value="poodle">Poodle</option>
          <option value="pitbull">Pitbull</option>
          <option value="labrador">Labrador</option>
          <option value="siames">Siamês</option>
          <option value="persa">Persa</option>
          <option value="outros">Outros</option>
        </select>

        <select id="filtroGenero" class="form-select form-select-sm w-auto">
          <option value="">Gênero</option>
          <option value="macho">Macho</option>
          <option value="femea">Fêmea</option>
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
          <option value="filhote">Filhote</option>
          <option value="adulto">Adulto</option>
          <option value="idoso">Idoso</option>
        </select>

        <button id="btnFiltrar" class="btn btn-success btn-sm">🔍 Filtrar</button>
      </div>
    </div>
  </div>

  <div id="map"></div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    var map = L.map('map').setView([-29.78126, -57.10689], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19, attribution: '© OpenStreetMap'
    }).addTo(map);

    let marcadores = [];

    function carregarAnimais() {
      fetch('carregar_encontrados.php')
        .then(r => r.json())
        .then(animais => {
          marcadores.forEach(m => map.removeLayer(m));
          marcadores = [];

          const especie = filtroEspecie.value.toLowerCase();
          const raca = filtroRaca.value.toLowerCase();
          const genero = filtroGenero.value.toLowerCase();
          const porte = filtroPorte.value.toLowerCase();
          const cor = filtroCor.value.toLowerCase();
          const idade = filtroIdade.value.toLowerCase();

          animais.filter(a => {
            return (!especie || a.especie.toLowerCase() === especie) &&
                   (!raca || (a.raca_nome && a.raca_nome.toLowerCase().includes(raca))) &&
                   (!genero || a.genero.toLowerCase() === genero) &&
                   (!porte || a.porte.toLowerCase() === porte) &&
                   (!cor || a.cor_predominante.toLowerCase().includes(cor)) &&
                   (!idade || a.idade.toLowerCase() === idade);
          }).forEach(a => {
            if (!a.latitude || !a.longitude) return;

            const icone = L.icon({ iconUrl: 'https://cdn-icons-png.flaticon.com/512/616/616408.png', iconSize: [36, 36] });
            const popup = `
              <div class="info-popup">
                <b>${a.nome || 'Sem nome'}</b><br>
                <b>Espécie:</b> ${a.especie}<br>
                <b>Raça:</b> ${a.raca_nome || 'Não informada'}<br>
                <b>Cor:</b> ${a.cor_predominante || 'Não informada'}<br>
                <b>Gênero:</b> ${a.genero}<br>
                <b>Idade:</b> ${a.idade}<br>
                <b>Porte:</b> ${a.porte}<br>
                <b>Descrição:</b> ${a.descricao || 'Sem descrição'}<br>
                <b>Telefone:</b> ${a.telefone_contato}<br><br>
                ${a.foto ? `<img src="uploads/${a.foto}" class="popup-img">` : ''}
              </div>`;
            marcadores.push(L.marker([a.latitude, a.longitude], { icon: icone }).addTo(map).bindPopup(popup));
          });
        });
    }

    btnFiltrar.addEventListener('click', carregarAnimais);
    carregarAnimais();
  </script>
</body>
</html>
