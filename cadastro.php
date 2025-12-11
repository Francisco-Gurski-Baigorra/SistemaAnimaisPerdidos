<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cadastro - Rastreia Bicho 🐾</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

/* =======================================
   NAVBAR (mesma do login)
======================================= */
.navbar {
    background-color: #179e46ff;
    padding: 1rem;
    border-bottom: 3px solid #2e3531ff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
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

/* =======================================
   FUNDO + CENTRALIZAÇÃO (igual login)
======================================= */
body {
    background-color: #f2f2f2;
    min-height: 100vh;
    padding-top: 120px; /* mesma distância da navbar */
    display: flex;
    justify-content: center;
    align-items: flex-start;
}

/* Card igual ao login */
.card-cadastro {
    width: 100%;
    max-width: 420px;
    background: #ffffff;
    border: 1px solid #dcdcdc;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Inputs padronizados */
.form-control {
    border-radius: 5px;
    border: 1px solid #ccc;
    height: 42px;
}

/* Botão igual ao login */
.btn-success {
    background-color: #179e46ff;
    border: none;
    height: 45px;
}

.btn-success:hover {
    background-color: #12843b;
}

</style>
</head>
<body>

<!-- =======================================
     NAVBAR (copiada do login)
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
     FORMULÁRIO DE CADASTRO
======================================= -->
<div class="card card-cadastro p-4">
  <div class="text-center mb-3">
    <h3 class="fw-bold text-dark">Rastreia Bicho 🐾</h3>
    <p class="text-muted">Crie sua conta</p>
  </div>

  <form action="salvar_usuario.php" method="POST">
    <div class="mb-3">
      <label class="form-label"><strong>Nome completo:</strong></label>
      <input type="text" class="form-control" name="nome" required>
    </div>

    <div class="mb-3">
      <label class="form-label"><strong>Email:</strong></label>
      <input type="email" class="form-control" name="email" required>
    </div>

    <div class="mb-3">
      <label class="form-label"><strong>Senha:</strong></label>
      <input type="password" class="form-control" name="senha" required>
    </div>

    <div class="mb-3">
      <label class="form-label"><strong>Confirmar senha:</strong></label>
      <input type="password" class="form-control" name="confirmar_senha" required>
    </div>

    <div class="mb-3">
      <label class="form-label"><strong>Telefone:</strong></label>
      <input type="text" class="form-control" name="telefone" placeholder="(xx) xxxxx-xxxx">
    </div>

    <div class="mb-3">
      <label class="form-label"><strong>Endereço:</strong></label>
      <input type="text" class="form-control" name="endereco">
    </div>

    <div class="mb-3">
      <label class="form-label"><strong>Data de nascimento:</strong></label>
      <input type="date" class="form-control" name="data_nascimento" required>
    </div>

    <button type="submit" class="btn btn-success w-100">Cadastrar</button>
  </form>

  <div class="text-center mt-3">
    <p>Já tem uma conta? <a href="login.php" class="text-success">Fazer login</a></p>
  </div>
</div>

</body>
</html>
