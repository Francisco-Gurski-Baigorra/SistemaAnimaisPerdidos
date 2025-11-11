<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Rastreia Bicho 🐾</title>

    <!-- ✅ Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ✅ Ícones do Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f8f9fa;
        }

        /* Barra superior */
        .navbar {
            background-color: #b6e388;
            padding: 1rem;
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

        /* Área principal */
        .hero {
            background-color: #fefefe;
            padding: 60px 15px;
            text-align: center;
        }

        .hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #212529;
        }

        .hero p {
            max-width: 600px;
            margin: 0 auto 30px;
            font-size: 1.1rem;
            color: #555;
        }

        /* Botões principais */
        .btn-custom {
            background-color: #1e9f4b;
            color: #fff;
            font-size: 1.1rem;
            border-radius: 10px;
            padding: 15px 25px;
            margin: 10px;
            transition: 0.3s;
        }

        .btn-custom:hover {
            background-color: #198f43;
            transform: scale(1.03);
        }

        .btn-explanation {
            color: #444;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        /* Imagem dos animais */
        .animals-img {
            max-width: 100%;
            height: auto;
            margin-top: 40px;
        }

        footer {
            background-color: #b6e388;
            color: #333;
            text-align: center;
            padding: 10px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<!-- ✅ Barra superior -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="#">
            RASTREIA BICHO <i class="bi bi-paw-fill"></i>
        </a>

        <div class="ms-auto">
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="registrar_animal.php" class="btn btn-success me-2">Registrar Animal</a>
                <a href="perfil_animais.php" class="btn btn-outline-dark me-2"><i class="bi bi-person-circle"></i> Perfil</a>
                <a href="logout.php" class="btn btn-outline-danger">Sair</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-dark me-2"><i class="bi bi-box-arrow-in-right"></i> Login</a>
                <a href="cadastro.php" class="btn btn-outline-success me-2"><i class="bi bi-person-plus"></i> Registrar Conta</a>
                <button class="btn btn-success" onclick="alert('Você precisa fazer login para registrar um animal!')">Registrar Animal</button>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- ✅ Conteúdo principal -->
<section class="hero">
    <div class="text-center mt-3">
  <h2 class="fw-bold">Encontre seu animal perdido</h2>
  <p class="text-muted mx-auto" style="max-width: 600px;">
    Este sistema web é uma ferramenta de busca para animais perdidos ou encontrados. 
    Utilize com o objetivo de encontrar seu animal ou reportar um animal encontrado.
  </p>

  <!-- Botões principais -->
  <div class="d-flex justify-content-center gap-4 mt-3">
    <div>
      <a href="buscar_animais_perdidos.php" class="btn btn-success btn-lg px-4">
        <i class="bi bi-search"></i> Buscar Animal Perdido
      </a>
      <p class="mt-2 text-muted small">Veja os animais que foram <strong>perdidos</strong> e cadastrados no sistema.</p>
    </div>

    <div>
      <a href="buscar_animais_encontrados.php" class="btn btn-success btn-lg px-4">
        <i class="bi bi-person-search"></i> Buscar Animal Encontrado
      </a>
      <p class="mt-2 text-muted small">Veja os animais que foram <strong>encontrados</strong> e aguardam seus tutores.</p>
    </div>
  </div>

  <!-- Imagem dos animais -->
  <div class="mt-4">
    <img src="uploads/animais.png" alt="Animais" class="img-fluid" style="max-width: 850px;">
  </div>
</div>

</section>

</body>
</html>
