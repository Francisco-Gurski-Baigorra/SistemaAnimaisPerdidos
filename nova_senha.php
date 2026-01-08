<?php
session_start();
include('conecta.php');

$email = isset($_GET['email']) ? $_GET['email'] : null;
$token = isset($_GET['token']) ? $_GET['token'] : null;

if (!$email || !$token) {
    die("Link inválido. Parâmetros ausentes.");
}

$sql = "SELECT * FROM recuperar_senha WHERE email='$email' AND token='$token' AND usado=0";
$resultado = mysqli_query($conexao, $sql);
$recuperar = mysqli_fetch_assoc($resultado);

if (!$recuperar) {
    die("Token inválido ou já utilizado.");
}

date_default_timezone_set('America/Sao_Paulo');
$agora = new DateTime('now');
$data_criacao = new DateTime($recuperar['data']);
$data_criacao->modify('+1 day');

if ($agora > $data_criacao) {
    die("Este link expirou. Faça um novo pedido de recuperação.");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha - Rastreia Bicho</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            background-color: #f2f2f2; 
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 160px; 
            padding-bottom: 70px;
        }

        .navbar {
            background-color: #179e46ff;
            padding: 1rem;
            border-bottom: 3px solid #2e3531ff;
            box-shadow: 0 2px 6px rgba(54, 51, 51, 0.15);
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.7rem;
            color: #2b2b2b !important;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: 0.2s;
        }

        .navbar-brand:hover {
            transform: translateY(-2px) scale(1.04);
            opacity: 0.9;
        }

        .card-redefinir {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border: 1px solid #dcdcdc;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 25px;
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

<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fa-solid fa-paw me-2"></i> RASTREIA BICHO
        </a>

        <div class="ms-auto">
            <a href="login.php" class="btn btn-dark me-2">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </a>
            <a href="cadastro.php" class="btn btn-dark">
                <i class="bi bi-person-plus"></i> Registrar Conta
            </a>
        </div>
    </div>
</nav>

<div class="card-redefinir">
    <h3 class="text-center fw-bold mb-3"><i class="bi bi-key-fill"></i> Redefinir Senha</h3>
    <p class="text-muted text-center small">Escolha sua nova senha para continuar.</p>

    <form action="salvar_senha.php" method="post">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

        <div class="mb-3">
            <label class="form-label"><strong>Nova senha:</strong></label>
            <input type="password" name="senha" class="form-control" placeholder="Preencha a nova senha" required>
        </div>

        <div class="mb-3">
            <label class="form-label"><strong>Confirmar senha:</strong></label>
            <input type="password" name="senha2" class="form-control" placeholder="Confirme a nova senha" required>
        </div>

        <button type="submit" class="btn btn-success w-100 fw-bold">Salvar nova senha</button>
    </form>

    <div class="mt-3 text-center">
        <a href="login.php" class="text-muted small text-decoration-none">⬅ Voltar ao login</a>
    </div>
</div>

<footer class="footer-rastreia">
    © 2025 Rastreia Bicho
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>