<?php
include('conecta.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM usuarios WHERE email = '$email'";
    $resultado = mysqli_query($conexao, $sql);

    if (mysqli_num_rows($resultado) == 0) {
        header("Location: login.php?erro=1&email=" . urlencode($email));
        exit;
    }

    $usuario = mysqli_fetch_assoc($resultado);

    if (password_verify($senha, $usuario['senha'])) {
        
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

        if ($usuario['tipo_usuario'] === 'administrador') {
            header("Location: admin.php");
        } else {
            header("Location: index.php");
        }
        exit;

    } else {
        header("Location: login.php?erro=2&email=" . urlencode($email));
        exit;
    }
}
?>