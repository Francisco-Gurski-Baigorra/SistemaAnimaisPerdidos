<?php
session_start();
include('conecta.php');

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$id_usuario = $_SESSION['usuario_id'];
$id_animal  = $_GET['id'] ?? null;
$resgate    = isset($_GET['resgate']);

if (!$id_animal) {
    header('Location: perfil_animais.php');
    exit;
}

/* =========================
   MARCAR COMO RESGATADO
========================= */
if ($resgate) {

    $sql = "UPDATE animais 
            SET situacao = 'resgatado' 
            WHERE id = ? AND usuario_id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ii", $id_animal, $id_usuario);
    $stmt->execute();

    header("Location: perfil_animais.php");
    exit;
}

/* =========================
   EXCLUSÃO REAL (opcional)
========================= */
$sql = "DELETE FROM animais WHERE id = ? AND usuario_id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("ii", $id_animal, $id_usuario);
$stmt->execute();

header("Location: perfil_animais.php");
exit;
