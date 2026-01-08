<?php
session_start();
include("conecta.php");

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_SESSION['usuario_id'];

$sql = "SELECT nome, email, telefone, endereco, data_nascimento FROM usuarios WHERE id = $id";
$result = mysqli_query($conexao, $sql);
$usuario = mysqli_fetch_assoc($result);

if (!$usuario) {
    session_destroy();
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome      = $_POST["nome"] ?? "";
    $telefone  = $_POST["telefone"] ?? "";
    $endereco  = $_POST["endereco"] ?? "";
    $data_nasc = $_POST["data_nascimento"] ?? "";

    $erros = [];
    if ($nome === "") $erros[] = "O nome não pode ser apagado.";
    if ($endereco === "") $erros[] = "O endereço não pode ser apagado.";
    if ($data_nasc === "") $erros[] = "A data de nascimento não pode ser apagada.";
    if (strlen($telefone) !== 11) $erros[] = "O telefone deve conter exatamente 11 dígitos.";

    if (!empty($erros)) {
        $msg = implode(" ", $erros);
        header("Location: perfil.php?msg=" . urlencode($msg));
        exit;
    }

    $sqlUp = "UPDATE usuarios SET 
                nome='$nome', 
                telefone='$telefone', 
                endereco='$endereco', 
                data_nascimento='$data_nasc' 
              WHERE id=$id";
    
    mysqli_query($conexao, $sqlUp);
$msgParts = ["Dados atualizados!"];

    if (!empty($_POST["senha"]) || !empty($_POST["confirmar_senha"])) {
        if (!empty($_POST["senha"]) && $_POST["senha"] === $_POST["confirmar_senha"]) {
            $senhaHash = password_hash($_POST["senha"], PASSWORD_DEFAULT);
            $senhaHashEscaped = mysqli_real_escape_string($conexao, $senhaHash);

            $sqlSenha = "UPDATE usuarios SET senha='$senhaHashEscaped' WHERE id=$id";
            mysqli_query($conexao, $sqlSenha);

            $msgParts[] = "Senha alterada com sucesso!";
        } else {
            $msgParts[] = "As senhas não coincidem.";
        }
    }

    $msg = implode(" ", $msgParts);
    header("Location: perfil.php?msg=" . urlencode($msg));
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meu Perfil - Rastreia Bicho 🐾</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body { background-color: #f2f2f2; min-height: 100vh; margin: 0; font-family: Arial, sans-serif; }
        .navbar { background-color: #179e46; padding: 1rem; border-bottom: 3px solid #2e3531; box-shadow: 0 2px 6px rgba(0,0,0,0.15); }
        .navbar-brand { font-weight: bold; font-size: 1.7rem; color: #2b2b2b !important; display: inline-flex; align-items: center; gap: 6px; }
        .card-perfil { max-width: 520px; background: #fff; border-radius: 10px; padding: 22px; margin: 40px auto; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .footer-rastreia { background-color: #179e46; color: #333; text-align: center; padding: 12px; font-weight: 600; border-top: 2px solid #2e3531; position: relative; bottom: 0; width: 100%; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fa-solid fa-paw"></i> RASTREIA BICHO
        </a>

        <div class="ms-auto d-flex gap-2">
            <a href="registrar_animal.php" class="btn btn-dark"><i class="bi bi-plus-circle"></i> Registrar Animal</a>
            <a href="perfil.php" class="btn btn-dark"><i class="bi bi-person-circle"></i> Perfil</a>
            <a href="perfil_animais.php" class="btn btn-dark"><i class="fa-solid fa-paw"></i> Meus Animais</a>
            <a href="logout.php" class="btn btn-danger"><i class="bi bi-box-arrow-right"></i> Sair</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="card-perfil">
        <h3 class="text-center fw-bold mb-3">Meu Perfil</h3>

        <?php if (isset($_GET["msg"])): ?>
            <div class="alert alert-info text-center small">
                <?= htmlspecialchars($_GET["msg"]) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" required
                       value="<?= htmlspecialchars($usuario['nome']) ?>" maxlength="50">
            </div>

            <div class="mb-3">
                <label class="form-label">E-mail</label>
                <input type="email" class="form-control" disabled
                       value="<?= htmlspecialchars($usuario['email']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Telefone</label>
                <input type="text" name="telefone" id="telefone" class="form-control"
                       required maxlength="15" placeholder="(99) 99999-9999"
                       value="<?= htmlspecialchars($usuario['telefone']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Endereço</label>
                <input type="text" name="endereco" class="form-control" required
                       value="<?= htmlspecialchars($usuario['endereco']) ?>" maxlength="50">
            </div>

            <div class="mb-3">
                <label class="form-label">Data de nascimento</label>
                <input type="date" name="data_nascimento" class="form-control" required
                       value="<?= htmlspecialchars($usuario['data_nascimento']) ?>">
            </div>

            <hr>
            <h5 class="fw-bold">Alterar senha</h5>
            <div class="mb-3">
                <input type="password" name="senha" class="form-control" placeholder="Nova senha" maxlength="50">
            </div>
            <div class="mb-3">
                <input type="password" name="confirmar_senha" class="form-control" placeholder="Confirmar nova senha" maxlength="50">
            </div>

            <button class="btn btn-success w-100">Salvar alterações</button>

            <button type="button" class="btn btn-danger w-100 mt-3 border-0" onclick="confirmarExclusao()"> Excluir minha conta</button>
        </form>
    </div>
</div>

<footer class="footer-rastreia">
    © 2025 Rastreia Bicho
</footer>

<script>
function confirmarExclusao() {
    if (confirm(" Tem certeza que deseja excluir sua conta?\n\nTodos os seus animais registrados também serão removidos. Essa ação não pode ser desfeita.")) {
        window.location.href = "excluir_usuario.php";
    }
}

document.getElementById('telefone').addEventListener('input', function (e) {
    let valor = e.target.value.replace(/\D/g, '');
    if (valor.length > 11) valor = valor.slice(0, 11);
    if (valor.length <= 2) {
        valor = '(' + valor;
    } else if (valor.length <= 7) {
        valor = '(' + valor.slice(0, 2) + ') ' + valor.slice(2);
    } else {
        valor = '(' + valor.slice(0, 2) + ') ' + valor.slice(2, 7) + '-' + valor.slice(7);
    }
    e.target.value = valor;
});
</script>

</body>
</html>