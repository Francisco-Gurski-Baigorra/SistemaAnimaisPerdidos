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

<style>

body {
    background-color:  #f2f2f2;
    min-height: 100vh;
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: Arial, sans-serif;
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

</style>

</head>
<body>

<div class="card-admin">
    <h2 class="mb-3">Painel do Administrador</h2>
    <p class="text-muted mb-4">Gerencie usuários e animais do sistema.</p>

    <a href="gerenciar_usuarios.php" class="btn btn-opcao btn-primary">
        👥 Gerenciar Usuários
    </a>

    <a href="gerenciar_animais.php" class="btn btn-opcao btn-success">
        🐾 Gerenciar Animais
    </a>

    <a href="logout.php" class="logout">Sair</a>
</div>

</body>
</html>
