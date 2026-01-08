<?php
session_start();
include('conecta.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['mensagem'] = 'Você precisa estar logado para excluir.';
    header('Location: login.php');
    exit;
}

// Pega os parâmetros via GET de forma básica
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
} else {
    $id = 0;
}

$usuario_id = (int)$_SESSION['usuario_id'];

if (isset($_GET['resgate']) && $_GET['resgate'] === '1') {
    $resgate = true;
} else {
    $resgate = false;
}

// BUSCA A FOTO (para remover o arquivo da pasta uploads)
// Usando SQL direto e mysqli_query
$sql_foto = "SELECT foto FROM animais WHERE id = $id AND usuario_id = $usuario_id";
$resultado_foto = mysqli_query($conexao, $sql_foto);

if ($resultado_foto && mysqli_num_rows($resultado_foto) > 0) {
    $animal = mysqli_fetch_assoc($resultado_foto);

    // Se houver foto cadastrada, apaga o arquivo físico
    if (!empty($animal['foto'])) {
        $caminho = 'uploads/' . $animal['foto'];
        if (file_exists($caminho)) {
            unlink($caminho);
        }
    }

    // EXCLUI O REGISTRO DO BANCO
    $sql_del = "DELETE FROM animais WHERE id = $id AND usuario_id = $usuario_id";
    mysqli_query($conexao, $sql_del);

    // Só define a mensagem de sucesso se não for um processo de "resgate"
    if (!$resgate) {
        $_SESSION['mensagem'] = 'Animal excluído com sucesso!';
    }

    header('Location: perfil_animais.php');
    exit;
} else {
    // Se o animal não pertencer ao usuário ou não existir
    $_SESSION['mensagem'] = 'Animal não encontrado.';
    header('Location: perfil_animais.php');
    exit;
}

// Fecha a conexão (estilo procedural)
mysqli_close($conexao);
?>