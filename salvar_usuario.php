<?php
include('conecta.php');

// Pega os dados do formulário
$nome = $_POST['nome'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];
$endereco = $_POST['endereco'];
$data_nascimento = $_POST['data_nascimento'];
$senha = $_POST['senha'];
$confirmar_senha = $_POST['confirmar_senha'];

// Verifica se as senhas coincidem
if ($senha !== $confirmar_senha) {
    echo "<script>alert('As senhas não coincidem!'); window.history.back();</script>";
    exit;
}

// Limpeza básica para evitar erros de aspas no SQL
$nome = mysqli_real_escape_string($conexao, $nome);
$email = mysqli_real_escape_string($conexao, $email);

// Verifica se o e-mail já existe (Usando query simples)
$sql_busca = "SELECT id FROM usuarios WHERE email = '$email'";
$resultado_busca = mysqli_query($conexao, $sql_busca);

if (mysqli_num_rows($resultado_busca) > 0) {
    echo "<script>alert('Este e-mail já está cadastrado!'); window.history.back();</script>";
    exit;
}

// Criptografa a senha
$senha_cripto = password_hash($senha, PASSWORD_DEFAULT);

// Insere o usuário com SQL direto
$sql_insert = "INSERT INTO usuarios (nome, email, telefone, endereco, data_nascimento, senha, tipo_usuario, ativo)
               VALUES ('$nome', '$email', '$telefone', '$endereco', '$data_nascimento', '$senha_cripto', 'usuario', 'sim')";

if (mysqli_query($conexao, $sql_insert)) {
    echo "<script>alert('Usuário cadastrado com sucesso!'); window.location='login.php';</script>";
} else {
    echo "Erro ao cadastrar: " . mysqli_error($conexao);
}

mysqli_close($conexao);
?>