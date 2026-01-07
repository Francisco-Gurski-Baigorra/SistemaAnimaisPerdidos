<?php
session_start();
require 'conecta.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    die("Erro: Você precisa estar logado para realizar esta ação.");
}

$usuario_id = $_SESSION['usuario_id'];

// --- COLETA DOS DADOS (Sem usar ??) ---
if (isset($_POST['nome'])) { $nome = $_POST['nome']; } else { $nome = ''; }
if (isset($_POST['situacao'])) { $situacao = $_POST['situacao']; } else { $situacao = ''; }
if (isset($_POST['especie'])) { $especie = $_POST['especie']; } else { $especie = ''; }
if (isset($_POST['genero'])) { $genero = $_POST['genero']; } else { $genero = ''; }
if (isset($_POST['raca_id'])) { $raca_id = (int)$_POST['raca_id']; } else { $raca_id = 0; }
if (isset($_POST['data_ocorrido'])) { $data_ocorrido = $_POST['data_ocorrido']; } else { $data_ocorrido = ''; }
if (isset($_POST['telefone_contato'])) { $telefone_contato = preg_replace('/\D/', '', $_POST['telefone_contato']); } else { $telefone_contato = ''; }
if (isset($_POST['latitude'])) { $latitude = $_POST['latitude']; } else { $latitude = ''; }
if (isset($_POST['longitude'])) { $longitude = $_POST['longitude']; } else { $longitude = ''; }

// Campos Opcionais (Mantendo as iniciais maiúsculas vindas do formulário)
if (isset($_POST['porte'])) { $porte = $_POST['porte']; } else { $porte = ''; }
if (isset($_POST['cor_predominante'])) { $cor_predominante = $_POST['cor_predominante']; } else { $cor_predominante = ''; }
if (isset($_POST['idade'])) { $idade = $_POST['idade']; } else { $idade = ''; }
if (isset($_POST['descricao'])) { $descricao = $_POST['descricao']; } else { $descricao = ''; }

// --- VALIDAÇÃO DE SEGURANÇA: ESPÉCIE VS RAÇA ---

// Primeiro, buscamos qual é a raça que o usuário selecionou no banco
$sql_busca_raca = "SELECT racas FROM racas WHERE id = '$raca_id'";
$resultado_raca = mysqli_query($conexao, $sql_busca_raca);
$dados_raca = mysqli_fetch_assoc($resultado_raca);

if (!$dados_raca) {
    die("Erro: Raça não encontrada.");
}

$nome_da_raca = strtolower($dados_raca['racas']);

// Listas de controle (Devem bater com os nomes na sua tabela 'racas')
$racas_de_cachorro = ['vira-lata', 'labrador', 'bulldog', 'pastor alemão', 'pincher', 'cimarron', 'husky', 'salsicha', 'golden'];
$racas_de_gato = ['persa', 'siamês', 'sphynx'];

if ($especie == 'cachorro') {
    if (!in_array($nome_da_raca, $racas_de_cachorro) && $nome_da_raca != 'outros') {
        die("Erro: Você não pode cadastrar um cachorro com uma raça de gato ou outra espécie.");
    }
} 
else if ($especie == 'gato') {
    if (!in_array($nome_da_raca, $racas_de_gato) && $nome_da_raca != 'outros') {
        die("Erro: Você não pode cadastrar um gato com uma raça de cachorro ou outra espécie.");
    }
} 
else if ($especie == 'outros') {
    if ($nome_da_raca != 'outros') {
        die("Erro: Para a espécie 'Outros', selecione a raça 'Outros'.");
    }
}

// --- UPLOAD DA FOTO ---
$foto_nome = "";
if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $foto_nome = "animal_" . time() . "_" . rand(1000, 9999) . "." . $extensao;
    move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/" . $foto_nome);
} else {
    die("Erro: A foto do animal é obrigatória.");
}

// --- INSERT NO BANCO (Lógica simples de string) ---
$sql = "INSERT INTO animais (
            usuario_id, nome, situacao, especie, genero, raca_id, porte,
            cor_predominante, idade, descricao, telefone_contato,
            data_ocorrido, latitude, longitude, foto
        ) VALUES (
            '$usuario_id', '$nome', '$situacao', '$especie', '$genero', '$raca_id', '$porte',
            '$cor_predominante', '$idade', '$descricao', '$telefone_contato',
            '$data_ocorrido', '$latitude', '$longitude', '$foto_nome'
        )";

if (mysqli_query($conexao, $sql)) {
    echo "Sucesso: Animal cadastrado com sucesso!";
} else {
    echo "Erro ao salvar no banco: " . mysqli_error($conexao);
}
?>