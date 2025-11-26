<?php
session_start();
require '../conexao.php';

// Apenas admin pode salvar edições
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] != 'administrador') {
    header("Location: ../login.php");
    exit;
}

$id = $_POST['id'];
$nome = $_POST['nome'];
$situacao = $_POST['situacao'];
$especie = $_POST['especie'];
$genero = $_POST['genero'];
$raca = $_POST['raca'];
$porte = $_POST['porte'];
$cor = $_POST['cor'];
$idade = $_POST['idade'];
$telefone = $_POST['telefone'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];

// FOTO
$novaFoto = null;
if (!empty($_FILES['foto']['name'])) {
    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $novoNome = "animal_" . time() . "." . $ext;

    move_uploaded_file($_FILES['foto']['tmp_name'], "../uploads/" . $novoNome);
    $novaFoto = $novoNome;
}

if ($novaFoto) {
    $sql = "UPDATE animais SET nome=?, situacao=?, especie=?, genero=?, raca=?, porte=?, cor=?, idade=?, telefone=?, latitude=?, longitude=?, foto=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssss si", 
        $nome, $situacao, $especie, $genero, $raca, $porte, $cor, $idade, $telefone, 
        $latitude, $longitude, $novaFoto, $id
    );
} else {
    $sql = "UPDATE animais SET nome=?, situacao=?, especie=?, genero=?, raca=?, porte=?, cor=?, idade=?, telefone=?, latitude=?, longitude=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssi", 
        $nome, $situacao, $especie, $genero, $raca, $porte, $cor, $idade, $telefone, 
        $latitude, $longitude, $id
    );
}

$stmt->execute();

header("Location: gerenciar_animais.php?edit=success");
exit;
