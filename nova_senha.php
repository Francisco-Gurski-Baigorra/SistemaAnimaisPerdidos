<?php
include('conecta.php');

$email = $_GET['email'] ?? null;
$token = $_GET['token'] ?? null;

if (!$email || !$token) {
    die("Link inválido. Parâmetros ausentes.");
}

// Verifica se o token é válido
$sql = "SELECT * FROM recuperar_senha WHERE email=? AND token=? AND usado=0";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("ss", $email, $token);
$stmt->execute();
$resultado = $stmt->get_result();
$recuperar = $resultado->fetch_assoc();

if (!$recuperar) {
    die("Token inválido ou já utilizado.");
}

// Verifica validade (24h)
date_default_timezone_set('America/Sao_Paulo');
$agora = new DateTime('now');
$data_criacao = new DateTime($recuperar['data']);
$data_criacao->modify('+1 day');

if ($agora > $data_criacao) {
    die("Este link expirou. Faça um novo pedido de recuperação.");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #ffffffff;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
        }

        .card-redefinir {
            width: 100%;
            max-width: 400px;
            padding: 25px;
            border-radius: 15px;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }

        .btn-verde {
            background-color: #28a745;
            color: white;
            font-weight: bold;
        }

        .btn-verde:hover {
            background-color: #218838;
        }

        a {
            text-decoration: none;
            color: #155724;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
            color: #0b3d1e;
        }
    </style>
</head>

<body>

    <div class="card-redefinir">
        <h3 class="text-center mb-3">🔑 Redefinir Senha</h3>
        <p class="text-muted text-center">Escolha sua nova senha para continuar.</p>

        <form action="salvar_senha.php" method="post">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <div class="mb-3">
                <label class="form-label">Nova senha:</label>
                <input type="password" name="senha" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Confirmar senha:</label>
                <input type="password" name="senha2" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-verde w-100">Salvar nova senha</button>
        </form>

        <div class="mt-3 text-center">
            <a href="login.php">⬅ Voltar ao login</a>
        </div>
    </div>

</body>
</html>
