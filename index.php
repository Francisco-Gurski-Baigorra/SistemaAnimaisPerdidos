<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Rastreia Bicho</title>
</head>
<body>
    <h1>Rastreia Bicho 🐾</h1>

    <h2>Encontre seu animal perdido</h2>
    <p>Este sistema web é uma ferramenta de busca para animais perdidos ou encontrados.</p>

    <!-- Botões principais -->
    <a href="buscar_animais_perdidos.php"><button>Buscar Animal Perdido</button></a>
    <a href="buscar_animais_encontrados.php"><button>Buscar Animal Encontrado</button></a>

    <?php if (isset($_SESSION['usuario_id'])): ?>
        <!-- Se o usuário estiver logado -->
        <a href="registrar_animal.php"><button>Registrar Animal</button></a>
        <a href="perfil_animais.php"><button>Perfil do Animal</button></a>
        <a href="logout.php"><button>Sair</button></a>
    <?php else: ?>
        <!-- Se não estiver logado -->
        <a href="login.php"><button>Login</button></a>
        <a href="cadastro.php"><button>Registrar Conta</button></a>
        <button onclick="alert('Você precisa fazer login para registrar um animal!')">Registrar Animal</button>
    <?php endif; ?>
</body>
</html>
