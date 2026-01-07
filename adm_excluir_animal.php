<?php
include('conecta.php');
session_start();

$id = ($_GET['id']);

$sqlFoto = "SELECT foto FROM animais WHERE id = ?";
$stmtFoto = $conexao->prepare($sqlFoto);
$stmtFoto->bind_param("i", $id);
$stmtFoto->execute();
$resultFoto = $stmtFoto->get_result();

$animal = $resultFoto->fetch_assoc();

$sql = "DELETE FROM animais WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    unlink("uploads/" . $animal['foto']);
    echo "<script>alert(' Animal excluído com sucesso!'); window.location='gerenciar_animais.php';</script>"; //session eh paia
}

?>
