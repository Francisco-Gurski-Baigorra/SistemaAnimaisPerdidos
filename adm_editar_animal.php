<?php
session_start();
require 'conecta.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'administrador') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("ID do animal não informado!");
}

$animal_id = intval($_GET['id']);

/* BUSCA ANIMAL */
$sql = "SELECT * FROM animais WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $animal_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Animal não encontrado!");
}

$animal = $result->fetch_assoc();

/* BUSCA RAÇAS */
$racas = [];
$sqlRacas = "SELECT id, racas FROM racas ORDER BY racas";
$resRacas = $conexao->query($sqlRacas);
while ($r = $resRacas->fetch_assoc()) {
    $racas[] = $r;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Animal</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #ffffff;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
        }

        /* ======= Navbar ======= */
        .navbar-custom {
            background-color: #179e46;
            padding: 1rem 0;
            border-bottom: 3px solid #2e3531;
            box-shadow: 0 2px 6px rgba(54, 51, 51, 0.15);
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
}

.navbar-brand:hover {
    transform: translateY(-2px) scale(1.04);
    opacity: 0.9;
}


        /* ======= Footer ======= */
        .footer-rastreia {
            background-color: #179e46ff;
    color: #333;
    text-align: center;
    padding: 12px;
    font-size: 0.95rem;
    font-weight: 600;
    width: 100%;
    border-top: 2px solid #2e3531ff;
    margin-top: auto;
        }


        #map {
            height: 300px;
            border-radius: 8px;
        }

        .preview {
            width: 100%;
            max-width: 260px;
            height: 260px;
            object-fit: cover;
            border-radius: 12px;
            border: 2px solid #ccc;
        }

        /* Botão voltar bem pequeno e à esquerda */
        .btn-voltar {
            padding: 4px 12px;
            font-size: 0.85rem;
        }

        /* ESPAÇO EXTRA ANTES DO RODAPÉ */
        .conteudo-principal {
            padding-bottom: 80px; /* Aumente ou diminua esse valor conforme necessário */
        }
    </style>
</head>
<body>

    <!-- Navbar full width, conteúdo alinhado com o container -->
    <nav class="navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="admin.php">
                <i class="fa-solid fa-paw me-2"></i> RASTREIA BICHO
            </a>
        </div>
    </nav>

    <!-- Conteúdo principal com espaço extra no final -->
    <div class="container mt-4 conteudo-principal">

        <h2 class="mb-3"><i class="bi bi-pencil-fill"></i> Editar Animal</h2>

        <!-- Botão voltar pequeno e à esquerda -->
        <a href="gerenciar_animais.php" class="btn btn-secondary btn-voltar mb-4 d-inline-block">
            <i class="bi bi-arrow-left-circle"></i> Voltar
        </a>

        <form action="adm_salvar_edicao_animal.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $animal['id'] ?>">

            <div class="row">
                <!-- FOTO -->
                <div class="col-md-4 text-center">
                    <label class="fw-bold">Foto Atual</label> <span class="text-danger">*</span><br>
                    <?php if (!empty($animal['foto'])): ?>
                        <img src="uploads/<?= htmlspecialchars($animal['foto']) ?>" class="preview mb-2">
                    <?php else: ?>
                        <p class="text-muted">Sem foto</p>
                    <?php endif; ?>
                    <input type="file" name="foto" class="form-control mt-2">
                </div>

                <!-- DADOS -->
                <div class="col-md-8">
                    <label class="form-label">
                        Nome do animal <span class="text-muted">(ou breve descrição)</span>
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           name="nome"
                           class="form-control"
                           required
                           minlength="3"
                           value="<?= htmlspecialchars($animal['nome']) ?>"
                           maxlength="30">
                           

                    <label class="form-label mt-2">Situação</label> <span class="text-danger">*</span>
                    <select name="situacao" class="form-control" required>
    <option value="">Selecione</option>
    <option value="perdido" <?= $animal['situacao']=='perdido'?'selected':'' ?>>Perdido</option>
    <option value="encontrado" <?= $animal['situacao']=='encontrado'?'selected':'' ?>>Encontrado</option>
    <option value="resgatado" <?= $animal['situacao']=='resgatado'?'selected':'' ?>>Resgatado</option>
    </select>


                    <label class="form-label mt-2">Espécie </label> <span class="text-danger">*</span>
                    <select name="especie" class="form-control" required>
                        <option value="">Selecione</option>
                        <option value="cachorro" <?= $animal['especie']=='cachorro'?'selected':'' ?>>Cachorro</option>
                        <option value="gato" <?= $animal['especie']=='gato'?'selected':'' ?>>Gato</option>
                        <option value="outros" <?= $animal['especie']=='outros'?'selected':'' ?>>Outros</option>
                    </select>

                    <label class="form-label mt-2">Gênero </label> <span class="text-danger">*</span>
                    <select name="genero" class="form-control" required>
                        <option value="macho" <?= $animal['genero']=='macho'?'selected':'' ?>>Macho</option>
                        <option value="femea" <?= $animal['genero']=='femea'?'selected':'' ?>>Fêmea</option>
                        <option value="nao_informado" <?= $animal['genero']=='nao_informado'?'selected':'' ?>>Não informado</option>
                    </select>

                    <label class="form-label mt-2">Raça </label> <span class="text-danger">*</span>
                    <select name="raca_id" class="form-control" required>
                        <option value="">Selecione</option>
                        <?php foreach ($racas as $r): ?>
                            <option value="<?= $r['id'] ?>"
                                <?= $animal['raca_id']==$r['id']?'selected':'' ?>>
                                <?= htmlspecialchars($r['racas']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label class="form-label mt-2">Porte</label>
                    <select name="porte" class="form-control">
                        <option value="">Selecione</option>
                        <option value="pequeno" <?= $animal['porte']=='pequeno'?'selected':'' ?>>Pequeno</option>
                        <option value="medio" <?= $animal['porte']=='medio'?'selected':'' ?>>Médio</option>
                        <option value="grande" <?= $animal['porte']=='grande'?'selected':'' ?>>Grande</option>
                    </select>

                    <label class="form-label mt-2">Cor Predominante</label>
                    <select name="cor_predominante" class="form-control">
                        <option value="">Selecione</option>
                        <option value="preto" <?= $animal['cor_predominante']=='preto'?'selected':'' ?>>Preto</option>
                        <option value="branco" <?= $animal['cor_predominante']=='branco'?'selected':'' ?>>Branco</option>
                        <option value="marrom" <?= $animal['cor_predominante']=='marrom'?'selected':'' ?>>Marrom</option>
                        <option value="cinza" <?= $animal['cor_predominante']=='cinza'?'selected':'' ?>>Cinza</option>
                        <option value="caramelo" <?= $animal['cor_predominante']=='caramelo'?'selected':'' ?>>Caramelo</option>
                        <option value="preto e branco" <?= $animal['cor_predominante']=='preto e branco'?'selected':'' ?>>Preto e Branco</option>
                        <option value="outros" <?= $animal['cor_predominante']=='outros'?'selected':'' ?>>Outros</option>
                    </select>

                    <label class="form-label mt-2">Idade</label>
                    <select name="idade" class="form-control">
                        <option value="">Selecione</option>
                        <option value="Filhote" <?= $animal['idade']=='Filhote'?'selected':'' ?>>Filhote</option>
                        <option value="Adulto" <?= $animal['idade']=='Adulto'?'selected':'' ?>>Adulto</option>
                        <option value="Idoso" <?= $animal['idade']=='Idoso'?'selected':'' ?>>Idoso</option>
                    </select>

                    <label class="form-label mt-2">Telefone </label> <span class="text-danger">*</span>
                    <input type="text"
                           name="telefone_contato"
                           id="telefone_contato"
                           class="form-control"
                           required
                           maxlength="15"
                           placeholder="(99) 99999-9999"
                           value="<?= htmlspecialchars($animal['telefone_contato']) ?>">

                    <label class="form-label mt-2">Descrição</label>
                    <textarea name="descricao" class="form-control" maxlength="150"><?= htmlspecialchars($animal['descricao'])  ?></textarea>
                </div>
            </div>

            <hr class="my-4">
            <h5><i class="bi bi-geo-alt-fill"></i> Localização</h5>
            <div id="map"></div>

            <input type="text" id="latitude" name="latitude" class="form-control mt-2"
                   value="<?= $animal['latitude'] ?>" readonly>

            <input type="text" id="longitude" name="longitude" class="form-control mt-2"
                   value="<?= $animal['longitude'] ?>" readonly>

            <button type="submit" class="btn btn-success mt-3"><i class="bi bi-save-fill"></i> Salvar Alterações</button>
        </form>
    </div>

    <!-- Footer full width, conteúdo alinhado -->
    <footer class="footer-rastreia">
        <div class="container">
            © 2025 Rastreia Bicho 
        </div>
    </footer>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        const lat = <?= $animal['latitude'] ?? -29.78 ?>;
        const lng = <?= $animal['longitude'] ?? -57.10 ?>;

        const map = L.map('map').setView([lat, lng], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        const marker = L.marker([lat, lng], { draggable: true }).addTo(map);

        marker.on('dragend', e => {
            document.getElementById('latitude').value = e.target.getLatLng().lat;
            document.getElementById('longitude').value = e.target.getLatLng().lng;
        });
    </script>

    <script>
        document.getElementById('telefone_contato').addEventListener('input', function (e) {
            let valor = e.target.value.replace(/\D/g, '');

            if (valor.length > 11) {
                valor = valor.slice(0, 11);
            }

            if (valor.length <= 2) {
                valor = '(' + valor;
            } else if (valor.length <= 7) {
                valor = '(' + valor.slice(0, 2) + ') ' + valor.slice(2);
            } else {
                valor = '(' + valor.slice(0, 2) + ') ' +
                        valor.slice(2, 7) + '-' +
                        valor.slice(7);
            }

            e.target.value = valor;
        });
    </script>
</body>
</html>