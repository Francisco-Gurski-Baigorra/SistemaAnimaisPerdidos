<?php
include('conecta.php');

$sql = "SELECT a.id, a.usuario_id, a.situacao, a.especie, a.genero, a.foto,
               a.raca_id, r.racas AS nome_raca, a.porte, a.cor_predominante, a.idade,
               a.nome, a.descricao, a.latitude, a.longitude, a.data_ocorrido,
               a.telefone_contato, a.data_cadastro
        FROM animais a
        LEFT JOIN racas r ON a.raca_id = r.id
        WHERE a.latitude IS NOT NULL AND a.longitude IS NOT NULL";


$result = mysqli_query($conexao, $sql);

$animais = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $animais[] = $row;
    }
}

// retorno eh json
header('Content-Type: application/json');

// exibe o json com ascento
echo json_encode($animais, JSON_UNESCAPED_UNICODE);
?>