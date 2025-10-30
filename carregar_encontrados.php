<?php
include('conecta.php');

// Consulta apenas animais ENCONTRADOS
$sql = "
SELECT 
    a.*, 
    r.racas AS raca_nome
FROM animais a
LEFT JOIN racas r ON a.raca_id = r.id
WHERE a.situacao = 'encontrado'
";
$res = $conexao->query($sql);

$animais = [];
while ($row = $res->fetch_assoc()) {
    $animais[] = $row;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($animais, JSON_UNESCAPED_UNICODE);
?>
