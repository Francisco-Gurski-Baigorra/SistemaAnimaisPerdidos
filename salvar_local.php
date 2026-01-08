<?php
session_start();
include('conecta.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    die("Erro: Você precisa estar logado.");
}

$usuario_id = $_SESSION['usuario_id'];

// --- COLETA DOS DADOS DIRETO DO POST ---
$nome = $_POST['nome'];
$situacao = $_POST['situacao'];
$especie = $_POST['especie'];
$genero = $_POST['genero'];
$raca_id = $_POST['raca_id'];
$data_ocorrido = $_POST['data_ocorrido'];
$telefone_contato = $_POST['telefone_contato'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];
$porte = $_POST['porte'];
$cor_predominante = $_POST['cor_predominante'];
$idade = $_POST['idade'];
$descricao = $_POST['descricao'];

$foto_nome = "";
if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $extensao = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $foto_nome = "animal_" . time() . "." . $extensao;
    move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/" . $foto_nome);
}

$sql = "INSERT INTO animais (
            usuario_id, nome, situacao, especie, genero, raca_id, porte,
            cor_predominante, idade, descricao, telefone_contato,
            data_ocorrido, latitude, longitude, foto
        ) VALUES (
            '$usuario_id', '$nome', '$situacao', '$especie', '$genero', '$raca_id', '$porte',
            '$cor_predominante', '$idade', '$descricao', '$telefone_contato',
            '$data_ocorrido', '$latitude', '$longitude', '$foto_nome'
        )";

if (mysqli_query($conexao, $sql)) {
    echo "Sucesso: Animal cadastrado!";
} else {
    echo "Erro ao cadastrar: " . mysqli_error($conexao);
}
?>