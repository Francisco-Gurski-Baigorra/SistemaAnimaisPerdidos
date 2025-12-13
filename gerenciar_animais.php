<?php
include('conecta.php');
session_start();

/* =========================
   🔐 Apenas administradores
========================= */
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    echo "<script>alert('❌ Você não tem permissão para acessar esta área!'); window.location='index.php';</script>";
    exit;
}

/* =========================
   🔍 Busca todos os animais
========================= */
$sql = "SELECT 
            a.id,
            a.usuario_id,
            a.situacao,
            a.especie,
            a.genero,
            a.foto,
            r.racas AS raca_nome,
            a.porte,
            a.cor_predominante,
            a.idade,
            a.nome,
            a.telefone_contato
        FROM animais a
        LEFT JOIN racas r ON a.raca_id = r.id
        ORDER BY a.situacao, a.id DESC";

$result = $conexao->query($sql);

/* =========================
   📦 Agrupa por situação
========================= */
$animaisPorSituacao = [
    'perdido'    => [],
    'encontrado' => [],
    'resgatado'  => []
];

while ($row = $result->fetch_assoc()) {
    $animaisPorSituacao[$row['situacao']][] = $row;
}

/* =========================
   📞 Formata telefone
========================= */
function formatarTelefone($telefone) {
    $telefone = preg_replace('/\D/', '', $telefone);

    if (strlen($telefone) === 11) {
        return sprintf(
            '(%s) %s-%s',
            substr($telefone, 0, 2),
            substr($telefone, 2, 5),
            substr($telefone, 7)
        );
    }

    return 'N/A';
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Gerenciar Animais</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


<style>
body {
    background-color: #f2f2f2;
}
.card {
    border-radius: 15px;
}
.table img {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 8px;
}
.table thead {
    background: #179e46ff;
    color: white;
}
.btn-editar {
    background-color: #ffc107;
    color: black;
}
.btn-excluir {
    background-color: #dc3545;
    color: white;
}
.btn-voltar {
    background-color: #179e46ff;
    color: white;
}
.titulo-situacao {
    background: #e9f5ee;
    border-left: 6px solid #179e46ff;
    padding: 10px 15px;
    font-weight: bold;
    margin-top: 30px;
}
</style>
</head>

<body>

<div class="container mt-4">
    <div class="card p-4 shadow">
        <h2 class="text-center mb-4">🐾 Gerenciar Animais</h2>

        <div class="text-end mb-3">
            <a href="admin.php" class="btn btn-voltar">⬅ Voltar</a>
        </div>

        <?php
        $labels = [
            'perdido'    => 'Animais Perdidos',
            'encontrado' => 'Animais Encontrados',
            'resgatado'  => 'Animais Resgatados'
        ];

        foreach ($animaisPorSituacao as $situacao => $lista):
            if (empty($lista)) continue;
        ?>

        <div class="titulo-situacao">
            <?= $labels[$situacao] ?>
        </div>

        <table class="table table-bordered table-striped text-center mt-2">
            <thead>
                <tr>
                    <th>ID Animal</th>
                    <th>ID Usuário</th>
                    <th>Foto</th>
                    <th>Nome</th>
                    <th>Situação</th>
                    <th>Espécie</th>
                    <th>Gênero</th>
                    <th>Raça</th>
                    <th>Porte</th>
                    <th>Cor</th>
                    <th>Idade</th>
                    <th>Telefone</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($lista as $animal): ?>
                <tr>
                    <td><?= $animal['id'] ?></td>
                    <td><?= $animal['usuario_id'] ?></td>

                    <td>
                        <?php if (!empty($animal['foto'])): ?>
                            <img src="uploads/<?= htmlspecialchars($animal['foto']) ?>">
                        <?php else: ?>
                            <span class="text-muted">N/A</span>
                        <?php endif; ?>
                    </td>

                    <td><?= $animal['nome'] ?: 'N/A' ?></td>
                    <td><?= ucfirst($animal['situacao']) ?></td>
                    <td><?= ucfirst($animal['especie']) ?></td>
                    <td><?= ucfirst($animal['genero']) ?></td>
                    <td><?= $animal['raca_nome'] ?: 'N/A' ?></td>
                    <td><?= ucfirst($animal['porte']) ?></td>
                    <td><?= ucfirst($animal['cor_predominante']) ?></td>
                    <td><?= $animal['idade'] ?: 'N/A' ?></td>
                    <td><?= formatarTelefone($animal['telefone_contato']) ?></td>

                    <td>
                        <a href="adm_editar_animal.php?id=<?= $animal['id'] ?>"
                           class="btn btn-editar btn-sm">
                            <i class="bi bi-pencil-square"></i> Editar
                        </a>
                    
                        <a href="adm_excluir_animal.php?id=<?= $animal['id'] ?>"
                           class="btn btn-excluir btn-sm"
                           onclick="return confirm('Tem certeza que deseja excluir este animal?')">
                           <i class="bi bi-trash"></i> Excluir
                        </a>
  
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php endforeach; ?>

    </div>
</div>

</body>
</html>
