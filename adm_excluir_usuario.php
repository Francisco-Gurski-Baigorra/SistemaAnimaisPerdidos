<?php
include("conecta.php");

if (!isset($_GET["id"])) {
    echo "<script>alert('ID inválido!'); window.location='gerenciar_usuarios.php';</script>";
    exit;
}

$id = $_GET["id"];

$sql = "DELETE FROM usuarios WHERE id = $id";

if ($conexao->query($sql)) {
    echo "<script>alert('Usuário excluído com sucesso!'); window.location='gerenciar_usuarios.php';</script>";
} else {
    echo "<script>alert('Erro ao excluir!'); window.location='gerenciar_usuarios.php';</script>";
}
?>
