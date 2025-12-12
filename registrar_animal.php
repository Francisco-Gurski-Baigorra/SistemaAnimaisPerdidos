<?php
session_start();
include('conecta.php');

if (!isset($_SESSION['usuario_id'])) {
    echo "<script>alert('Você precisa estar logado para registrar um animal.'); window.location='login.php';</script>";
    exit;
}

$racas = [];
$sql = "SELECT id, racas FROM racas ORDER BY racas";
$res = $conexao->query($sql);
while ($row = $res->fetch_assoc()) {
    $racas[] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cadastro de Animal no Mapa</title>

<!-- Bootstrap + Ícones -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<style>
   body {
    background-color: #ffffffff;
    min-height: 100vh;
    margin: 0;
    padding-top: 24px;
    font-family: Arial, sans-serif;
    padding-top: 0;
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


  /* ======================= AREA PRINCIPAL ======================= */
  .content {
    padding: 18px;
    flex: 1;
    display: flex;
    flex-direction: column;
  }

  h2 { text-align: center; margin-top: 8px; color: #252121ff; font-weight: 700; }
  p { text-align: center; color: #4b4b4b; margin-bottom: 10px; }

  /* ======================= MARCAÇÃO DO MAPA ======================= */
 .map-wrapper {
    width: 100%;             /* agora ocupa toda a largura */
    max-width: 100%;         /* remove limite */
    margin: 0;
    padding: 0;
    border-radius: 0;        /* opcional: deixa o mapa encostar nas bordas */
    background: none;        /* elimina a caixa branca */
    border: none;
    box-shadow: none;
}


  .map-header {
    display:flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
  }

  .map-title {
    font-weight: 700;
    color: #155724;
  }

  #map {
    height: 78vh;
    width: 100%;
    border-radius: 12px;     /* opcional */
    border: 3px solid #1e201fff;  /* COR + ESPESSURA */
}



  .leaflet-popup-content-wrapper {
    border-radius: 12px;
    background: #ffffff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.25);
  }

  .popup-form {
    max-height: 420px;
    overflow-y: auto;
    width: 320px;
  }

  .popup-form label {
    font-weight: 600;
    margin-top: 6px;
    display: block;
  }

  .popup-form input,
  .popup-form select,
  .popup-form textarea {
    width: 100%;
    padding: 6px 8px;
    border: 1px solid #ccc;
    border-radius: 6px;
    margin-bottom: 8px;
    font-size: 14px;
  }

  .popup-form button {
    background: #4CAF50;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    width: 100%;
    font-weight: 600;
    transition: background 0.3s;
  }

  .popup-form .btn-close-popup {
    background: #e0e0e0;
    color: #333;
    margin-top: 6px;
  }

  .popup-form button:hover { background: #388e3c; }

  /* responsividade */
  @media (max-width: 576px) {
    #map { height: 55vh; }
    .popup-form { width: 260px; }
  }
</style>
</head>
<body>



<!-- NAVBAR PADRÃO -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">
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
                    <i class="fa-solid fa-paw"></i><i class="bi bi-paw-fill me-2"></i> Meus Animais
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

<!-- CONTEÚDO -->
<div class="content">
  <h2>Registre o animal encontrado ou perdido</h2>
  <p>Clique no mapa para marcar o local e cadastrar o animal.</p>

  <div class="map-wrapper">
    <div class="map-header">

    </div>

    <div id="map"></div>
  </div>
</div>

<!-- SCRIPTS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
const racas = <?php echo json_encode($racas, JSON_UNESCAPED_UNICODE); ?>;
const usuario_id = <?php echo $_SESSION['usuario_id']; ?>;

// inicializa o mapa
const map = L.map('map').setView([-29.78126, -57.10689], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

// variável que controla se já existe um popup/form aberto
let popupOpen = false;
let currentPopup = null;

// quando qualquer popup do mapa for fechado, atualiza o estado
map.on('popupclose', function() {
  popupOpen = false;
  currentPopup = null;
});

// monta opções de raças (função reutilizável)
function montarOpcoesRacas() {
  let opt = '<option value="">-- Selecione a raça --</option>';
  racas.forEach(r => {
    opt += `<option value="${r.id}">${r.racas}</option>`;
  });
  return opt;
}

// função que cria e abre o popup-form no ponto clicado
function abrirPopupForm(lat, lng) {
  const racasOptions = montarOpcoesRacas();

  const formHtml = `
    <div class="popup-form">
      <form id="formAnimal" enctype="multipart/form-data">
        <input type="hidden" name="latitude" value="${lat}">
        <input type="hidden" name="longitude" value="${lng}">
        <input type="hidden" name="usuario_id" value="${usuario_id}">


        <label>Nome do animal: <span style="color:red;">*</span></label>
        <input name="nome" type="text"rows="3" placeholder="Digite o nome do seu animal"></textarea>
        

        <label>Situação: <span style="color:red;">*</span></label>
        <select name="situacao">
            <option value="">Selecione</option>
            <option value="perdido">Perdido</option>
            <option value="encontrado">Encontrado</option>
        </select>

        <label>Espécie: <span style="color:red;">*</span></label>
        <select name="especie">
            <option value="">Selecione</option>
            <option value="cachorro">Cachorro</option>
            <option value="gato">Gato</option>
            <option value="outros">Outros</option>
        </select>

        <label>Gênero: <span style="color:red;">*</span></label>
        <select name="genero">
            <option value="">Selecione</option>
            <option value="macho">Macho</option>
            <option value="femea">Fêmea</option>
            <option value="nao_informado">Não informado</option>
        </select>

        <label>Raça: <span style="color:red;">*</span></label>
        <select name="raca_id">${racasOptions}</select>

        <label>Data que foi visto: <span style="color:red;">*</span></label>
        <input name="data_ocorrido" type="date" value="<?php echo date('Y-m-d'); ?>">

        <label>Seu telefone (com DDD): <span style="color:red;">*</span></label>
        <input name="telefone_contato" id="tel" type="text" maxlength="15" placeholder="(55) 99999-9999">

        <label>Foto do animal: <span style="color:red;">*</span></label>
        <input name="foto" type="file" accept="image/*">

        <label>Porte:</label>
        <select name="porte">
            <option value="">Selecione</option>
            <option value="pequeno">Pequeno</option>
            <option value="medio">Médio</option>
            <option value="grande">Grande</option>
        </select>

        <label>Cor Predominante:</label>
        <select name="cor_predominante">
            <option value="">Selecione</option>
            <option value="preto">Preto</option>
            <option value="branco">Branco</option>
            <option value="marrom">Marrom</option>
            <option value="cinza">Cinza</option>
            <option value="caramelo">Caramelo</option>
            <option value="tricolor">Tricolor</option>
        </select>

        <label>Idade aproximada:</label>
        <select name="idade">
            <option value="">Selecione</option>
            <option value="filhote">Filhote</option>
            <option value="adulto">Adulto</option>
            <option value="idoso">Idoso</option>
        </select>

        <label>Descrição (opcional):</label>
        <textarea name="descricao" rows="3" placeholder="Detalhes que ajudem na identificação..."></textarea>

        <button type="button" id="btnSalvar">Registrar</button>
        <button type="button" id="btnFechar" class="btn-close-popup">Fechar</button>
      </form>
    </div>
  `;

  const popup = L.popup({
    maxWidth: 360,
    closeOnClick: false,
    autoClose: false,
    className: 'custom-popup'
  })
    .setLatLng([lat, lng])
    .setContent(formHtml)
    .openOn(map);

  popupOpen = true;
  currentPopup = popup;

  // espera o DOM do popup existir para ligar eventos
  setTimeout(() => {
    const form = document.getElementById('formAnimal');
    const btnSalvar = document.getElementById('btnSalvar');
    const btnFechar = document.getElementById('btnFechar');

    // máscara de telefone
    const telInput = document.getElementById('tel');
    if (telInput) {
      telInput.addEventListener('input', function(e) {
        let v = e.target.value.replace(/\D/g, '');
        if (v.length > 11) v = v.substr(0, 11);
        v = v.replace(/^(\d{2})(\d)/g, '($1) $2');
        v = v.replace(/(\d{5})(\d)/, '$1-$2');
        e.target.value = v;
      });
    }

    // fechar popup pelo botão "Fechar"
    if (btnFechar) {
      btnFechar.addEventListener('click', () => {
        map.closePopup(popup);
        // popupclose event trata popupOpen = false
      });
    }

    // salvar (mesma lógica do seu salvarAnimal)
    if (btnSalvar) {
      btnSalvar.addEventListener('click', () => {
        // validações
        const obrigatorios = {
          nome: "Nome do animal",
          situacao: "Situação",
          especie: "Espécie",
          genero: "Gênero",
          raca_id: "Raça",
          data_ocorrido: "Data que foi visto",
          telefone_contato: "Telefone de contato"
        };

        for (let campo in obrigatorios) {
          if (!form[campo] || !form[campo].value || form[campo].value.trim() === '') {
            alert(`Preencha o campo obrigatório: ${obrigatorios[campo]}`);
            if (form[campo]) form[campo].focus();
            return;
          }
        }

        const tel = form.telefone_contato.value.replace(/\D/g, '');
        if (tel.length !== 11) {
          alert("Telefone deve ter 11 dígitos (ex: 55999999999)");
          form.telefone_contato.focus();
          return;
        }

        if (!form.foto.files || form.foto.files.length === 0) {
          alert("Selecione uma foto do animal");
          return;
        }

        // envio por fetch
        const formData = new FormData(form);

        fetch('salvar_local.php', {
          method: 'POST',
          body: formData
        })
        .then(r => r.text())
        .then(msg => {
          alert(msg.trim());
          if (msg.toLowerCase().includes('sucesso') || msg.toLowerCase().includes('cadastrado')) {
            map.closePopup(popup); // fecha o popup atual e atualiza estado
            location.reload();
          }
        })
        .catch(() => alert('Erro de conexão'));
      });
    }

  }, 100); // pequeno delay para garantir que o popup DOM foi injetado
}

// evento de clique no mapa: abre popup somente se NÃO houver outro aberto
map.on('click', function(e) {
  if (popupOpen) {
    // já existe um formulário aberto: não fazer nada
    // opcional: mostrar uma mensagem sutil
    // alert('Feche o formulário aberto para criar outro.');
    return;
  }
  abrirPopupForm(e.latlng.lat.toFixed(6), e.latlng.lng.toFixed(6));
});


</script>


</body>
</html>
