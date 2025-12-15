<!DOCTYPE html>
<html lang="pt-BR">
<head>

          <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #ffffffff;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;

            /* 🔹 Centraliza o card */
            display: flex;
            flex-direction: column;
        }

        /* ======= Navbar igual ao index.php ======= */
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
        }

        .navbar-brand:hover {
            transform: translateY(-2px) scale(1.04);
            opacity: 0.9;
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

            /* 🔹 Remove sublinhado dos botões */
            text-decoration: none !important;
        }

        .navbar .btn:hover {
            transform: translateY(-2px);
            text-decoration: none !important;
        }

        .card-recuperar {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
            background: white;
            text-align: center;

            /* 🔹 Centralização perfeita */
            margin: 150px auto;
        }

        .btn-verde {
            background-color: #28a745;
            color: white;
            font-weight: bold;
            text-decoration: none !important;
        }

        .btn-verde:hover {
            background-color: #218838;
            text-decoration: none !important;
        }

        a {
            text-decoration: none;
            color: #155724;
            font-weight: bold;
        }

        a:hover {
            text-decoration: none;
            color: #0b3d1e;
        }

        @media (max-width: 480px) {
            .card-perfil {
                padding: 18px;
                margin: 0 12px;
            }
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

<div class="card-recuperar">
    <h3 class="mb-3"><i class="bi bi-lock-fill"></i>  Recuperar Senha</h3>
    <p class="text-muted">Informe o email cadastrado para continuar.</p>

    <form action="recuperar.php" method="post">
        <div class="mb-3 text-start">
            <label class="form-label">Email:</label>
            <input type="email" name="email" class="form-control" placeholder="seuemail@exemplo.com" required>
        </div>

        <button type="submit" class="btn btn-verde w-100">Enviar</button>
    </form>

    <div class="mt-3">
        <a href="login.php">⬅ Voltar ao login</a>
    </div>
</div>

</body>
</html>
