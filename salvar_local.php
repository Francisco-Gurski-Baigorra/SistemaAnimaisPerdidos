<?php
session_start();
require 'conecta.php';

if (!isset($_SESSION['usuario_id'])) {
    die("Erro: Você precisa estar logado.");
}

$usuario_id = $_SESSION['usuario_id'];

// Campos obrigatórios
$nome             = trim($_POST['nome'] ?? '');
$situacao         = $_POST['situacao'] ?? '';
$especie          = $_POST['especie'] ?? '';
$genero           = $_POST['genero'] ?? '';
$raca_id          = (int)($_POST['raca_id'] ?? 0);
$data_ocorrido    = $_POST['data_ocorrido'] ?? '';
$telefone_contato = preg_replace('/\D/', '', $_POST['telefone_contato'] ?? '');
$latitude         = $_POST['latitude'] ?? null;
$longitude        = $_POST['longitude'] ?? null;

// Campos opcionais
$porte            = $_POST['porte'] ?? null;
$cor_predominante = $_POST['cor_predominante'] ?? null;
$idade            = strtolower($_POST['idade'] ?? '');
$descricao        = trim($_POST['descricao'] ?? '');

// Validação dos campos obrigatórios
if ($nome === '' || $situacao === '' || $especie === '' || $genero === '' || 
    $raca_id === 0 || $data_ocorrido === '' || strlen($telefone_contato) !== 11) {
    die("Erro: Preencha todos os campos obrigatórios corretamente.");
}

// Validação da foto
if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK || $_FILES['foto']['size'] === 0) {
    die("Erro: Selecione uma foto válida do animal.");
}

// Upload da foto
$ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
$permitidos = ['jpg','jpeg','png','gif','webp'];
if (!in_array($ext, $permitidos)) {
    die("Erro: Formato de imagem não permitido (use JPG, PNG, GIF ou WEBP).");
}

$foto_nome = "animal_" . time() . "_" . rand(1000,9999) . "." . $ext;
$caminho = "uploads/" . $foto_nome;

if (!move_uploaded_file($_FILES['foto']['tmp_name'], $caminho)) {
    die("Erro ao salvar a foto no servidor.");
}
// ================= VALIDAÇÃO ESPÉCIE x RAÇA =================

$mapaRacasPorEspecie = [
    'cachorro' => [
        'vira-lata','labrador','bulldog','pastor alemão','pincher',
        'cimarron','husky','salsicha','golden'
    ],
    'gato' => [
        'persa','siamês','sphynx'
    ]
];

// Busca o nome da raça pelo ID
$stmtRaca = $conexao->prepare("SELECT racas FROM racas WHERE id = ?");
$stmtRaca->bind_param("i", $raca_id);
$stmtRaca->execute();
$resRaca = $stmtRaca->get_result();

if ($resRaca->num_rows === 0) {
    die("Erro: Raça inválida.");
}

$nomeRaca = strtolower($resRaca->fetch_assoc()['racas']);
$stmtRaca->close();

// Regra: espécie "outros" só aceita raça "outros"
if ($especie === 'outros' && $nomeRaca !== 'outros') {
    die('Erro: Para a espécie "outros", a raça deve ser "outros".');
}

// Regra: raça "outros" é permitida para qualquer espécie
if ($nomeRaca !== 'outros') {

    if (isset($mapaRacasPorEspecie[$especie])) {
        if (!in_array($nomeRaca, $mapaRacasPorEspecie[$especie])) {
            die('Erro: A raça selecionada não corresponde à espécie informada.');
        }
    }
}


// === INSERÇÃO NO BANCO (15 campos → 15 tipos) ===
$sql = "INSERT INTO animais (
    usuario_id, nome, situacao, especie, genero, raca_id, porte,
    cor_predominante, idade, descricao, telefone_contato,
    data_ocorrido, latitude, longitude, foto
) VALUES (
    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
)";

$stmt = $conexao->prepare($sql);

// CORREÇÃO: agora são 15 tipos corretos
$stmt->bind_param(
    "issssississsdss",  // ← 15 tipos certinhos!
    $usuario_id,       // int
    $nome,             // string
    $situacao,         // string
    $especie,          // string
    $genero,           // string
    $raca_id,          // int
    $porte,            // string (ou null)
    $cor_predominante, // string (ou null)
    $idade,            // string
    $descricao,        // string
    $telefone_contato, // string (11 dígitos)
    $data_ocorrido,    // string (date)
    $latitude,         // double (ou null)
    $longitude,        // double (ou null)
    $foto_nome         // string
);

if ($stmt->execute()) {
    echo "Animal cadastrado com sucesso!";
} else {
    echo "Erro no banco de dados: " . $stmt->error;
}
$stmt->close();
?>