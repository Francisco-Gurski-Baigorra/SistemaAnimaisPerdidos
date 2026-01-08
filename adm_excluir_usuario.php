<?php
include("conecta.php");
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    die("Erro: Acesso negado. Você não tem permissão para excluir usuários.");
}

if (isset($_GET["id"])) {
    $id = $_GET["id"];

    if ($id == $_SESSION['usuario_id']) {
        echo "<script>alert('Erro: Você não pode excluir sua própria conta!'); window.location='gerenciar_usuarios.php';</script>";
        exit;
    }

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