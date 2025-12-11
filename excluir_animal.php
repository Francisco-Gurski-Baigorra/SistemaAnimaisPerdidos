<?php
session_start();
include('conecta.php');

// garante que não haja saída antes de headers
// verifica usuário logado
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['mensagem'] = 'Você precisa estar logado para excluir.';
    header('Location: login.php');
    exit;
}

// parâmetros
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$usuario_id = (int) $_SESSION['usuario_id'];
$resgate = (isset($_GET['resgate']) && $_GET['resgate'] === '1') ? true : false;

// busca foto (para remover do disco)
$sql = "SELECT foto FROM animais WHERE id = ? AND usuario_id = ?";
$stmt = $conexao->prepare($sql);
if (!$stmt) {
    $_SESSION['mensagem'] = 'Erro interno (prepare).';
    header('Location: perfil_animais.php');
    exit;
}
$stmt->bind_param("ii", $id, $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado && $resultado->num_rows > 0) {
    $animal = $resultado->fetch_assoc();

    if (!empty($animal['foto'])) {
        $caminho = __DIR__ . '/uploads/' . $animal['foto'];
        if (file_exists($caminho)) {
            @unlink($caminho);
        }
    }

    // exclui registro
    $sqlDel = "DELETE FROM animais WHERE id = ? AND usuario_id = ?";
    $stmtDel = $conexao->prepare($sqlDel);
    if ($stmtDel) {
        $stmtDel->bind_param("ii", $id, $usuario_id);
        $stmtDel->execute();
    }

    // só seta mensagem se NÃO for resgate
    if (!$resgate) {
        $_SESSION['mensagem'] = 'Animal excluído com sucesso!';
    }

    header('Location: perfil_animais.php');
    exit;
} else {
    $_SESSION['mensagem'] = 'Animal não encontrado.';
    header('Location: perfil_animais.php');
    exit;
}
