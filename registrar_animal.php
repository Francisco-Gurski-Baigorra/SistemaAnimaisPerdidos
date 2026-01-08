<?php
session_start();
// Conexão procedural simples
include('conecta.php');

if (!isset($_SESSION['usuario_id'])) {
    echo "<script>alert('Você precisa estar logado para registrar um animal.'); window.location='login.php';</script>";
    exit;
}

$racas = [];
$sql = "SELECT id, racas FROM racas ORDER BY racas";

$res = mysqli_query($conexao, $sql); 
while ($row = mysqli_fetch_assoc($res)) {
    $racas[] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cadastro de Animal no Mapa</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<style>
    body { background-color: #ffffff; min-height: 100vh; margin: 0; font-family: Arial, sans-serif; display: flex; flex-direction: column; }
    .navbar { background-color: #179e46ff; padding: 1rem; border-bottom: 3px solid #2e3531ff; box-shadow: 0 2px 6px rgba(0,0,0,0.15); }
    .navbar-brand { font-weight: bold; font-size: 1.7rem; color: #2b2b2b !important; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
    .navbar .btn { padding: 7px 14px; border-radius: 8px; font-weight: 500; transition: 0.2s; }
    .content { padding: 18px; flex: 1; display: flex; flex-direction: column; }
    h2 { text-align: center; margin-top: 8px; color: #252121ff; font-weight: 700; }
    p { text-align: center; color: #4b4b4b; margin-bottom: 10px; }
    #map { height: 75vh; width: 100%; border-radius: 12px; border: 3px solid #1e201fff; }
    .popup-form { max-height: 420px; overflow-y: auto; width: 320px; }
    .popup-form label { font-weight: 600; margin-top: 6px; display: block; }
    .popup-form input, .popup-form select, .popup-form textarea { width: 100%; padding: 6px 8px; border: 1px solid #ccc; border-radius: 6px; margin-bottom: 8px; font-size: 14px; }
    .popup-form button { background: #4CAF50; color: white; border: none; padding: 8px 16px; border-radius: 8px; width: 100%; font-weight: 600; cursor: pointer; }
    .popup-form .btn-close-popup { background: #e0e0e0; color: #333; margin-top: 6px; }
    .footer-rastreia { background-color: #179e46ff; color: #333; text-align: center; padding: 12px; font-size: 0.95rem; font-weight: 600; border-top: 2px solid #2e3531ff; margin-top: 20px; }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fa-solid fa-paw"></i> RASTREIA BICHO
        </a>
        <div class="ms-auto">
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="registrar_animal.php" class="btn btn-dark me-2"><i class="bi bi-plus-circle"></i> Registrar Animal</a>
                <a href="perfil.php" class="btn btn-dark me-2"><i class="bi bi-person-circle"></i> Perfil</a>
                <a href="perfil_animais.php" class="btn btn-dark me-2"><i class="fa-solid fa-paw"></i> Meus Animais</a>
                <a href="logout.php" class="btn btn-danger"><i class="bi bi-box-arrow-right"></i> Sair</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-dark me-2">Login</a>
                <a href="cadastro.php" class="btn btn-dark">Registrar Conta</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="content">
    <h2>Registre o animal encontrado ou perdido</h2>
    <p>Clique no mapa a localização o qual você perdeu ou encontrou um animal.</p>
    <div id="map"></div>
</div>

<footer class="footer-rastreia">© 2025 Rastreia Bicho</footer>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
const todasRacas = <?php echo json_encode($racas, JSON_UNESCAPED_UNICODE); ?>;
const usuario_id = <?php echo $_SESSION['usuario_id']; ?>;

const racasCachorro = ['vira-lata', 'labrador', 'bulldog', 'pastor alemão', 'pincher', 'cimarron', 'husky', 'salsicha', 'golden'];
const racasGato = ['persa', 'siamês', 'sphynx'];

const map = L.map('map').setView([-29.78126, -57.10689], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

function abrirPopupForm(lat, lng) {
    const formHtml = `
    <div class="popup-form">
      <form id="formAnimal" enctype="multipart/form-data">
        <input type="hidden" name="latitude" value="${lat}">
        <input type="hidden" name="longitude" value="${lng}">
        <input type="hidden" name="usuario_id" value="${usuario_id}">

        <label>Nome do animal *</label>
        <input type="text" name="nome" required placeholder="Ex: Nome do animal ou Caracteristica">

        <label>Situação *</label>
        <select name="situacao" required>
            <option value="">Selecione</option>
            <option value="perdido">Perdido</option>
            <option value="encontrado">Encontrado</option>
        </select>

        <label>Espécie *</label>
        <select name="especie" id="especieSelect" required>
            <option value="">Selecione</option>
            <option value="cachorro">Cachorro</option>
            <option value="gato">Gato</option>
            <option value="outros">Outros</option>
        </select>

        <label>Raça *</label>
        <select name="raca_id" id="racaSelect" required>
            <option value="">Selecione a espécie primeiro</option>
        </select>

        <label>Gênero *</label>
        <select name="genero" required>
            <option value="macho">Macho</option>
            <option value="femea">Fêmea</option>
            <option value="nao_informado">Não informado</option>
        </select>

        <label>Data da ocorrência *</label>
        <input name="data_ocorrido" type="date" value="<?php echo date('Y-m-d'); ?>" required>

        <label>Seu telefone *</label>
        <input name="telefone_contato" id="tel" type="text" placeholder="(55) 99999-9999" required>

        <label>Foto do animal *</label>
        <input name="foto" type="file" accept="image/*" required>

        <label>Porte</label>
        <select name="porte">
            <option value="">Selecione</option>
            <option value="Pequeno">Pequeno</option>
            <option value="Medio">Médio</option>
            <option value="Grande">Grande</option>
        </select>

        <label>Cor Predominante</label>
        <select name="cor_predominante">
            <option value="">Selecione</option>
            <option value="Preto">Preto</option>
            <option value="Branco">Branco</option>
            <option value="Marrom">Marrom</option>
            <option value="Cinza">Cinza</option>
            <option value="Caramelo">Caramelo</option>
            <option value="Outros">Outros</option>
        </select>

        <label>Idade Aproximada</label>
        <select name="idade">
            <option value="">Selecione</option>
            <option value="Filhote">Filhote</option>
            <option value="Adulto">Adulto</option>
            <option value="Idoso">Idoso</option>
        </select>

        <label>Descrição (opcional)</label>
        <textarea name="descricao" rows="2"></textarea>

        <button type="button" id="btnSalvar">Cadastrar Animal</button>
        <button type="button" id="btnFechar" class="btn-close-popup">Cancelar</button>
      </form>
    </div>`;

    const popup = L.popup({ maxWidth: 360, closeOnClick: false }).setLatLng([lat, lng]).setContent(formHtml).openOn(map);

    setTimeout(() => {
        const especieSel = document.getElementById('especieSelect');
        const racaSel = document.getElementById('racaSelect');

        especieSel.addEventListener('change', function() {
            const esp = this.value;
            racaSel.innerHTML = '<option value="">-- Selecione a raça --</option>';

            todasRacas.forEach(r => {
                const nomeR = r.racas.toLowerCase();
                let mostrar = false;

                if (esp === 'cachorro' && (racasCachorro.includes(nomeR) || nomeR === 'outros')) mostrar = true;
                if (esp === 'gato' && (racasGato.includes(nomeR) || nomeR === 'outros')) mostrar = true;
                if (esp === 'outros' && nomeR === 'outros') mostrar = true;

                if (mostrar) {
                    racaSel.innerHTML += `<option value="${r.id}">${r.racas}</option>`;
                }
            });

            if (esp === 'outros') {
                const rOutros = todasRacas.find(r => r.racas.toLowerCase() === 'outros');
                if (rOutros) racaSel.value = rOutros.id;
            }
        });

        document.getElementById('tel').addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length > 11) v = v.substr(0, 11);
            v = v.replace(/^(\d{2})(\d)/g, '($1) $2');
            v = v.replace(/(\d{5})(\d)/, '$1-$2');
            e.target.value = v;
        });

        document.getElementById('btnSalvar').addEventListener('click', function() {
            const formData = new FormData(document.getElementById('formAnimal'));
            fetch('salvar_local.php', { method: 'POST', body: formData })
            .then(r => r.text())
            .then(msg => {
                alert(msg.trim());
                if (msg.toLowerCase().includes('sucesso')) location.reload();
            });
        });

        document.getElementById('btnFechar').addEventListener('click', () => map.closePopup());
    }, 200);
}

map.on('click', e => abrirPopupForm(e.latlng.lat.toFixed(6), e.latlng.lng.toFixed(6)));
</script>
</body>
</html>