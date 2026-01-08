<?php
session_start();
include("conecta.php");

if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] !== "administrador") {
    echo "<script>alert(' Você não tem permissão para acessar esta área!'); window.location='index.php';</script>";
    exit;
}

$sql = "SELECT * FROM usuarios ORDER BY id DESC";
$resultado = mysqli_query($conexao, $sql);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Usuários</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            background-color: #ffffff;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
        }

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
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: transform 0.2s ease, opacity 0.2s ease;
            cursor: pointer;
            text-decoration: none;
        }

        .navbar-brand:hover {
            transform: translateY(-2px) scale(1.04);
            opacity: 0.9;
        }

        .card { border-radius: 15px; }
        .table thead { background: #179e46ff; color: white; }
        .btn-editar { background-color: #f3f053ff; color: black; }
        .btn-excluir { background-color: #df4e5cff; color: white; }
        .btn-voltar { background-color: #179e46ff; color: white; }
        .no-break { white-space: nowrap; }

        .footer-rastreia {
            background-color: #179e46ff;
            color: #333;
            text-align: center;
            padding: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            width: 100%;
            border-top: 2px solid #2e3531ff;
            margin-top: auto;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="admin.php">
            <i class="fa-solid fa-paw me-2"></i> RASTREIA BICHO
        </a>
    </div>
</nav>

<div class="container mt-4 mb-5"> 
    <div class="card p-4 shadow">
        <h2 class="text-center mb-4"><i class="bi bi-people-fill"></i> Gerenciar Usuários</h2>

        <div class="text-end mb-3">
            <a href="admin.php" class="btn btn-voltar"><i class="bi bi-arrow-left-circle"></i> Voltar</a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Endereço</th>
                        <th>Nascimento</th>
                        <th>Tipo</th>
                        <th>Ativo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($usuario = mysqli_fetch_assoc($resultado)): ?>
                    <tr>
                        <td><?php echo $usuario["id"]; ?></td>
                        <td><?php echo $usuario["nome"]; ?></td>
                        <td><?php echo $usuario["email"]; ?></td>
                        <td class="no-break"><?php echo $usuario["telefone"]; ?></td>
                        <td><?php echo $usuario["endereco"]; ?></td>
                        <td><?php echo date("d/m/Y", strtotime($usuario["data_nascimento"])); ?></td>
                        <td class="no-break">
                            <?php 
                            if ($usuario["tipo_usuario"] === "administrador") {
                                echo "<i class='bi bi-gear'></i> Administrador";
                            } else {
                                echo "<i class='bi bi-person-fill'></i> Usuário";
                            }
                            ?>
                        </td>

                        <td class="no-break">
                            <?php echo $usuario["ativo"] == "sim" ? "🟢 Sim" : "🔴 Não"; ?>
                        </td>

                        <td class="no-break">
                            <a href="editar_usuario.php?id=<?php echo $usuario['id']; ?>" class="btn btn-editar btn-sm">
                                <i class="bi bi-pencil-square"></i> Editar
                            </a>

                            <a href="adm_excluir_usuario.php?id=<?php echo $usuario['id']; ?>" 
                               class="btn btn-excluir btn-sm"
                               onclick="return confirm('Tem certeza que deseja excluir este usuário?')">
                               <i class="bi bi-trash"></i> Excluir
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<footer class="footer-rastreia">
    © 2025 Rastreia Bicho
</footer>

</body>
</html>