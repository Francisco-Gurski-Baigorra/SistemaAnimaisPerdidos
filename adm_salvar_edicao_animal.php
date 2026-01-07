<?php
session_start();
include 'conecta.php';

$id = $_POST['id'];

// Pegando os dados com if normal (sem ??)
if (isset($_POST['nome'])) { $nome = $_POST['nome']; } else { $nome = ''; }
if (isset($_POST['situacao'])) { $situacao = $_POST['situacao']; } else { $situacao = ''; }
if (isset($_POST['especie'])) { $especie = $_POST['especie']; } else { $especie = ''; }
if (isset($_POST['genero'])) { $genero = $_POST['genero']; } else { $genero = ''; }
if (isset($_POST['raca_id'])) { $raca_id = $_POST['raca_id']; } else { $raca_id = ''; }
if (isset($_POST['porte'])) { $porte = $_POST['porte']; } else { $porte = ''; }
if (isset($_POST['cor_predominante'])) { $cor_predominante = $_POST['cor_predominante']; } else { $cor_predominante = ''; }
if (isset($_POST['idade'])) { $idade = $_POST['idade']; } else { $idade = ''; }
if (isset($_POST['telefone_contato'])) { $telefone_contato = $_POST['telefone_contato']; } else { $telefone_contato = ''; }
if (isset($_POST['latitude'])) { $latitude = $_POST['latitude']; } else { $latitude = 'NULL'; }
if (isset($_POST['longitude'])) { $longitude = $_POST['longitude']; } else { $longitude = 'NULL'; }
if (isset($_POST['descricao'])) { $descricao = $_POST['descricao']; } else { $descricao = ''; }

// Upload da foto
$novaFoto = "";
if ($_FILES['foto']['name'] != "") {
    $extensoes = array("jpg", "jpeg", "png", "gif", "webp");
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    
    if (in_array($ext, $extensoes)) {
        $novoNome = "animal_" . time() . "_" . rand(1000, 9999) . "." . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/" . $novoNome);
        $novaFoto = $novoNome;
    }
}

// Montagem do SQL "Logicão" (Direto na string)
if ($novaFoto != "") {
    $sql = "UPDATE animais SET 
            nome = '$nome', 
            situacao = '$situacao', 
            especie = '$especie', 
            genero = '$genero', 
            raca_id = '$raca_id', 
            porte = '$porte', 
            cor_predominante = '$cor_predominante', 
            idade = '$idade', 
            telefone_contato = '$telefone_contato', 
            latitude = '$latitude', 
            longitude = '$longitude', 
            foto = '$novaFoto' 
            WHERE id = $id";
} else {
    $sql = "UPDATE animais SET 
            nome = '$nome', 
            situacao = '$situacao', 
            especie = '$especie', 
            genero = '$genero', 
            raca_id = '$raca_id', 
            porte = '$porte', 
            cor_predominante = '$cor_predominante', 
            idade = '$idade', 
            telefone_contato = '$telefone_contato', 
            latitude = '$latitude', 
            longitude = '$longitude' 
            WHERE id = $id";
}

// Executa a query simples
mysqli_query($conexao, $sql);

header("Location: gerenciar_animais.php?edit=success");
exit;
?>