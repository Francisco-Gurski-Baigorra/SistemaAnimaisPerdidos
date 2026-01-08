<?php
session_start();
include('conecta.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Pega o ID da sessão de forma direta
$id = (int)$_SESSION['usuario_id'];

// SQL DIRETO (Estilo básico, sem prepare ou bind_param)
$sql = "DELETE FROM usuarios WHERE id = $id";

// Executa a query usando a função mysqli_query
if (mysqli_query($conexao, $sql)) {

    // Se a conta foi excluída, precisamos limpar a sessão para o usuário sair do sistema
    session_unset();
    session_destroy();

    echo "
        <script>
            alert('Conta excluída com sucesso.');
            window.location.href = 'login.php';
        </script>
    ";
    exit();

} else {
    // Caso ocorra algum erro (ex: se o banco impedir a exclusão por segurança)
    echo "
        <script>
            alert('Erro ao excluir conta.');
            window.location.href = 'perfil.php';
        </script>
    ";
}

// Fecha a conexão de forma básica
mysqli_close($conexao);
?>