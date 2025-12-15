<?php
include('conecta.php');
session_start();

// Apenas admin
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    echo "<script>alert('❌ Você não tem permissão!'); window.location='index.php';</script>";
    exit;
}

// Verifica ID
if (!isset($_GET['id'])) {
    echo "<script>alert('ID inválido!'); window.location='gerenciar_animais.php';</script>";
    exit;
}

$id = intval($_GET['id']);

// Buscar foto antes de excluir
$sqlFoto = "SELECT foto FROM animais WHERE id = ?";
$stmtFoto = $conexao->prepare($sqlFoto);
$stmtFoto->bind_param("i", $id);
$stmtFoto->execute();
$resultFoto = $stmtFoto->get_result();

if ($resultFoto->num_rows === 0) {
    echo "<script>alert('Animal não encontrado!'); window.location='gerenciar_animais.php';</script>";
    exit;
}

$animal = $resultFoto->fetch_assoc();

// Excluir registro
$sql = "DELETE FROM animais WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    // Apagar foto se existir
    if (!empty($animal['foto']) && file_exists("uploads/" . $animal['foto'])) {
        unlink("uploads/" . $animal['foto']);
    }

    echo "<script>alert(' Animal excluído com sucesso!'); window.location='gerenciar_animais.php';</script>";
} else {
    echo "<script>alert('Erro ao excluir!'); window.location='gerenciar_animais.php';</script>";
}

$stmt->close();
$conexao->close();
?>
