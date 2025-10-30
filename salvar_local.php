<?php
session_start();
include('conecta.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    echo "Erro: usuário não autenticado.";
    exit;
}

$usuario_id        = $_SESSION['usuario_id'];
$situacao           = $_POST['situacao'] ?? null;
$especie            = $_POST['especie'] ?? null;
$genero             = $_POST['genero'] ?? null;
$raca_id            = !empty($_POST['raca_id']) ? (int)$_POST['raca_id'] : null;
$porte              = $_POST['porte'] ?? null;
$cor_predominante   = $_POST['cor_predominante'] ?? null;
$idade              = $_POST['idade'] ?? null;
$nome               = $_POST['nome'] ?? null;
$descricao          = $_POST['descricao'] ?? null;
$latitude           = !empty($_POST['lat']) ? (float)$_POST['lat'] : null;
$longitude          = !empty($_POST['lng']) ? (float)$_POST['lng'] : null;
$data_ocorrido      = !empty($_POST['data_ocorrido']) ? $_POST['data_ocorrido'] : null;
$telefone_contato   = $_POST['telefone_contato'] ?? null;

// === Upload da foto (se enviada) ===
$foto_nome = null;
if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $permitidos = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $permitidos)) {
        echo "Formato de imagem não permitido.";
        exit;
    }
    $foto_nome = uniqid() . "." . $ext;
    $destino = __DIR__ . "/uploads/" . $foto_nome;
    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
        echo "Erro ao salvar imagem.";
        exit;
    }
}

// === Validação básica ===
if (!$situacao || !$especie || !$genero || !$porte || !$idade || !$telefone_contato || !$latitude || !$longitude) {
    echo "Erro: Preencha todos os campos obrigatórios.";
    exit;
}

// === Inserção segura com prepared statement ===
$sql = "INSERT INTO animais 
        (usuario_id, situacao, especie, genero, foto, raca_id, porte, cor_predominante, 
         idade, nome, descricao, latitude, longitude, data_ocorrido, telefone_contato)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conexao->prepare($sql);

if (!$stmt) {
    die("Erro no prepare: " . $conexao->error);
}

// Tipos corretos:
// i = int | s = string | d = double (decimal)
$stmt->bind_param(
    "issssiisssssdds",
    $usuario_id,
    $situacao,
    $especie,
    $genero,
    $foto_nome,
    $raca_id,
    $porte,
    $cor_predominante,
    $idade,
    $nome,
    $descricao,
    $latitude,
    $longitude,
    $data_ocorrido,
    $telefone_contato
);

if ($stmt->execute()) {
    echo "✅ Animal registrado com sucesso!";
} else {
    echo "❌ Erro ao salvar: " . $stmt->error;
}

$stmt->close();
$conexao->close();
?>