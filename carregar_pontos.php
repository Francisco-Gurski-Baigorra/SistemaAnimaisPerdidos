<?php
include('conecta.php');

// O SQL continua o mesmo
$sql = "SELECT a.id, a.usuario_id, a.situacao, a.especie, a.genero, a.foto,
               a.raca_id, r.racas AS nome_raca, a.porte, a.cor_predominante, a.idade,
               a.nome, a.descricao, a.latitude, a.longitude, a.data_ocorrido,
               a.telefone_contato, a.data_cadastro
        FROM animais a
        LEFT JOIN racas r ON a.raca_id = r.id
        WHERE a.latitude IS NOT NULL AND a.longitude IS NOT NULL";

// Alterado: Uso do mysqli_query (procedural) em vez de ->query
$result = mysqli_query($conexao, $sql);

$animais = [];

// Alterado: Uso do mysqli_fetch_assoc em vez de ->fetch_assoc()
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $animais[] = $row;
    }
}

// Informa ao navegador que o retorno é um JSON
header('Content-Type: application/json');

// Exibe o JSON (mantendo acentos com JSON_UNESCAPED_UNICODE)
echo json_encode($animais, JSON_UNESCAPED_UNICODE);

// Fecha a conexão de forma básica
mysqli_close($conexao);
?>