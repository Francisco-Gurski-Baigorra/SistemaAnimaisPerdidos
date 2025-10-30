<?php
$host = "localhost";
$user = "root";   // ajuste conforme seu ambiente
$pass = "";       // ajuste se houver senha
$db   = "animais_perdidos";

$conexao = new mysqli($host, $user, $pass, $db);
if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}
$conexao->set_charset("utf8mb4");
?>
