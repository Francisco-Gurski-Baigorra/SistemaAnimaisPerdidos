<?php
session_start();
include("conecta.php");

// 🔒 Verifica se é administrador
if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] !== "administrador") {
    echo "<script>alert('❌ Você não tem permissão para acessar esta área!'); window.location='index.php';</script>";
    exit;
}

// Busca todos os usuários
$sql = "SELECT * FROM usuarios ORDER BY id DESC";
$resultado = $conexao->query($sql);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Gerenciar Usuários</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
body {
    background-color: #f2f2f2;
}
.card {
    border-radius: 15px;
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
        <h2 class="text-center mb-4">👥 Gerenciar Usuários</h2>

        <div class="text-end mb-3">
            <a href="admin.php" class="btn btn-voltar"><i class="bi bi-arrow-left"></i> Voltar</a>
        </div>

        <table class="table table-bordered table-striped text-center">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Endereço</th>
                    <th>Nascimento</th>
                    <th>Tipo</th>
                    <th>Ativo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($usuario = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= $usuario["id"] ?></td>
                    <td><?= $usuario["nome"] ?></td>
                    <td><?= $usuario["email"] ?></td>
                    <td><?= $usuario["telefone"] ?></td>
                    <td><?= $usuario["endereco"] ?></td>
                    <td><?= date("d/m/Y", strtotime($usuario["data_nascimento"])) ?></td>
                    <td><?= ucfirst($usuario["tipo_usuario"]) ?></td>
                    <td><?= $usuario["ativo"] == "sim" ? "🟢 Sim" : "🔴 Não" ?></td>

                    <td>
                        <a href="editar_usuario.php?id=<?= $usuario['id'] ?>" class="btn btn-editar btn-sm">
                            <i class="bi bi-pencil-square"></i> Editar
                        </a>

                        <a href="adm_excluir_usuario.php?id=<?= $usuario['id'] ?>" 
                           class="btn btn-excluir btn-sm"
                           onclick="return confirm('Tem certeza que deseja excluir este usuário?')">
                           <i class="bi bi-trash"></i> Excluir
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
