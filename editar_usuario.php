<?php
session_start();
include("conecta.php");

// 🔒 Verifica se é administrador
if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] !== "administrador") {
    echo "<script>alert('❌ Você não tem permissão para acessar esta área!'); window.location='index.php';</script>";
    exit;
}

// Verifica se recebeu o ID
if (!isset($_GET["id"])) {
    echo "<script>alert('ID inválido!'); window.location='gerenciar_usuarios.php';</script>";
    exit;
}

$id = $_GET["id"];

// Busca os dados do usuário
$sql = "SELECT * FROM usuarios WHERE id = $id";
$resultado = $conexao->query($sql);

if ($resultado->num_rows == 0) {
    echo "<script>alert('Usuário não encontrado!'); window.location='gerenciar_usuarios.php';</script>";
    exit;
}

$usuario = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Usuário</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background-color: #f2f2f2; }
.card { border-radius: 15px; }
.btn-salvar { background-color: #179e46ff; color:white; }
.btn-voltar { background-color: #6c757d; color:white; }
</style>

</head>
<body>

<div class="container mt-4">
    <div class="card p-4 shadow col-md-6 offset-md-3">

        <h3 class="text-center mb-3">✏️ Editar Usuário</h3>

        <form action="salvar_edicao_usuario.php" method="POST">

            <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

            <label class="form-label">Nome:</label>
            <input type="text" class="form-control" name="nome" value="<?= $usuario['nome'] ?>" required>

            <label class="form-label mt-3">Email:</label>
            <input type="email" class="form-control" name="email" value="<?= $usuario['email'] ?>" required>

            <label class="form-label mt-3">Telefone:</label>
            <input type="text" class="form-control" name="telefone" value="<?= $usuario['telefone'] ?>">

            <label class="form-label mt-3">Endereço:</label>
            <input type="text" class="form-control" name="endereco" value="<?= $usuario['endereco'] ?>">

            <label class="form-label mt-3">Data de Nascimento:</label>
            <input type="date" class="form-control" name="data_nascimento" value="<?= $usuario['data_nascimento'] ?>">

            <label class="form-label mt-3">Tipo de Usuário:</label>
            <select class="form-select" name="tipo_usuario">
                <option value="comum" <?= $usuario['tipo_usuario'] == 'comum' ? 'selected' : '' ?>>Comum</option>
                <option value="administrador" <?= $usuario['tipo_usuario'] == 'administrador' ? 'selected' : '' ?>>Administrador</option>
            </select>

            <label class="form-label mt-3">Ativo:</label>
            <select class="form-select" name="ativo">
                <option value="sim" <?= $usuario['ativo'] == 'sim' ? 'selected' : '' ?>>Sim</option>
                <option value="nao" <?= $usuario['ativo'] == 'nao' ? 'selected' : '' ?>>Não</option>
            </select>

            <button class="btn btn-salvar mt-4 w-100">Salvar Alterações</button>
        </form>

        <a href="gerenciar_usuarios.php" class="btn btn-voltar mt-3 w-100">Voltar</a>

    </div>
</div>

</body>
</html>
