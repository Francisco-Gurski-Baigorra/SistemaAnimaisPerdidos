<?php
include('conecta.php');
session_start();

// Verifica se os dados foram enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Busca o usuário pelo email (SQL direto)
    $sql = "SELECT * FROM usuarios WHERE email = '$email'";
    $resultado = mysqli_query($conexao, $sql);

    // 1. Verifica se o email existe
    if (mysqli_num_rows($resultado) == 0) {
        // Email não existe -> volta com erro 1 e mantém o email no campo
        header("Location: login.php?erro=1&email=" . urlencode($email));
        exit;
    }

    // Pega os dados do usuário encontrado
    $usuario = mysqli_fetch_assoc($resultado);

    // 2. Verifica se a senha está correta
    if (password_verify($senha, $usuario['senha'])) {
        
        // Login correto -> Criar sessões
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

        // Redireciona conforme o tipo de usuário
        if ($usuario['tipo_usuario'] === 'administrador') {
            header("Location: admin.php");
        } else {
            header("Location: index.php");
        }
        exit;

    } else {
        // Senha errada -> volta com erro 2 e mantém o email no campo
        header("Location: login.php?erro=2&email=" . urlencode($email));
        exit;
    }
}
?>