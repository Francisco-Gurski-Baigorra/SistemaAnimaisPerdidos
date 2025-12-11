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

// se não achou usuário (caso raro), desloga
if (!$usuario) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Atualizar dados
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = $_POST["nome"] ?? "";
    $telefone = $_POST["telefone"] ?? "";
    $endereco = $_POST["endereco"] ?? "";
    $data_nasc = $_POST["data_nascimento"] ?? "";

    // Atualização comum
    $sqlUp = "UPDATE usuarios SET nome=?, telefone=?, endereco=?, data_nascimento=? WHERE id=?";
    $stmtUp = $conexao->prepare($sqlUp);
    $stmtUp->bind_param("ssssi", $nome, $telefone, $endereco, $data_nasc, $id);
    $stmtUp->execute();

    $msgParts = [];

    // Atualizar senha (se for preenchida)
    if (!empty($_POST["senha"]) || !empty($_POST["confirmar_senha"])) {
        if (!empty($_POST["senha"]) && $_POST["senha"] === $_POST["confirmar_senha"]) {
            $senhaHash = password_hash($_POST["senha"], PASSWORD_DEFAULT);

            $sqlSenha = "UPDATE usuarios SET senha=? WHERE id=?";
            $stmtSenha = $conexao->prepare($sqlSenha);
            $stmtSenha->bind_param("si", $senhaHash, $id);
            $stmtSenha->execute();

            $msgParts[] = "Senha alterada com sucesso!";
        } else {
            $msgParts[] = "As senhas não coincidem!";
        }
    }

    if (empty($msgParts)) {
        $msgParts[] = "Dados atualizados!";
    }

    $msg = implode(" ", $msgParts);

    // Recarrega os dados atualizados para exibir na página
    header("Location: perfil.php?msg=" . urlencode($msg));
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
   <meta charset="UTF-8">
   <title>Meu Perfil - Rastreia Bicho 🐾</title>

   <!-- Icones e Bootstrap -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
/* ===============================
   ESTILO UNIFICADO (igual login/cadastro)
================================*/

/* Fundo e centralização */
/* --- Garanta empilhamento vertical e centralização --- */
/* ======= corpo (para navbar NÃO fixa, como index.php) ======= */
/* ======= Estilo base ======= */
body {
    background-color: #f2f2f2;
    min-height: 100vh;
    margin: 0;
    padding-top: 24px;
    font-family: Arial, sans-serif;
    padding-top: 0;
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

/* ======= Card do perfil ======= */
.card-perfil {
    width: 100%;
    max-width: 520px;
    background: #ffffff;
    border: 1px solid #dcdcdc;
    border-radius: 10px;
    padding: 22px;
    margin: 0 auto; /* centraliza */
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Inputs */
.form-control {
    border-radius: 5px;
    border: 1px solid #ccc;
    height: 44px;
}

/* Botões */
.btn-success {
    background-color: #179e46ff;
    border: none;
    height: 44px;
}
.btn-success:hover {
    background-color: #12843b;
}

.btn-outline-danger {
    border-radius: 6px;
}

/* Footer */
footer {
    margin-top: 30px;
    width: 100%;
    max-width: 520px;
}

/* Responsivo */
@media (max-width: 480px) {
    .card-perfil {
        padding: 18px;
        margin: 0 12px;
    }
}

</style>
</head>
<body>

<!-- NAVBAR (igual ao login/cadastro) -->
<!-- NAVBAR idêntica ao index.php -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">
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

                <a href="perfil_animais.php" class="btn btn-dark me-2">
                   <i class="fa-solid fa-paw"></i><i class="bi bi-paw-fill me-2"></i> Meus Animais
                </a>

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


<!-- CONTAINER DO PERFIL (card central) -->
<div class="card-perfil">
    <h3 class="text-center mb-3 fw-bold">Meu Perfil</h3>

    <?php if (isset($_GET["msg"])): ?>
      <div class="alert alert-info text-center">
        <?= htmlspecialchars($_GET["msg"]) ?>
      </div>
    <?php endif; ?>

    <!-- Formulário -->
    <form method="POST" novalidate>
      <div class="mb-3">
        <label class="form-label">Nome completo</label>
        <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">E-mail cadastrado</label>
        <input type="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" disabled>
      </div>

      <div class="mb-3">
        <label class="form-label">Telefone</label>
        <input type="text" name="telefone" class="form-control" value="<?= htmlspecialchars($usuario['telefone']) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Endereço</label>
        <input type="text" name="endereco" class="form-control" value="<?= htmlspecialchars($usuario['endereco']) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Data de nascimento</label>
        <input type="date" name="data_nascimento" class="form-control" value="<?= htmlspecialchars($usuario['data_nascimento']) ?>">
      </div>

      <hr>

      <h5 class="fw-bold">Alterar senha</h5>

      <div class="mb-3">
        <label class="form-label">Nova senha</label>
        <input type="password" name="senha" class="form-control">
      </div>

      <div class="mb-3">
        <label class="form-label">Confirmar nova senha</label>
        <input type="password" name="confirmar_senha" class="form-control">
      </div>

      <button type="submit" class="btn btn-success w-100 mt-2">Salvar alterações</button>
    </form>

    <hr class="my-4">

    <!-- EXCLUIR CONTA -->
    <div class="text-center">
      <button id="btnExibirConfirmacao" class="btn btn-outline-danger px-4">
        <i class="bi bi-trash"></i> Excluir minha conta
      </button>

      <div id="confirmacaoExclusao" class="mt-3 d-none">
        <p class="fw-bold text-danger">Tem certeza que deseja excluir sua conta?</p>

        <a href="excluir_usuario.php" class="btn btn-danger px-4">
          <i class="bi bi-exclamation-octagon"></i> Excluir
        </a>

        <button id="btnCancelarExclusao" class="btn btn-secondary px-4 ms-2">Cancelar</button>
      </div>
    </div>
</div>

<!-- Footer simples -->
<footer class="text-center py-3">
        <!-- Só pra deixar um espaço em baixo -->
</footer>

<script>
// Toggle exclusão
document.getElementById("btnExibirConfirmacao").addEventListener("click", function () {
    document.getElementById("confirmacaoExclusao").classList.remove("d-none");
    this.classList.add("d-none");
});
document.getElementById("btnCancelarExclusao").addEventListener("click", function () {
    document.getElementById("confirmacaoExclusao").classList.add("d-none");
    document.getElementById("btnExibirConfirmacao").classList.remove("d-none");
});
</script>

</body>
</html>
