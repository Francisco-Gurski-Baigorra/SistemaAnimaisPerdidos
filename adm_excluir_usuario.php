<?php
include("conecta.php");

$id = $_GET["id"];
$sql = "DELETE FROM usuarios WHERE id = $id";

if ($conexao->query($sql)) {
    echo "<script>alert('Usuário excluído com sucesso!'); window.location='gerenciar_usuarios.php';</script>";
}
?>
