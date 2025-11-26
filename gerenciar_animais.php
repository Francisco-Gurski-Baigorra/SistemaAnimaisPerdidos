<?php
include('conecta.php');
session_start();

// 🔐 Apenas administradores
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    echo "<script>alert('❌ Você não tem permissão para acessar esta área!'); window.location='index.php';</script>";
    exit;
}

// 🔍 Busca todos os animais com JOIN na tabela racas
$sql = "SELECT 
            a.id,
            a.usuario_id,
            a.situacao,
            a.especie,
            a.genero,
            a.foto,
            r.racas AS raca_nome,
            a.porte,
            a.cor_predominante,
            a.idade,
            a.nome,
            a.descricao,
            a.latitude,
            a.longitude,
            a.telefone_contato,
            a.data_ocorrido,
            a.data_cadastro
        FROM animais a
        LEFT JOIN racas r ON a.raca_id = r.id
        ORDER BY a.id DESC";

$result = $conexao->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Animais</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background: #f7f7f7; }
        .table img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
        }
        h2 { color: #179e46ff; font-weight: bold; }
    </style>
</head>
<body class="container py-4">

<h2 class="mb-4">🐾 Gerenciar Animais</h2>

<a href="admin.php" class="btn btn-secondary mb-3">⬅ Voltar</a>

<table class="table table-bordered table-striped">
    <thead class="table-success">
        <tr>
            <th>ID</th>
            <th>ID Usuário</th>
            <th>Foto</th>
            <th>Nome</th>
            <th>Situação</th>
            <th>Espécie</th>
            <th>Gênero</th>
            <th>Raça</th>
            <th>Porte</th>
            <th>Cor</th>
            <th>Idade</th>
            <th>Telefone</th>
            <th>Localização</th>
            <th>Ações</th>
        </tr>
    </thead>

    <tbody>
        <?php while ($animal = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $animal['id'] ?></td>

            <td><?= $animal['usuario_id'] ?></td>

            <td>
    <?php if (!empty($animal['foto'])): ?>
        <img src="uploads/<?= htmlspecialchars($animal['foto']) ?>" 
             alt="Foto" width="80" height="80" 
             style="object-fit: cover; border-radius: 5px;">
    <?php else: ?>
        <span class="text-muted">Sem foto</span>
    <?php endif; ?>
</td>



            <td><?= htmlspecialchars($animal['nome']) ?></td>
            <td><?= ucfirst($animal['situacao']) ?></td>
            <td><?= ucfirst($animal['especie']) ?></td>
            <td><?= ucfirst($animal['genero']) ?></td>

            <td><?= $animal['raca_nome'] ?? '—' ?></td>

            <td><?= ucfirst($animal['porte']) ?></td>
            <td><?= ucfirst($animal['cor_predominante']) ?></td>
            <td><?= $animal['idade'] ?></td>
            <td><?= $animal['telefone_contato'] ?></td>

            <td>
                <?php if ($animal['latitude'] && $animal['longitude']): ?>
                    <a target="_blank" 
                       href="https://www.google.com/maps?q=<?= $animal['latitude'] ?>,<?= $animal['longitude'] ?>">
                       Ver mapa
                    </a>
                <?php else: ?>
                    <span class="text-muted">Não informado</span>
                <?php endif; ?>
            </td>

            <td>
                <a href="adm_excluir_animal.php?id=<?= $animal['id'] ?>" 
             class="btn btn-danger btn-sm"
             onclick="return confirm('Tem certeza que deseja excluir este animal?')">
             🗑 Excluir
                    </a>

                <a href="adm_editar_animal.php?id=<?= $animal['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
