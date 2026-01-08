<?php
session_start();
include("conecta.php");

// 🔒 Verifica se é administrador
if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] !== "administrador") {
    echo "<script>alert('❌ Você não tem permissão para acessar esta área!'); window.location='index.php';</script>";
    exit;
}

// Verifica se recebeu o ID de forma simples
if (isset($_GET["id"])) {
    $id = (int)$_GET["id"];
} else {
    echo "<script>alert('ID inválido!'); window.location='gerenciar_usuarios.php';</script>";
    exit;
}

// Busca os dados do usuário usando estilo procedural
$sql = "SELECT * FROM usuarios WHERE id = $id";
$resultado = mysqli_query($conexao, $sql);

if (mysqli_num_rows($resultado) == 0) {
    echo "<script>alert('Usuário não encontrado!'); window.location='gerenciar_usuarios.php';</script>";
    exit;
}

$usuario = mysqli_fetch_assoc($resultado);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body { background-color: #ffffff; min-height: 100vh; margin: 0; font-family: Arial, sans-serif; display: flex; flex-direction: column; }
        .navbar { background-color: #179e46; padding: 1rem; border-bottom: 3px solid #2e3531; box-shadow: 0 2px 6px rgba(54, 51, 51, 0.15); width: 100%; }
        .navbar-brand { font-weight: bold; font-size: 1.7rem; color: #2b2b2b !important; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; transition: 0.2s; }
        .navbar-brand:hover { transform: translateY(-2px) scale(1.04); opacity: 0.9; }
        .footer-rastreia { background-color: #179e46ff; color: #333; text-align: center; padding: 12px; font-size: 0.95rem; font-weight: 600; width: 100%; border-top: 2px solid #2e3531ff; margin-top: auto; }
        .card { border-radius: 15px; }
        .btn-salvar { background-color: #179e46ff; color:white; }
        .btn-voltar { background-color: #6c757d; color:white; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="admin.php">
            <i class="fa-solid fa-paw me-2"></i> RASTREIA BICHO
        </a>
    </div>
</nav>

<div class="container mt-4 mb-5">
    <div class="card p-4 shadow col-md-6 offset-md-3">

        <h3 class="text-center mb-3"><i class="bi bi-pencil"></i> Editar Usuário</h3>

        <form action="salvar_edicao_usuario.php" method="POST" id="formEditarUsuario">
            
            <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">

            <label class="form-label">Nome:</label>
            <input type="text" class="form-control" name="nome" value="<?php echo $usuario['nome']; ?>" maxlength="30" required>

            <label class="form-label mt-3">Email:</label>
            <input type="email" class="form-control" name="email" value="<?php echo $usuario['email']; ?>" maxlength="30" required>

            <label class="form-label mt-3">Telefone:</label>
            <input type="text" class="form-control" name="telefone" id="telefone" maxlength="15" placeholder="(99) 99999-9999" value="<?php echo $usuario['telefone']; ?>" required>

            <label class="form-label mt-3">Endereço:</label>
            <input type="text" class="form-control" name="endereco" value="<?php echo $usuario['endereco']; ?>" maxlength="50" required>

            <label class="form-label mt-3">Data de Nascimento:</label>
            <input type="date" class="form-control" name="data_nascimento" value="<?php echo $usuario['data_nascimento']; ?>" required>

            <label class="form-label mt-3">Tipo de Usuário:</label>
            <select class="form-select" name="tipo_usuario">
                <option value="">Selecione</option>
                <option value="comum" <?php if($usuario['tipo_usuario'] == 'comum') echo 'selected'; ?>>Comum</option>
                <option value="administrador" <?php if($usuario['tipo_usuario'] == 'administrador') echo 'selected'; ?>>Administrador</option>
            </select>

            <label class="form-label mt-3">Ativo:</label>
            <select class="form-select" name="ativo" required>
                <option value="">Selecione</option>
                <option value="sim" <?php if($usuario['ativo'] == 'sim') echo 'selected'; ?>>Sim</option>
                <option value="nao" <?php if($usuario['ativo'] == 'nao') echo 'selected'; ?>>Não</option>
            </select>

            <button type="submit" class="btn btn-salvar mt-4 w-100">Salvar Alterações</button>
        </form>

        <a href="gerenciar_usuarios.php" class="btn btn-voltar mt-3 w-100">Voltar</a>
    </div>
</div>

<script>
// Máscara do telefone
document.getElementById('telefone').addEventListener('input', function (e) {
    let valor = e.target.value.replace(/\D/g, '');
    if (valor.length > 11) valor = valor.slice(0, 11);
    if (valor.length <= 2) valor = '(' + valor;
    else if (valor.length <= 7) valor = '(' + valor.slice(0, 2) + ') ' + valor.slice(2);
    else valor = '(' + valor.slice(0, 2) + ') ' + valor.slice(2, 7) + '-' + valor.slice(7);
    e.target.value = valor;
});

// Validação geral do formulário
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

    const telefoneInput = document.getElementById('telefone');
    const telefoneNumeros = telefoneInput.value.replace(/\D/g, '');
    if (telefoneNumeros.length !== 11) {
        telefoneInput.classList.add('is-invalid');
        alert('O telefone deve conter exatamente 11 dígitos (DDD + celular).');
        erro = true;
    }
    if (erro) e.preventDefault();
});
</script>

<footer class="footer-rastreia">
    © 2025 Rastreia Bicho
</footer>

</body>
</html>