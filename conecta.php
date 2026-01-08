<?php
$servidor = "localhost";
$usuario = "root";   // ajuste conforme seu ambiente
$senha = "";       // ajuste se houver senha
$db   = "animais_perdidos";

// Conexão usando a função direta (procedural)
$conexao = mysqli_connect($servidor, $usuario, $senha, $db);

// Verifica se a conexão falhou de forma simples
if (!$conexao) {
    die("Erro na conexão: " . mysqli_connect_error());
}

// Define o charset para não dar erro nos acentos e emojis (🐾)
mysqli_set_charset($conexao, "utf8mb4");
?>