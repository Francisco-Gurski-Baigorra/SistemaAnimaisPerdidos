<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Rastreia Bicho 🐾</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

   <style>
    body {
        background-color: #ffffff;
        min-height: 100vh;
        margin: 0;
        font-family: Arial, sans-serif;
        display: flex;
        flex-direction: column;
        padding-bottom: 0;
    }

    .navbar {
        background-color: #179e46ff;
        padding: 1rem;
        border-bottom: 3px solid #2e3531ff;
        box-shadow: 0 2px 6px rgba(54, 51, 51, 0.15);
        width: 100%;
    }

    .navbar-brand {
        font-weight: bold;
        font-size: 1.7rem;
        color: #2b2b2b !important;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: transform 0.2s ease, opacity 0.2s ease;
        cursor: pointer;
        text-decoration: none;
    }
    .hero {
        background-color: #fefefe;
        padding: 20px 5px;
        text-align: center;
        flex: 1;
    }

    .hero h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #212529;
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
        position: relative;
        margin-top: auto; 
    }
    @media (max-width: 480px) {
        .navbar-brand { font-size: 1.4rem; }
    }
</style>
</head>
<body>

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

                <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'administrador'): ?>
                    <a href="admin.php" class="btn btn-primary me-2">
                        <i class="bi bi-gear-fill"></i> Administrador
                    </a>
                <?php endif; ?>

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

<section class="hero">
    <div class="text-center mt-3">
        <h2 class="fw-bold">Encontre seu animal perdido</h2>
        <p class="text-muted mx-auto">
            Este sistema web é uma ferramenta de busca para animais perdidos ou encontrados. 
            Utilize com o objetivo de encontrar seu animal ou reportar algum animal que encontrou.
        </p>

        <div class="d-flex justify-content-center gap-4 mt-3">
            <div>
                <a href="buscar_animais_perdidos.php" class="btn btn-success btn-lg px-4">
                    <i class="bi bi-search"></i> Animal de Estimação Desaparecido
                </a>
                <p class="mt-2 text-muted small">Veja os animais que foram <strong>perdidos</strong> e procuram seus tutores.</p>
            </div>

            <div>
                <a href="buscar_animais_encontrados.php" class="btn btn-success btn-lg px-4">
                    <i class="bi bi-search"></i> Animal de Estimação Encontrado
                </a>
                <p class="mt-2 text-muted small">Veja os animais que foram <strong>encontrados</strong> e aguardam seus tutores.</p>
            </div>
        </div>

        <div class="mt-4">
            <img src="uploads/animais.png" alt="Animais" class="img-fluid" style="max-width: 850px;">
        </div>
    </div>
</section>

<footer class="footer-rastreia">
    © 2025 Rastreia Bicho
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>