<?php
include('conecta.php');

// Informamos ao navegador que o conteúdo é um JSON
header('Content-Type: application/json');

// SQL ajustado para buscar APENAS animais com a situação 'perdido'
$sql = "SELECT animais.*, racas.racas as raca_nome 
        FROM animais 
        LEFT JOIN racas ON animais.raca_id = racas.id
        WHERE animais.situacao = 'perdido'";

$resultado = mysqli_query($conexao, $sql);

$animais = array();

if ($resultado) {
    while ($linha = mysqli_fetch_assoc($resultado)) {
        
        // Verificação simples para raça nula
        if ($linha['raca_nome'] == NULL) {
            $linha['raca_nome'] = "Vira-lata";
        }

        // Adiciona o animal na lista
        $animais[] = $linha;
    }
}

// Retorna os dados para o JavaScript
echo json_encode($animais);
?>