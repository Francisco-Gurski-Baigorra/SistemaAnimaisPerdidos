<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Login - Rastreia Bicho 🐾</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
 body {
    background-color: #ffffffff;
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

@media (max-width: 480px) {
    .card-perfil {
        padding: 18px;
        margin: 0 12px;
    }
}

/* =======================================
   COR E CENTRALIZAÇÃO DO LOGIN
======================================= */
/* Fundo claro */
body {
    background-color: #f2f2f2; 
    min-height: 100vh;
    padding-top: 160px; /* espaço da navbar */
    display: flex;
    justify-content: center; /* centraliza horizontalmente */
    align-items: flex-start; /* impede esticar vertical */
}


/* Card no estilo da imagem */
.card-login {
    width: 100%;
    max-width: 420px;
    background: #ffffff;
    border: 1px solid #dcdcdc;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}


</style>
</head>
<body>

<!-- =======================================
     NAVBAR COM PHP
======================================= -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fa-solid fa-paw me-2"></i> RASTREIA BICHO
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
                    <i class="fa-solid fa-paw"></i> Animais Registrados
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

<!-- =======================================
     CARD CENTRALIZADO DO LOGIN
======================================= -->
<div class="card card-login p-4">
  <div class="text-center mb-3">
    <?php if (isset($_GET['erro'])): ?>
    <div class="alert alert-danger py-2 text-center">
        <?php if ($_GET['erro'] == 1): ?>
            ❌ <strong>Email não encontrado.</strong>
        <?php elseif ($_GET['erro'] == 2): ?>
            ❌ <strong>Senha incorreta.</strong>
        <?php endif; ?>
    </div>
<?php endif; ?>

    <h3 class="fw-bold text-dark">Rastreia Bicho 🐾</h3>
    <p class="text-muted">Entre na sua conta</p>
  </div>

  <form action="verifica_login.php" method="POST">
    <div class="mb-3">
      <label class="form-label"><strong> Email: </strong> </label>
      <input type="email" class="form-control" name="email" 
       value="<?= isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '' ?>" 
       placeholder="Digite seu email" required>


    <div class="mb-3">
      <label class="form-label"><strong> Senha: </strong></label>
      <input type="password" class="form-control" name="senha" placeholder="Digite sua senha" required>
    </div>

    <button type="submit" class="btn btn-success w-100">Entrar</button>
  </form>

  <div class="text-center mt-3">
    <p class="mb-1">Ainda não possui uma conta? 
      <a href="cadastro.php" class="text-success">Fazer Cadastro</a>
    </p>
    <p><a href="recuperar_senha.php" class="text-muted">Esqueceu a senha?</a></p>
  </div>
</div>

</body>
</html>
