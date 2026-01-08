<?php
include('conecta.php');

header('Content-Type: application/json');

$sql = "SELECT animais.*, racas.racas as raca_nome 
        FROM animais 
        LEFT JOIN racas ON animais.raca_id = racas.id
        WHERE animais.situacao = 'perdido'";

$resultado = mysqli_query($conexao, $sql);

$animais = array();

if ($resultado) {
    while ($linha = mysqli_fetch_assoc($resultado)) {
        $animais[] = $linha;
    }
}

// retornap java
echo json_encode($animais);
?>