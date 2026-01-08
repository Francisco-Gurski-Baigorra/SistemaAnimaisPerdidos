<?php
include('conecta.php');

// json
header('Content-Type: application/json');
//apenas animal encontrados
$sql = "SELECT animais.*, racas.racas as raca_nome 
        FROM animais 
        LEFT JOIN racas ON animais.raca_id = racas.id
        WHERE animais.situacao = 'encontrado'";

$resultado = mysqli_query($conexao, $sql);

$animais = array();
if ($resultado) {
    while ($linha = mysqli_fetch_assoc($resultado)) {
        
        $animais[] = $linha;
    }
}

//devovler os dados para o java no buscar animais
echo json_encode($animais);
?>