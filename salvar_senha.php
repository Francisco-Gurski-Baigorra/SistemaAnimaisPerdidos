<?php
include('conecta.php');

// Coleta os dados do POST de forma direta
$email  = $_POST['email'];
$token  = $_POST['token'];
$senha  = $_POST['senha'];
$senha2 = $_POST['senha2'];

// Verifica se as senhas batem
if ($senha !== $senha2) {
    die("As senhas não coincidem!");
}

// Criptografa a nova senha (usando a função padrão do PHP)
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

// 1. Atualiza a senha na tabela de usuários (Lógica simples)
$sql1 = "UPDATE usuarios SET senha='$senha_hash' WHERE email='$email'";
mysqli_query($conexao, $sql1);

// 2. Marca o token como usado na tabela de recuperação (Lógica simples)
$sql2 = "UPDATE recuperar_senha SET usado=1 WHERE email='$email' AND token='$token'";
mysqli_query($conexao, $sql2);

echo "<script>alert('Senha alterada com sucesso! Faça login novamente.'); window.location='login.php';</script>";
?>