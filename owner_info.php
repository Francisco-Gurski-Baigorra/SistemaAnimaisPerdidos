<?php
session_start();
include('conecta.php');

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    exit; 
}

$id = $_GET['usuario_id'];

$sql = "SELECT id, nome, telefone, email FROM usuarios WHERE id = $id";
$resultado = mysqli_query($conexao, $sql);
$user = mysqli_fetch_assoc($resultado);

if ($user) {
    echo json_encode($user);
}

exit;