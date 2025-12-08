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
$usuario = $stmt->get_result()->fetch_assoc();

// Atualizar dados
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = $_POST["nome"];
    $telefone = $_POST["telefone"];
    $endereco = $_POST["endereco"];
    $data_nasc = $_POST["data_nascimento"];

    // Atualização comum
    $sqlUp = "UPDATE usuarios SET nome=?, telefone=?, endereco=?, data_nascimento=? WHERE id=?";
    $stmtUp = $conexao->prepare($sqlUp);
    $stmtUp->bind_param("ssssi", $nome, $telefone, $endereco, $data_nasc, $id);
    $stmtUp->execute();

    // Atualizar senha (se for preenchida)
    if (!empty($_POST["senha"]) && !empty($_POST["confirmar_senha"])) {

        if ($_POST["senha"] === $_POST["confirmar_senha"]) {
            $senhaHash = password_hash($_POST["senha"], PASSWORD_DEFAULT);

            $sqlSenha = "UPDATE usuarios SET senha=? WHERE id=?";
            $stmtSenha = $conexao->prepare($sqlSenha);
            $stmtSenha->bind_param("si", $senhaHash, $id);
            $stmtSenha->execute();

            $msg = "Senha alterada com sucesso!";
        } else {
            $msg = "As senhas não coincidem!";
        }
    } else {
        $msg = "Dados atualizados!";
    }

    // atualizar dados na página sem reload obrigatório
    header("Location: perfil.php?msg=" . urlencode($msg));
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
   <!-- Pacote de emojis de anomal -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 

<!-- icone de perfil -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<meta charset="UTF-8">
<title>Meu Perfil - Rastreia Bicho 🐾</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
  background-color: #b6e388;
  min-height: 100vh;
}

.navbar {
  background-color: #179e46ff;
}

.card {
  border-radius: 15px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.18);
}

.btn-success {
  background-color: #179e46ff;
  border: none;
}

.btn-success:hover {
  background-color: #12843b;
}
</style>

</head>
<body>
<nav class="navbar navbar-expand-lg" style="background-color: #179e46ff; padding: 1rem;">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3 text-dark" href="index.php">
            <i class="fa-solid fa-paw"></i><i class="bi bi-paw-fill me-2"></i> RASTREIA BICHO
        </a>

        <div class="ms-auto">
            <?php if (isset($_SESSION['usuario_id'])): ?>

                <a href="registrar_animal.php" class="btn btn-outline-dark me-2">
                    <i class="bi bi-plus-circle"></i> Registrar Animal
                </a>

                <a href="perfil.php" class="btn btn-outline-dark me-2">
                    <i class="bi bi-person-circle"></i> Perfil
                </a>

                <a href="perfil_animais.php" class="btn btn-outline-dark me-2">
                    <i class="fa-solid fa-paw"></i><i class="bi bi-paw-fill me-2"></i> Animais Registrados
                </a>

                <a href="logout.php" class="btn btn-outline-danger me-2">
                    <i class="bi bi-box-arrow-right"></i> Sair
                </a>

            <?php else: ?>

                <a href="login.php" class="btn btn-outline-dark me-2">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </a>

                <a href="cadastro.php" class="btn btn-outline-dark me-2">
                    <i class="bi bi-person-plus"></i> Registrar Conta
                </a>

            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container mb-5">
  <div class="row justify-content-center">
    <div class="col-md-6">

      <div class="card p-4">
        <h3 class="text-center mb-3 fw-bold">Meu Perfil</h3>

        <?php if (isset($_GET["msg"])): ?>
          <div class="alert alert-info text-center">
            <?= htmlspecialchars($_GET["msg"]) ?>
          </div>
        <?php endif; ?>

        <form method="POST">
          <!-- NOME -->
          <div class="mb-3">
            <label class="form-label">Nome completo</label>
            <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
          </div>

          <!-- EMAIL (somente leitura) -->
          <div class="mb-3">
            <label class="form-label">E-mail cadastrado</label>
            <input type="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" disabled>
          </div>

          <!-- TELEFONE -->
          <div class="mb-3">
            <label class="form-label">Telefone</label>
            <input type="text" name="telefone" class="form-control" value="<?= htmlspecialchars($usuario['telefone']) ?>">
          </div>

          <!-- ENDEREÇO -->
          <div class="mb-3">
            <label class="form-label">Endereço</label>
            <input type="text" name="endereco" class="form-control" value="<?= htmlspecialchars($usuario['endereco']) ?>">
          </div>

          <!-- DATA DE NASCIMENTO -->
          <div class="mb-3">
            <label class="form-label">Data de nascimento</label>
            <input type="date" name="data_nascimento" class="form-control" value="<?= htmlspecialchars($usuario['data_nascimento']) ?>">
          </div>

          <hr>

          <h5 class="fw-bold mt-3">Alterar senha</h5>

          <div class="mb-3">
            <label class="form-label">Nova senha</label>
            <input type="password" name="senha" class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label">Confirmar nova senha</label>
            <input type="password" name="confirmar_senha" class="form-control">
          </div>

          <button type="submit" class="btn btn-success w-100 mt-2">
            Salvar alterações
          </button>
        </form>

            <hr class="my-4">

<!-- Botão Excluir Conta -->
<div class="text-center">
  <button id="btnExibirConfirmacao" class="btn btn-outline-danger px-4">
    <i class="bi bi-trash"></i> Excluir minha conta
  </button>

  <!-- Confirmação (aparece só depois de clicar) -->
  <div id="confirmacaoExclusao" class="mt-3 d-none">
    <p class="fw-bold text-danger">Tem certeza que deseja excluir sua conta?</p>

    <a href="excluir_usuario.php" class="btn btn-danger px-4">
      <i class="bi bi-exclamation-octagon"></i> Excluir
    </a>

    <button id="btnCancelarExclusao" class="btn btn-secondary px-4 ms-2">
      Cancelar
    </button>
  </div>
</div>


      </div>

    </div>
  </div>
</div>

<footer class="text-center py-3 bg-light mt-auto">
  <p class="text-muted mb-0">&copy; <?= date("Y") ?> Rastreia Bicho 🐾</p>
</footer>


<script>
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
