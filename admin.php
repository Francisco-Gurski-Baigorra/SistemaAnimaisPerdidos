<?php
session_start();

if ($_SESSION['tipo_usuario'] !== 'administrador') {
    echo "<script>alert('Você não tem permissão para acessar esta área!'); window.location='index.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Administrador - Rastreia Bicho</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #ffffff;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            padding-bottom: 60px; 
        }

        .navbar {
            background-color: #179e46;
            padding: 1rem;
            border-bottom: 3px solid #2e3531;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.7rem;
            color: #2b2b2b !important;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: transform 0.2s ease;
        }

        .navbar-brand:hover {
            transform: scale(1.02);
        }

        .admin-wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .card-admin {
            width: 100%;
            max-width: 460px;
            background: #fff;
            border: 1px solid #dcdcdc;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 40px 30px;
            text-align: center;
        }

        .card-admin h2 {
            font-weight: 800;
            color: #222;
            margin-bottom: 10px;
        }

        .btn-opcao {
            width: 100%;
            padding: 14px;
            font-size: 18px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-weight: 600;
            transition: 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
        }

        .btn-opcao:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .logout {
            font-weight: bold;
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #d92323;
            transition: 0.2s;
        }

        .logout:hover {
            color: #a81a1a;
            text-decoration: underline;
        }

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
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fa-solid fa-paw"></i> RASTREIA BICHO
        </a>
    </div>
</nav>

<div class="admin-wrapper">
    <div class="card-admin">
        <h2>Painel do Administrador</h2>
        <p class="text-muted mb-4">Seja bem-vindo. Opções de Gerenciamento.</p>

        <a href="gerenciar_usuarios.php" class="btn btn-opcao btn-primary">
            <i class="bi bi-people-fill"></i> Gerenciar Usuários
        </a>

        <a href="gerenciar_animais.php" class="btn btn-opcao btn-success">
            <i class="fa-solid fa-paw"></i> Gerenciar Animais
        </a>

        <a href="index.php" class="btn btn-opcao btn-secondary">
            <i class="bi bi-house-door-fill"></i> Área do Usuário
        </a>

        <a href="logout.php" class="logout">
            <i class="bi bi-box-arrow-right"></i> Sair do Sistema
        </a>
    </div>
</div>

<footer class="footer-rastreia">
    © 2025 Rastreia Bicho
</footer>

</body>
</html>