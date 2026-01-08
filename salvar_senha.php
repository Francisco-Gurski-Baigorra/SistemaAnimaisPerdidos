<?php
include('conecta.php');

$email  = $_POST['email'];
$token  = $_POST['token'];
$senha  = $_POST['senha'];
$senha2 = $_POST['senha2'];

if ($senha !== $senha2) {
    die("As senhas não coincidem!");
}

$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

$sql1 = "UPDATE usuarios SET senha='$senha_hash' WHERE email='$email'";
mysqli_query($conexao, $sql1);

// marcar token como usado
$sql2 = "UPDATE recuperar_senha SET usado=1 WHERE email='$email' AND token='$token'";
mysqli_query($conexao, $sql2);

echo "<script>alert('Senha alterada com sucesso! Faça login novamente.'); window.location='login.php';</script>";
?>