<?php
$servidor = "localhost";
$usuario = "root"; 
$senha = ""; 
$db   = "animais_perdidos";

$conexao = mysqli_connect($servidor, $usuario, $senha, $db);

if (!$conexao) {
    die("Erro na conexão: " . mysqli_connect_error());
}

?>