<?php
include('conecta.php');
session_start();

// 🔐 Apenas administradores
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    echo "<script>alert('❌ Você não tem permissão para acessar esta área!'); window.location='index.php';</script>";
    exit;
}

// 🔍 Busca todos os animais com JOIN
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
body {
    background-color: #f2f2f2;
}
.card {
    border-radius: 15px;
}
.table img {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 8px;
}
.table thead {
    background: #179e46ff;
    color: white;
}
.btn-editar {
    background-color: #ffc107;
    color: black;
}
.btn-excluir {
    background-color: #dc3545;
    color: white;
}
.btn-voltar {
    background-color: #179e46ff;
    color: white;
}
</style>

</head>
<body>

<div class="container mt-4">
    <div class="card p-4 shadow">
        <h2 class="text-center mb-4">🐾 Gerenciar Animais</h2>

        <div class="text-end mb-3">
            <a href="admin.php" class="btn btn-voltar">⬅ Voltar</a>
        </div>

        <table class="table table-bordered table-striped text-center">
            <thead>
                <tr>
                    <th>ID Animal</th>
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
            <img src="uploads/<?= htmlspecialchars($animal['foto']) ?>" alt="Foto">
        <?php else: ?>
            <span class="text-muted">N/A</span>
        <?php endif; ?>
    </td>

    <td><?= !empty($animal['nome']) ? htmlspecialchars($animal['nome']) : 'N/A' ?></td>
    <td><?= !empty($animal['situacao']) ? ucfirst($animal['situacao']) : 'N/A' ?></td>
    <td><?= !empty($animal['especie']) ? ucfirst($animal['especie']) : 'N/A' ?></td>
    <td><?= !empty($animal['genero']) ? ucfirst($animal['genero']) : 'N/A' ?></td>
    <td><?= !empty($animal['raca_nome']) ? $animal['raca_nome'] : 'N/A' ?></td>
    <td><?= !empty($animal['porte']) ? ucfirst($animal['porte']) : 'N/A' ?></td>
    <td><?= !empty($animal['cor_predominante']) ? ucfirst($animal['cor_predominante']) : 'N/A' ?></td>
    <td><?= !empty($animal['idade']) ? $animal['idade'] : 'N/A' ?></td>
    <td><?= !empty($animal['telefone_contato']) ? $animal['telefone_contato'] : 'N/A' ?></td>

    <td>
        <a href="adm_excluir_animal.php?id=<?= $animal['id'] ?>" 
           class="btn btn-excluir btn-sm"
           onclick="return confirm('Tem certeza que deseja excluir este animal?')">
           🗑 Excluir
        </a>

        <a href="adm_editar_animal.php?id=<?= $animal['id'] ?>" class="btn btn-editar btn-sm">
            ✏ Editar
        </a>
    </td>

</tr>
<?php endwhile; ?>
</tbody>

        </table>

    </div>
</div>

</body>
</html>
