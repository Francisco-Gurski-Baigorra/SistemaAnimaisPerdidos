<?php
include("conecta.php");
session_start();

// 1. Verificação essencial: Só um administrador pode acessar este arquivo
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    die("Erro: Acesso negado. Você não tem permissão para excluir usuários.");
}

// 2. Coleta o ID da URL
if (isset($_GET["id"])) {
    $id = $_GET["id"];

    // Evitar que o administrador exclua a si próprio por acidente
    if ($id == $_SESSION['usuario_id']) {
        echo "<script>alert('Erro: Você não pode excluir sua própria conta!'); window.location='gerenciar_usuarios.php';</script>";
        exit;
    }

    // 3. Executa a exclusão de forma procedural simples
    $sql = "DELETE FROM usuarios WHERE id = '$id'";

    if (mysqli_query($conexao, $sql)) {
        echo "<script>alert('Usuário excluído com sucesso!'); window.location='gerenciar_usuarios.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir usuário: " . mysqli_error($conexao) . "'); window.location='gerenciar_usuarios.php';</script>";
    }

} else {
    header("Location: gerenciar_usuarios.php");
}
?>