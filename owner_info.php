<?php
session_start();
include('conecta.php');

// Endpoint simples e seguro: recebe usuario_id via GET e retorna nome, telefone, email
// Somente retorna dados se o visitante estiver logado (proteção de privacidade).

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'login_required']);
    exit;
}

$usuario_id = isset($_GET['usuario_id']) ? (int)$_GET['usuario_id'] : 0;
if ($usuario_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'invalid_id']);
    exit;
}

$sql = "SELECT id, nome, telefone, email FROM usuarios WHERE id = ?";
$stmt = $conexao->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'prepare_failed']);
    exit;
}
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();

if (!$user) {
    http_response_code(404);
    echo json_encode(['error' => 'not_found']);
    exit;
}

// Retorna apenas campos públicos e de contato
$out = [
    'id' => (int)$user['id'],
    'nome' => $user['nome'] ?? null,
    'telefone' => $user['telefone'] ?? null,
    'email' => $user['email'] ?? null
];

echo json_encode($out, JSON_UNESCAPED_UNICODE);
exit;
