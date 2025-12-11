<?php
include('conecta.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    // Se email não existir
   // Se email não existir → mantém o email no input
if ($resultado->num_rows === 0) {
    header("Location: login.php?erro=1&email=" . urlencode($email));
    exit;
}

if ($resultado->num_rows > 0) {
    $usuario = $resultado->fetch_assoc();

    if (password_verify($senha, $usuario['senha'])) {

        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

        if ($usuario['tipo_usuario'] === 'administrador') {
            header("Location: admin.php");
            exit;
        }

        header("Location: index.php");
        exit;

    } else {
        // SENHA ERRADA → mantém o email
        header("Location: login.php?erro=2&email=" . urlencode($email));
        exit;
    }

} else {
    // EMAIL NÃO EXISTE → limpa o email
    header("Location: login.php?erro=1");
    exit;
}


    // Login correto → criar sessão
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

    // Se for administrador, vai para painel admin
    if ($usuario['tipo_usuario'] === 'administrador') {
        header("Location: admin.php");
        exit;
    }

    // Usuário normal → tela inicial
    header("Location: index.php");
    exit;
}
