<?php
session_start();
include('conecta.php');

// Informamos que a resposta será JSON
header('Content-Type: application/json; charset=utf-8');

// 1. Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'login_required']);
    exit;
}

// 2. Pega o ID enviado pelo Mapa
$usuario_id = isset($_GET['usuario_id']) ? (int)$_GET['usuario_id'] : 0;

if ($usuario_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'invalid_id']);
    exit;
}

// 3. Consulta Simples (Sem STMT)
// Usamos o escape para segurança básica, embora o (int) acima já proteja
$sql = "SELECT id, nome, telefone, email FROM usuarios WHERE id = '$usuario_id'";
$res = $conexao->query($sql);
$user = $res->fetch_assoc();

// 4. Se não encontrar o usuário
if (!$user) {
    http_response_code(404);
    echo json_encode(['error' => 'not_found']);
    exit;
}

// 5. Retorna os dados formatados
$out = [
    'id' => (int)$user['id'],
    'nome' => $user['nome'],
    'telefone' => $user['telefone'],
    'email' => $user['email']
];

echo json_encode($out, JSON_UNESCAPED_UNICODE);
exit;