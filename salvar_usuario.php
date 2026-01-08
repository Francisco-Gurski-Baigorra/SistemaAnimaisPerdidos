<?php
include('conecta.php');

// Pega os dados do formulário direto, sem limpeza ou segurança adicional
$nome = $_POST['nome'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];
$endereco = $_POST['endereco'];
$data_nascimento = $_POST['data_nascimento'];
$senha = $_POST['senha'];
$confirmar_senha = $_POST['confirmar_senha'];

// Verifica se as senhas coincidem (Lógica básica necessária)
if ($senha !== $confirmar_senha) {
    echo "<script>alert('As senhas não coincidem!'); window.history.back();</script>";
    exit;
}

// Verifica se o e-mail já existe (Usando query procedural simples)
$sql_busca = "SELECT id FROM usuarios WHERE email = '$email'";
$resultado_busca = mysqli_query($conexao, $sql_busca);

if (mysqli_num_rows($resultado_busca) > 0) {
    echo "<script>alert('Este e-mail já está cadastrado!'); window.history.back();</script>";
    exit;
}

// Criptografa a senha para o banco aceitar o login depois
$senha_cripto = password_hash($senha, PASSWORD_DEFAULT);

// Insere o usuário com SQL direto e variáveis soltas na string
$sql_insert = "INSERT INTO usuarios (nome, email, telefone, endereco, data_nascimento, senha, tipo_usuario, ativo)
               VALUES ('$nome', '$email', '$telefone', '$endereco', '$data_nascimento', '$senha_cripto', 'usuario', 'sim')";

// Executa a query no padrão simples
if (mysqli_query($conexao, $sql_insert)) {
    echo "<script>alert('Usuário cadastrado com sucesso!'); window.location='login.php';</script>";
} else {
    echo "Erro ao cadastrar: " . mysqli_error($conexao);
}
?>