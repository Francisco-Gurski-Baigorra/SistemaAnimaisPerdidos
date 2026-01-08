<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Rastreia Bicho 🐾</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
    body { background-color: #f2f2f2; min-height: 100vh; margin: 0; padding-top: 160px; font-family: Arial, sans-serif; }
    .form-wrapper { display: flex; justify-content: center; margin-bottom: 80px; }
    .navbar { background-color: #179e46ff; padding: 1rem; border-bottom: 3px solid #2e3531ff; box-shadow: 0 2px 6px rgba(54, 51, 51, 0.15); width: 100%; }
    .navbar-brand { font-weight: bold; font-size: 1.7rem; color: #2b2b2b !important; display: inline-flex; align-items: center; gap: 6px; transition: transform 0.2s ease, opacity 0.2s ease; cursor: pointer; text-decoration: none; }
    .navbar-brand:hover { transform: translateY(-2px) scale(1.04); opacity: 0.9; }
    .navbar .btn { padding: 7px 14px; border-radius: 8px; font-weight: 500; transition: 0.2s; }
    .navbar .btn:hover { transform: translateY(-2px); }
    .card-cadastro { width: 100%; max-width: 600px; background: #ffffff; border: 1px solid #dcdcdc; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 25px; }
    .footer-rastreia { background-color: #179e46ff; color: #333; text-align: center; padding: 12px; font-size: 0.95rem; font-weight: 600; width: 100%; border-top: 2px solid #2e3531ff; position: relative; bottom: 0; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fa-solid fa-paw me-2"></i> RASTREIA BICHO</a>
        <div class="ms-auto">
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="registrar_animal.php" class="btn btn-dark me-2"><i class="bi bi-plus-circle"></i> Registrar Animal</a>
                <a href="perfil.php" class="btn btn-dark me-2"><i class="bi bi-person-circle"></i> Perfil</a>
                <a href="perfil_animais.php" class="btn btn-dark me-2"><i class="fa-solid fa-paw"></i> Animais Registrados</a>
                <a href="logout.php" class="btn btn-danger me-2"><i class="bi bi-box-arrow-right"></i> Sair</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-dark me-2"><i class="bi bi-box-arrow-in-right"></i> Login</a>
                <a href="cadastro.php" class="btn btn-dark me-2"><i class="bi bi-person-plus"></i> Registrar Conta</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="form-wrapper">
    <div class="card card-cadastro p-4">
        <div class="text-center mb-3">
            <h3 class="fw-bold text-dark">Rastreia Bicho <i class="fa-solid fa-paw me-2"></i></h3>
            <p class="text-muted">Crie sua conta</p>
        </div>

        <form action="salvar_usuario.php" method="POST">
            <div class="mb-3">
                <label class="form-label"><strong>Nome:</strong></label>
                <input type="text" class="form-control" name="nome" placeholder="Digite seu Nome" required maxlength="35">
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Email:</strong></label>
                <input type="email" class="form-control" name="email" placeholder="Digite seu E-mail" required maxlength="50">
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Senha:</strong></label>
                <input type="password" class="form-control" name="senha" placeholder="Digite sua Senha" required maxlength="50">
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Confirmar Senha:</strong></label>
                <input type="password" class="form-control" name="confirmar_senha" placeholder="Confirme sua Senha" required maxlength="50">
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Telefone (11 dígitos):</strong></label>
                <input type="text" class="form-control" id="telefone" name="telefone" placeholder="(XX) XXXXX-XXXX" maxlength="15" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><strong>Endereço (Bairro, Rua e Número):</strong></label>
                <input type="text" class="form-control" name="endereco" placeholder="Ex: Centro, Rua A, Nº 123" required maxlength="50">
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
</div>

<script>
document.getElementById("telefone").addEventListener("input", function (e) {
    let value = e.target.value.replace(/\D/g, "");
    if (value.length > 11) value = value.slice(0, 11);
    if (value.length > 6) {
        e.target.value = `(${value.slice(0,2)}) ${value.slice(2,7)}-${value.slice(7)}`;
    } else if (value.length > 2) {
        e.target.value = `(${value.slice(0,2)}) ${value.slice(2)}`;
    } else {
        e.target.value = value;
    }
});
</script>

<footer class="footer-rastreia">
    © 2025 Rastreia Bicho
</footer>

</body>
</html>