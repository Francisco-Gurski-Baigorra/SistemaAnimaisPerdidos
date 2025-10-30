<?php
$host = "localhost";
$user = "root";     // ajuste se necessário
$pass = "";         // coloque sua senha
$dbname = "animais_perdidos";

$conexao = new mysqli($host, $user, $pass, $dbname);

if ($conexao->connect_error) {
    die("Erro de conexão: " . $conexao->connect_error);
}
$conexao->set_charset("utf8mb4");
?>
