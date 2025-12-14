<?php
session_start();

// Impede acesso caso não esteja logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Impede acesso caso não seja administrador
if ($_SESSION['tipo_usuario'] !== 'administrador') {
    echo "<script>alert('❌ Você não tem permissão para acessar esta área!'); window.location='index.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Administrador</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


<style>

body {
    background-color: #ffffff;
    min-height: 100vh;
    margin: 0;
    padding-top: 0;
    font-family: Arial, sans-serif;
    display: flex;
    flex-direction: column;
}


/* ======= Navbar ======= */
.navbar {
    background-color: #179e46;
    padding: 1rem;
    border-bottom: 3px solid #2e3531;
    box-shadow: 0 2px 6px rgba(54, 51, 51, 0.15);
    width: 100%;
}

.navbar-brand {
    font-weight: bold;
    font-size: 1.7rem;
    color: #2b2b2b !important;
}
/* ===== CARD CENTRAL ===== */
.card-admin {
    width: 100%;
    max-width: 460px;
    background: #fff;
    border: 1px solid #dcdcdc;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    padding: 30px;
    text-align: center;
}

.card-admin h2 {
    font-weight: bold;
    color: #222;
}

.btn-opcao {
    width: 100%;
    padding: 14px;
    font-size: 18px;
    border-radius: 8px;
    margin-bottom: 12px;
    font-weight: 600;
    transition: 0.2s;
}

.btn-opcao:hover {
    transform: translateY(-2px);
}

.logout {
    font-weight: bold;
    display: inline-block;
    margin-top: 10px;
    text-decoration: none;
    color: #d92323;
}

.logout:hover {
    text-decoration: underline;
}

/* Centraliza o card considerando navbar e footer fixos */
.admin-wrapper {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    padding-top: 120px;  /* altura da navbar */
    padding-bottom: 80px; /* altura do footer */
}



</style>

</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="fa-solid fa-paw me-2"></i> RASTREIA BICHO
        </a>

        </div>
    </div>
</nav>

<div class="admin-wrapper">
<div class="card-admin">
    <h2 class="mb-3">Painel do Administrador</h2>
    <p class="text-muted mb-4">Gerencie usuários e animais do sistema.</p>

   <a href="gerenciar_usuarios.php" class="btn btn-opcao btn-primary">
    <i class="bi bi-people-fill me-2"></i> Gerenciar Usuários
</a>

<a href="gerenciar_animais.php" class="btn btn-opcao btn-success">
    <i class="fa-solid fa-paw me-2"></i> Gerenciar Animais

</a>

<a href="index.php" class="btn btn-opcao btn-secondary">
    <i class="bi bi-house-door-fill me-2"></i> Área Principal
</a>

<a href="logout.php" class="logout">
    <i class="bi bi-box-arrow-right me-1"></i> Sair
</a>

</div>
</div>

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
    position: fixed;
    bottom: 0;
    left: 0;
}

body {
    padding-bottom: 60px;
}
</style>


</body>
</html>
