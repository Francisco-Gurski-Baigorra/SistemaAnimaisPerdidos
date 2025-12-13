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

        <form action="salvar_edicao_usuario.php" method="POST" id="formEditarUsuario">


            <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

            <label class="form-label">Nome:</label>
<input type="text" class="form-control" name="nome" value="<?= $usuario['nome'] ?>" required>

<label class="form-label mt-3">Email:</label>
<input type="email" class="form-control" name="email" value="<?= $usuario['email'] ?>" required>

<label class="form-label mt-3">Telefone:</label>
<input
    type="text"
    class="form-control"
    name="telefone"
    id="telefone"
    maxlength="15"
    placeholder="(99) 99999-9999"
    value="<?= htmlspecialchars($usuario['telefone']) ?>"
    required
>


<label class="form-label mt-3">Endereço:</label>
<input type="text" class="form-control" name="endereco" value="<?= $usuario['endereco'] ?>" required>

<label class="form-label mt-3">Data de Nascimento:</label>
<input type="date" class="form-control" name="data_nascimento" value="<?= $usuario['data_nascimento'] ?>" required>

<label class="form-label mt-3">Tipo de Usuário:</label>
<select class="form-select" name="tipo_usuario">
    <option value="">Selecione</option>
    <option value="comum" <?= $usuario['tipo_usuario'] == 'comum' ? 'selected' : '' ?>>Comum</option>
    <option value="administrador" <?= $usuario['tipo_usuario'] == 'administrador' ? 'selected' : '' ?>>Administrador</option>
</select>

<label class="form-label mt-3">Ativo:</label>
<select class="form-select" name="ativo" required>
    <option value="">Selecione</option>
    <option value="sim" <?= $usuario['ativo'] == 'sim' ? 'selected' : '' ?>>Sim</option>
    <option value="nao" <?= $usuario['ativo'] == 'nao' ? 'selected' : '' ?>>Não</option>
</select>


            <button class="btn btn-salvar mt-4 w-100">Salvar Alterações</button>
        </form>

        <a href="gerenciar_usuarios.php" class="btn btn-voltar mt-3 w-100">Voltar</a>

    </div>
</div>

<script>
// 🔹 Máscara do telefone
document.getElementById('telefone').addEventListener('input', function (e) {
    let valor = e.target.value.replace(/\D/g, '');

    // Limita a 11 números
    if (valor.length > 11) {
        valor = valor.slice(0, 11);
    }

    if (valor.length <= 2) {
        valor = '(' + valor;
    } 
    else if (valor.length <= 7) {
        valor = '(' + valor.slice(0, 2) + ') ' + valor.slice(2);
    } 
    else {
        valor = '(' + valor.slice(0, 2) + ') ' +
                valor.slice(2, 7) + '-' +
                valor.slice(7);
    }

    e.target.value = valor;
});

// 🔹 Validação geral do formulário
document.getElementById('formEditarUsuario').addEventListener('submit', function (e) {

    let erro = false;
    const campos = this.querySelectorAll('input[required], select[required]');

    campos.forEach(campo => {
        if (!campo.value.trim()) {
            campo.classList.add('is-invalid');
            erro = true;
        } else {
            campo.classList.remove('is-invalid');
        }
    });

    // 🔹 Validação específica do telefone
    const telefoneInput = document.getElementById('telefone');
    const telefoneNumeros = telefoneInput.value.replace(/\D/g, '');

    if (telefoneNumeros.length !== 11) {
        telefoneInput.classList.add('is-invalid');
        alert(' O telefone deve conter exatamente 11 dígitos (DDD + celular).');
        erro = true;
    }

    if (erro) {
        e.preventDefault();
    }
});
</script>



</body>
</html>
