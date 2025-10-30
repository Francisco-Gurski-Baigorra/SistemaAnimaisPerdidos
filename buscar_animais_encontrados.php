<?php
session_start();
include('conecta.php');

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Mapa de Animais Encontrados</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
#map { height: 600px; width: 100%; margin-top: 10px; }
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

<h2>🐾 Mapa de Animais Encontrados</h2>
<p>Veja no mapa os animais marcados como <b>encontrados</b>.</p>

<div id="map"></div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
// Inicializa o mapa
var map = L.map('map').setView([-29.78126, -57.10689], 13);

// Camada do mapa OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 19,
  attribution: '© OpenStreetMap'
}).addTo(map);

// Carrega os animais encontrados do banco
fetch('carregar_encontrados.php')
.then(r => r.json())
.then(animais => {
    animais.forEach(a => {
        if (!a.latitude || !a.longitude) return;

        var icone = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/616/616408.png', // ícone de animal
            iconSize: [36, 36]
        });

        var popupContent = `
            <div class="info-popup">
                <b>${a.nome || 'Sem nome'}</b><br>
                <b>Espécie:</b> ${a.especie}<br>
                <b>Raça:</b> ${a.raca_nome || 'Não informada'}<br>
                <b>Cor:</b> ${a.cor_predominante || 'Não informada'}<br>
                <b>Gênero:</b> ${a.genero}<br>
                <b>Idade:</b> ${a.idade}<br>
                <b>Porte:</b> ${a.porte}<br>
                <b>Data do ocorrido:</b> ${a.data_ocorrido || '—'}<br>
                <b>Descrição:</b> ${a.descricao || 'Sem descrição'}<br>
                <b>Telefone:</b> ${a.telefone_contato}<br><br>
                ${a.foto ? `<img src="uploads/${a.foto}" class="popup-img">` : ''}
                <br><i>Registrado em: ${a.data_cadastro}</i>
            </div>
        `;

        L.marker([a.latitude, a.longitude], {icon: icone})
            .addTo(map)
            .bindPopup(popupContent);
    });
})
.catch(err => {
    alert("Erro ao carregar os animais: " + err);
});
</script>

</body>
</html>
