<?php
include("conecta.php");

// Recebe dados do POST
$id = $_POST["id"];
$nome = $_POST["nome"];
$email = $_POST["email"];
$telefone = $_POST["telefone"];
$endereco = $_POST["endereco"];
$data_nascimento = $_POST["data_nascimento"];
$tipo_usuario = $_POST["tipo_usuario"];
$ativo = $_POST["ativo"];

// Atualiza no banco
$sql = "UPDATE usuarios 
        SET nome='$nome', email='$email', telefone='$telefone', endereco='$endereco',
            data_nascimento='$data_nascimento', tipo_usuario='$tipo_usuario', ativo='$ativo'
        WHERE id = $id";

if ($conexao->query($sql)) {
    echo "<script>alert('Usuário atualizado com sucesso!'); window.location='gerenciar_usuarios.php';</script>";
} else {
    echo "<script>alert('Erro ao atualizar!'); window.location='gerenciar_usuarios.php';</script>";
}
?>
