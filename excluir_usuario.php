<?php
session_start();
require_once "conexao.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['usuario_id'];

// Excluir usuário
$sql = "DELETE FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {

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
    echo "
        <script>
            alert('Erro ao excluir conta.');
            window.location.href = 'perfil.php';
        </script>
    ";
}

$stmt->close();
$conn->close();
?>
