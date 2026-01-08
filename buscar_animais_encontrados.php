<?php
session_start();
include('conecta.php');

$racas = [];
$sql_racas = "SELECT id, racas FROM racas ORDER BY racas";
$res_racas = mysqli_query($conexao, $sql_racas);
while ($row = mysqli_fetch_assoc($res_racas)) {
    $racas[] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Mapa - Rastreia Bicho</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <style>
        body, html {
            height: 100%;
            margin: 0;
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background-color: #179e46ff;
            padding: 1rem;
            border-bottom: 3px solid #2e3531ff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.7rem;
            color: #2b2b2b !important;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: transform 0.2s ease, opacity 0.2s ease;
            cursor: pointer;
            text-decoration: none;
        }

        .navbar-brand:hover {
            transform: translateY(-2px) scale(1.04);
            opacity: 0.9;
        }

        .filter-container {
            background: #ffffff;
            padding: 12px 20px;
            margin: 8px auto;
            width: 96%;
            max-width: 1200px;
            position: relative;
            z-index: 999;
        }

        #map {
            flex: 1;
            width: 100%;
            margin: 0;
            border-radius: 10px;
            border: 3px solid #2e3531ff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

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

        #aviso-vazio {
            display: none;
            position: absolute;
            top: 150px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            min-width: 300px;
            border: 2px solid #2e3531ff;
        }
        
        .status-badge {
            background-color: #179e46ff;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 11px;
            text-transform: uppercase;
            vertical-align: middle;
            margin-left: 5px;
        }
    </style>
</head>
<body>

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
      <?php foreach ($racas as $r): ?>
        <option value="<?php echo strtolower($r['racas']); ?>"><?php echo $r['racas']; ?></option>
      <?php endforeach; ?>
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

    <select id="filtroIdade" class="form-select form-select-sm w-auto">
      <option value="">Idade</option>
      <option value="Filhote">Filhote</option>
      <option value="Adulto">Adulto</option>
      <option value="Idoso">Idoso</option>
    </select>

    <button id="btnFiltrar" class="btn btn-success btn-sm">🔍 Filtrar</button>
  </div>
</div>

<div id="aviso-vazio" class="alert alert-warning alert-dismissible fade show text-center shadow" role="alert">
  <strong>Nenhum animal encontrado</strong> com esses filtros.
  <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'"></button>
</div>

<div id="map"></div>

<div class="modal fade" id="contatoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border: 3px solid #2e3531ff;">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Contato do Responsável</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center" id="contatoModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
const isLogged = <?= isset($_SESSION['usuario_id']) ? 'true' : 'false' ?>; //verificar se esta logado para poder acessar contato
const map = L.map('map').setView([-29.78126, -57.10689], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

let marcadores = [];

async function carregarAnimais() { //noap precisa recarregar pagina pra filtrar
    const res = await fetch('carregar_encontrados.php'); //pega os animais econtrados do outro codigo
    const animais = await res.json();

    marcadores.forEach(m => map.removeLayer(m)); // filtro dos marcadores
    marcadores = [];

    const fEsp = document.getElementById('filtroEspecie').value.toLowerCase();
    const fRac = document.getElementById('filtroRaca').value.toLowerCase();
    const fGen = document.getElementById('filtroGenero').value.toLowerCase();
    const fPor = document.getElementById('filtroPorte').value.toLowerCase();
    const fIda = document.getElementById('filtroIdade').value.toLowerCase();

    const filtrados = animais.filter(a => { //aplica o filtro de cada caractersitica
        return (!fEsp || a.especie.toLowerCase() === fEsp) &&
               (!fRac || a.raca_nome.toLowerCase().includes(fRac)) &&
               (!fGen || a.genero.toLowerCase() === fGen) &&
               (!fPor || a.porte.toLowerCase() === fPor) &&
               (!fIda || a.idade.toLowerCase() === fIda);
    });

    const aviso = document.getElementById('aviso-vazio'); // avisa casoa busca do filtro nao eocntre nada
    if (filtrados.length === 0) {
        aviso.style.display = 'block';
        setTimeout(() => { aviso.style.display = 'none'; }, 4000);
    } else {
        aviso.style.display = 'none';
    }

    filtrados.forEach(a => { //icone do animal para cada um cadastrado
        const icone = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/616/616408.png',
            iconSize: [36, 36]
        });

        const dataFormatada = a.data_ocorrido ? a.data_ocorrido.split('-').reverse().join('/') : "N/A"; //formatar a data

        //janela do formulario para preencher as informações com marcador 
        const popupHtml = ` 
            <div class="info-popup">
                <b style="font-size:16px;">${a.nome || 'N/A'} <span class="status-badge">${a.situacao}</span></b><br>
                <hr style="margin:5px 0; border-top:1px solid #2e3531ff;">
                <b>Espécie:</b> ${a.especie}<br>
                <b>Raça:</b> ${a.raca_nome}<br>
                <b>Gênero:</b> ${a.genero}<br>
                <b>Idade:</b> ${a.idade}<br>
                <b>Porte:</b> ${a.porte}<br>
                <b>Data:</b> ${dataFormatada}<br>
                <b>Descrição:</b> ${a.descricao || 'N/A'}<br><br>
                <img src="uploads/${a.foto}" class="popup-img">
                <div class="d-grid mt-2">
                    <button class="btn btn-sm btn-success" onclick="verContato(${a.usuario_id})">
                        Ver contato do responsável
                    </button>
                </div>
            </div>
        `;

        const m = L.marker([a.latitude, a.longitude], {icon: icone}).addTo(map).bindPopup(popupHtml);
        marcadores.push(m); //armazenando os marcadores
    });
}

//mostrar todo os animais no mapa, sem filtro
document.getElementById('btnFiltrar').addEventListener('click', carregarAnimais);
carregarAnimais();

//prepara o modal para visualizar o contato de outro usuario
async function verContato(id) {
    const modalBody = document.getElementById('contatoModalBody');
    const modalElement = document.getElementById('contatoModal');
    const instanciaModal = bootstrap.Modal.getOrCreateInstance(modalElement);

    // se nao estiver logado nostra as opcao de login
    if (!isLogged) {
        modalBody.innerHTML = `
            <p>Você precisa estar <strong>logado</strong> para visualizar o contato.</p>
            <div class="d-flex justify-content-center gap-2 mt-3">
                <a href="login.php" class="btn btn-dark">Entrar</a>
                <a href="cadastro.php" class="btn btn-success">Cadastrar</a>
            </div>
        `;
        instanciaModal.show();
        return; 
    }

    // mostra os dados de contato caso esteja logado
    modalBody.innerHTML = '<div class="spinner-border text-success"></div><p>Carregando...</p>';
    instanciaModal.show();

    try {
        const res = await fetch('owner_info.php?usuario_id=' + id); // esperar o servidor responder antes de avancar
        if (!res.ok) throw new Error("Erro ao buscar dados"); // maldito erro 404
        const data = await res.json(); // se der certro trransofrma o conteudo em um  onjetivo json

            //verifica se nao retornou algum erro
        if (data.error) {
            modalBody.innerHTML = `<p class="text-danger">Acesso negado ou erro no servidor.</p>`;
            return;
        }

        // preenche o modal com as informaçoes do php
        modalBody.innerHTML = `
            <p><strong>Nome:</strong> ${data.nome || 'Não informado'}</p>
            <p><strong>Telefone:</strong> ${data.telefone || 'Não informado'}</p>
            <p><strong>Email:</strong> ${data.email || 'Não informado'}</p>
            <div class="text-muted small mt-3">Respeite a privacidade ao contatar o responsável.</div>
        `;

    //aviso se houver algum erro no try
    } catch (erro) {
        console.error("Erro na requisição:", erro);
        modalBody.innerHTML = `<p class="text-danger">Erro ao carregar contato. Tente novamente.</p>`;
    }
}
</script>
</body>
</html>