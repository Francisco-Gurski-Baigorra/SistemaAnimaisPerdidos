<?php
session_start();
include('conecta.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$id_usuario = $_SESSION['usuario_id'];

// Simplificação do ?? para o padrão básico isset
if (isset($_GET['id'])) {
    $id_animal = mysqli_real_escape_string($conexao, $_GET['id']);
} else {
    $id_animal = null;
}

$resgate = isset($_GET['resgate']);

// Se não houver ID, volta para a listagem
if (!$id_animal) {
    header('Location: perfil_animais.php');
    exit;
}

/* =========================
   MARCAR COMO RESGATADO
========================= */
if ($resgate) {
    // SQL simples sem prepare/stmt
    $sql = "UPDATE animais 
            SET situacao = 'resgatado' 
            WHERE id = '$id_animal' AND usuario_id = '$id_usuario'";
    
    mysqli_query($conexao, $sql);

    header("Location: perfil_animais.php");
    exit;
}

/* =========================
   EXCLUSÃO REAL
========================= */
// SQL simples sem prepare/stmt
$sql_delete = "DELETE FROM animais WHERE id = '$id_animal' AND usuario_id = '$id_usuario'";
mysqli_query($conexao, $sql_delete);

header("Location: perfil_animais.php");
exit;
?>