<?php
include('conecta.php');

$sql = "SELECT a.id, a.usuario_id, a.situacao, a.especie, a.genero, a.foto,
               a.raca_id, r.racas AS nome_raca, a.porte, a.cor_predominante, a.idade,
               a.nome, a.descricao, a.latitude, a.longitude, a.data_ocorrido,
               a.telefone_contato, a.data_cadastro
        FROM animais a
        LEFT JOIN racas r ON a.raca_id = r.id
        WHERE a.latitude IS NOT NULL AND a.longitude IS NOT NULL";
$result = $conexao->query($sql);

$animais = [];
while ($row = $result->fetch_assoc()) {
    $animais[] = $row;
}

header('Content-Type: application/json');
echo json_encode($animais, JSON_UNESCAPED_UNICODE);
?>
