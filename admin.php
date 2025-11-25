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

    <style>
        body {
            background-color: #9fccebff;
            min-height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card-admin {
            width: 100%;
            max-width: 500px;
            padding: 25px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 12px rgba(0,0,0,0.15);
            text-align: center;
        }

        .btn-opcao {
            width: 100%;
            padding: 12px;
            margin-bottom: 10px;
            font-size: 18px;
            font-weight: bold;
        }

        .btn-usuarios {
            background-color: #0069d9;
            color: white;
        }

        .btn-usuarios:hover {
            background-color: #0052a3;
        }

        .btn-animais {
            background-color: #28a745;
            color: white;
        }

        .btn-animais:hover {
            background-color: #218838;
        }

        .logout {
            margin-top: 15px;
            display: block;
            font-weight: bold;
            color: #333;
        }

        .logout:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="card-admin">
        <h2 class="mb-3">Painel do Administrador</h2>
        <p class="text-muted">Gerencie usuários e animais do sistema.</p>

        <a href="gerenciar_usuarios.php" class="btn btn-opcao btn-usuarios">👥 Gerenciar Usuários</a>
        <a href="gerenciar_animais.php" class="btn btn-opcao btn-animais">🐾 Gerenciar Animais</a>

        <a href="logout.php" class="logout">Sair</a>
    </div>

</body>
</html>
