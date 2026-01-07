<?php
session_start();
include 'conecta.php';


$id = $_POST['id'];
$nome = $_POST['nome'] ?? '';
$situacao = $_POST['situacao'] ?? '';
$especie = $_POST['especie'] ?? '';
$genero = $_POST['genero'] ?? '';
$raca_id = ($_POST['raca_id']) ?? '';
$porte = $_POST['porte'] ?? '';
$cor_predominante = $_POST['cor_predominante'] ?? '';
$idade = $_POST['idade'] ?? '';
$telefone_contato = $_POST['telefone_contato'] ?? '';
$latitude = !empty($_POST['latitude']) ? floatval($_POST['latitude']) : null;
$longitude = !empty($_POST['longitude']) ? floatval($_POST['longitude']) : null;


$novaFoto = null;
if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === 0) {
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $novoNome = "animal_" . time() . "_" . rand(1000,9999) . "." . $ext; // so pra garantir que nao sobreescreva arquivos
    move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/" . $novoNome);
    $novaFoto = $novoNome;
}


if ($novaFoto) {
    $sql = "UPDATE animais SET 
            nome = ?, situacao = ?, especie = ?, genero = ?, raca_id = ?, porte = ?, 
            cor_predominante = ?, idade = ?, telefone_contato = ?, latitude = ?, longitude = ?, foto = ? 
            WHERE id = ?";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("ssssisssssssi",
    $nome,
    $situacao,
    $especie,
    $genero,
    $raca_id,
    $porte,
    $cor_predominante,
    $idade,
    $telefone_contato,
    $latitude,
    $longitude,
    $novaFoto,
    $id
);
}//alteracao sem a nova foto pra foto antiga nao ser apagada
else {
    $sql = "UPDATE animais SET 
                nome = ?, situacao = ?, especie = ?, genero = ?, raca_id = ?, porte = ?, 
                cor_predominante = ?, idade = ?, telefone_contato = ?, latitude = ?, longitude = ? 
            WHERE id = ?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ssssissssssi",
        $nome,
        $situacao,
        $especie,
        $genero,
        $raca_id,
        $porte,
        $cor_predominante,
        $idade,
        $telefone_contato,
        $latitude,
        $longitude,
        $id
    );
}

$stmt->execute();
header("Location: gerenciar_animais.php?edit=success");
exit;