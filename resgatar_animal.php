<?php
session_start();
include('conecta.php');

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$id_usuario = $_SESSION['usuario_id'];

if (isset($_GET['id'])) {
    $id_animal = $_GET['id'];
} else {
    $id_animal = null;
}

$resgate = isset($_GET['resgate']);


if ($resgate) {
    $sql = "UPDATE animais 
            SET situacao = 'resgatado' 
            WHERE id = '$id_animal' AND usuario_id = '$id_usuario'";
    
    mysqli_query($conexao, $sql);

    header("Location: perfil_animais.php");
    exit;
}

$sql_delete = "DELETE FROM animais WHERE id = '$id_animal' AND usuario_id = '$id_usuario'";
mysqli_query($conexao, $sql_delete);

header("Location: perfil_animais.php");
exit;
?>