<?php
session_start();
include('conecta.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['usuario_id'];

$sql = "DELETE FROM usuarios WHERE id = $id";

if (mysqli_query($conexao, $sql)) {

session_unset();
session_destroy();
    echo "
        <script>
            alert('Conta excluída com sucesso.');
            window.location.href = 'login.php';
        </script>
    ";
    exit();

}
?>