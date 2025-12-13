<?php
session_start();
include("conecta.php");

// Verifica login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_SESSION['usuario_id'];

// Buscar informações do usuário
$sql = "SELECT nome, email, telefone, endereco, data_nascimento FROM usuarios WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// ==========================
// ATUALIZAÇÃO DE DADOS
// ==========================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome       = trim($_POST["nome"] ?? "");
    $telefone   = preg_replace('/\D/', '', $_POST["telefone"] ?? "");
    $endereco   = trim($_POST["endereco"] ?? "");
    $data_nasc  = $_POST["data_nascimento"] ?? "";

    $erros = [];

    // Validações obrigatórias
    if ($nome === "") {
        $erros[] = "O nome não pode ser apagado.";
    }

    if ($endereco === "") {
        $erros[] = "O endereço não pode ser apagado.";
    }

    if ($data_nasc === "") {
        $erros[] = "A data de nascimento não pode ser apagada.";
    }

    if (strlen($telefone) !== 11) {
        $erros[] = "O telefone deve conter exatamente 11 dígitos.";
    }

    // Se houver erros, volta com notificação
    if (!empty($erros)) {
        $msg = implode(" ", $erros);
        header("Location: perfil.php?msg=" . urlencode($msg));
        exit;
    }

    // Atualiza dados
    $sqlUp = "UPDATE usuarios SET nome=?, telefone=?, endereco=?, data_nascimento=? WHERE id=?";
    $stmtUp = $conexao->prepare($sqlUp);
    $stmtUp->bind_param("ssssi", $nome, $telefone, $endereco, $data_nasc, $id);
    $stmtUp->execute();

    $msgParts = ["Dados atualizados!"];

    // Atualizar senha (opcional)
    if (!empty($_POST["senha"]) || !empty($_POST["confirmar_senha"])) {
        if (!empty($_POST["senha"]) && $_POST["senha"] === $_POST["confirmar_senha"]) {
            $senhaHash = password_hash($_POST["senha"], PASSWORD_DEFAULT);

            $sqlSenha = "UPDATE usuarios SET senha=? WHERE id=?";
            $stmtSenha = $conexao->prepare($sqlSenha);
            $stmtSenha->bind_param("si", $senhaHash, $id);
            $stmtSenha->execute();

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
body {
    background-color: #f2f2f2;
    min-height: 100vh;
    margin: 0;
    font-family: Arial, sans-serif;
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
.card-perfil {
    max-width: 520px;
    background: #fff;
    border-radius: 10px;
    padding: 22px;
    margin: 40px auto;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.footer-rastreia {
    background-color: #179e46ff;
    color: #333;
    text-align: center;
    padding: 12px;
    font-weight: 600;
    border-top: 2px solid #2e3531ff;
}
</style>
</head>

<body>

<!-- ✅ Barra superior -->

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="#">
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

                <!-- 🐾 Ícone atualizado para Animais Registrados -->
                <a href="perfil_animais.php" class="btn btn-dark me-2">
                   <i class="fa-solid fa-paw"></i><i class="bi bi-paw-fill me-2"></i> Meus Animais
                </a>

                <!-- 🔄 Botão Sair mais harmonioso -->
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

<div class="card-perfil">
    <h3 class="text-center fw-bold mb-3">Meu Perfil</h3>

    <?php if (isset($_GET["msg"])): ?>
        <div class="alert alert-info text-center">
            <?= htmlspecialchars($_GET["msg"]) ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nome completo</label>
            <input type="text" name="nome" class="form-control" required
                   value="<?= htmlspecialchars($usuario['nome']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">E-mail</label>
            <input type="email" class="form-control" disabled
                   value="<?= htmlspecialchars($usuario['email']) ?>">
        </div>

        <div class="mb-3">
    <label class="form-label">Telefone</label>
    <input
        type="text"
        name="telefone"
        id="telefone"
        class="form-control"
        required
        maxlength="15"
        placeholder="(99) 99999-9999"
        value="<?= htmlspecialchars($usuario['telefone']) ?>"
    >
</div>


        <div class="mb-3">
            <label class="form-label">Endereço</label>
            <input type="text" name="endereco" class="form-control" required
                   value="<?= htmlspecialchars($usuario['endereco']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Data de nascimento</label>
            <input type="date" name="data_nascimento" class="form-control" required
                   value="<?= htmlspecialchars($usuario['data_nascimento']) ?>">
        </div>

        <hr>

        <h5 class="fw-bold">Alterar senha</h5>

        <div class="mb-3">
            <input type="password" name="senha" class="form-control" placeholder="Nova senha">
        </div>

        <div class="mb-3">
            <input type="password" name="confirmar_senha" class="form-control" placeholder="Confirmar senha">
        </div>

        <button class="btn btn-success w-100">Salvar alterações</button>
    </form>
</div>

<script>
document.getElementById('telefone').addEventListener('input', function (e) {
    let valor = e.target.value.replace(/\D/g, '');

    // limita a 11 dígitos
    if (valor.length > 11) {
        valor = valor.slice(0, 11);
    }

    // formatação
    if (valor.length <= 2) {
        valor = '(' + valor;
    } 
    else if (valor.length <= 7) {
        valor = '(' + valor.slice(0, 2) + ') ' + valor.slice(2);
    } 
    else {
        valor = '(' + valor.slice(0, 2) + ') ' + valor.slice(2, 7) + '-' + valor.slice(7);
    }

    e.target.value = valor;
});
</script>



<footer class="footer-rastreia">
    © 2025 Rastreia Bicho 
</footer>
<style>
.footer-rastreia {
    background-color: #179e46ff;
    color: #333;
    text-align: center;
    padding: 12px;
    font-size: 0.95rem;
    font-weight: 600;
    width: 100%;
    border-top: 2px solid #2e3531ff;
}
</style>


</body>
</html>
