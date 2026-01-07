<?php
include('conecta.php');
session_start();

// Verifica se o ID foi passado pela URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 1. Primeiro buscamos o nome da foto para poder apagar o arquivo da pasta
    $sqlFoto = "SELECT foto FROM animais WHERE id = '$id'";
    $resFoto = mysqli_query($conexao, $sqlFoto);
    $animal = mysqli_fetch_assoc($resFoto);

    // 2. Agora executamos o comando para deletar o registro do banco de dados
    $sqlDelete = "DELETE FROM animais WHERE id = '$id'";
    
    if (mysqli_query($conexao, $sqlDelete)) {
        
        // 3. Se deletou do banco, tentamos apagar a foto da pasta 'uploads'
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
    // Se não houver ID, volta para a lista
    header("Location: gerenciar_animais.php");
}
?>