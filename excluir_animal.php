<?php
session_start();
include('conecta.php');

if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['mensagem'] = 'Você precisa estar logado para realizar esta ação.';
    header('Location: login.php');
    exit;
}

$id = $_GET['id'];
$usuario_id = $_SESSION['usuario_id'];

if (isset($_GET['excluir']) && $_GET['excluir'] === '1') {
    $foi_excluido = true;
} else {
    $foi_excluido = false;
}

$sql_foto = "SELECT foto FROM animais WHERE id = $id AND usuario_id = $usuario_id";
$resultado_foto = mysqli_query($conexao, $sql_foto);


if ($resultado_foto && mysqli_num_rows($resultado_foto) > 0) {
    $animal = mysqli_fetch_assoc($resultado_foto);

    if (!empty($animal['foto'])) {
        $caminho = 'uploads/' . $animal['foto'];
        if (file_exists($caminho)) {
            unlink($caminho);
        }
    }
    $sql_del = "DELETE FROM animais WHERE id = $id AND usuario_id = $usuario_id";
    mysqli_query($conexao, $sql_del);

    if ($foi_excluido == false) {
        $_SESSION['mensagem'] = 'Animal excluído do sistema!';
    }

    header('Location: perfil_animais.php');
    exit;

}

?>