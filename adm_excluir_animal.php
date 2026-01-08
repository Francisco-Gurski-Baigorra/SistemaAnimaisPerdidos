<?php
include('conecta.php');
session_start();

if (isset($_GET['id'])) {
    $id = $_GET['id'];

$sqlFoto = "SELECT foto FROM animais WHERE id = '$id'";
$resFoto = mysqli_query($conexao, $sqlFoto);
$animal = mysqli_fetch_assoc($resFoto);

$sqlDelete = "DELETE FROM animais WHERE id = '$id'";
    
if (mysqli_query($conexao, $sqlDelete)) {
        
    if ($animal['foto'] != "") {
        $caminhoFoto = "uploads/" . $animal['foto'];
        if (file_exists($caminhoFoto)) {
            unlink($caminhoFoto);
        }
    }

        echo "<script>alert('Animal excluído com sucesso!'); window.location='gerenciar_animais.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir o animal.'); window.location='gerenciar_animais.php';</script>";
    }

} else {
    header("Location: gerenciar_animais.php");
}
?>