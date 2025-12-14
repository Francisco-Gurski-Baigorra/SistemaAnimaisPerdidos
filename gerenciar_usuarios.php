<?php
session_start();
include("conecta.php");

// 🔒 Verifica se é administrador
if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] !== "administrador") {
    echo "<script>alert('❌ Você não tem permissão para acessar esta área!'); window.location='index.php';</script>";
    exit;
}

// Busca todos os usuários
$sql = "SELECT * FROM usuarios ORDER BY id DESC";
$resultado = $conexao->query($sql);

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
    /* Removido o padding-top desnecessário */
    font-family: Arial, sans-serif;
    display: flex; /* Para o layout de rodapé fixo no final (footer) */
    flex-direction: column; /* Para o layout de rodapé fixo no final (footer) */
}


/* ======= Navbar ======= */
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
}
.card {
    border-radius: 15px;
}
.table thead {
    background: #179e46ff;
    color: white;
}
.btn-editar {
    background-color: #ffc107;
    color: black;
}
.btn-excluir {
    background-color: #dc3545;
    color: white;
}
.btn-voltar {
    background-color: #179e46ff;
    color: white;
}

/* FOOTER CORRIGIDO */
.footer-rastreia {
    background-color: #179e46ff;
    color: #333;
    text-align: center;
    padding: 12px;
    font-size: 0.95rem;
    font-weight: 600;
    width: 100%;
    border-top: 2px solid #2e3531ff;
    margin-top: auto; /* Garante que ocupe o espaço restante e vá para o final */
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


<div class="container mt-4 mb-5"> <div class="card p-4 shadow">
        <h2 class="text-center mb-4">👥 Gerenciar Usuários</h2>

        <div class="text-end mb-3">
            <a href="admin.php" class="btn btn-voltar"><i class="bi bi-arrow-left"></i> Voltar</a>
        </div>

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
                <?php while ($usuario = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= $usuario["id"] ?></td>
                    <td><?= $usuario["nome"] ?></td>
                    <td><?= $usuario["email"] ?></td>
                    <td><?= $usuario["telefone"] ?></td>
                    <td><?= $usuario["endereco"] ?></td>
                    <td><?= date("d/m/Y", strtotime($usuario["data_nascimento"])) ?></td>
                    <td>
                    <?= $usuario["tipo_usuario"] === "administrador" ? "⚙️ Administrador" : "👤 Usuário" ?>
                    </td>

                    <td><?= $usuario["ativo"] == "sim" ? "🟢 Sim" : "🔴 Não" ?></td>

                    <td>
                        <a href="editar_usuario.php?id=<?= $usuario['id'] ?>" class="btn btn-editar btn-sm">
                            <i class="bi bi-pencil-square"></i> Editar
                        </a>

                        <a href="adm_excluir_usuario.php?id=<?= $usuario['id'] ?>" 
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

<footer class="footer-rastreia">
    © 2025 Rastreia Bicho
</footer>

</body>
</html>