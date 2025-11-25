<?php
session_start();
include("conecta.php");

// 🔒 Apenas administradores podem acessar
if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] !== "administrador") {
    echo "<script>alert('❌ Você não tem permissão para acessar esta área!'); window.location='index.php';</script>";
    exit;
}

// Busca todos os animais cadastrados
$sql = "SELECT * FROM animais ORDER BY id DESC";
$resultado = $conexao->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Gerenciar Animais</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background-color: #f8f9fa; }
.table img { width: 70px; height: 70px; object-fit: cover; border-radius: 10px; }
.card { border-radius: 15px; }
.btn-editar { background-color: #179e46ff; color:white; }
.btn-visualizar { background-color: #0d6efd; color:white; }
.btn-excluir { background-color: #dc3545; color:white; }
</style>

</head>
<body>

<div class="container mt-4">

    <div class="card p-4 shadow">

        <h3 class="text-center mb-3">🐾 Gerenciar Animais</h3>

        <table class="table table-bordered table-striped align-middle">

            <thead class="table-success">
                <tr>
                    <th>ID</th>
                    <th>ID Usuário</th>
                    <th>Nome</th>
                    <th>Situação</th>
                    <th>Espécie</th>
                    <th>Gênero</th>
                    <th>Raça</th>
                    <th>Porte</th>
                    <th>Cor</th>
                    <th>Idade</th>
                    <th>Telefone</th>
                    <th>Foto</th>
                    <th>Localização</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($a = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= $a['id'] ?></td>
                    <td><?= $a['usuario_id'] ?></td>
                    <td><?= $a['nome'] ?></td>
                    <td><?= ucfirst($a['situacao']) ?></td>
                    <td><?= $a['especie'] ?></td>
                    <td><?= $a['genero'] ?></td>
                    <td><?= $a['racas'] ?></td>
                    <td><?= $a['porte'] ?></td>
                    <td><?= $a['cor_predominante'] ?></td>
                    <td><?= $a['idade'] ?></td>
                    <td><?= $a['telefone_contato'] ?></td>

                    <td>
                        <?php if (!empty($a['foto'])): ?>
                            <img src="<?= $a['foto'] ?>" alt="foto">
                        <?php else: ?>
                            <span class="text-muted">Sem foto</span>
                        <?php endif; ?>
                    </td>

                    <td><?= $a['localizacao'] ?></td>

                    <td>
                        <a href="editar_animal.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-editar mb-1">✏️ Editar</a>
                        <a href="visualizar_animal.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-visualizar mb-1">👁 Ver</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>

        </table>

        <a href="admin.php" class="btn btn-secondary mt-2">⬅ Voltar</a>

    </div>

</div>

</body>
</html>
