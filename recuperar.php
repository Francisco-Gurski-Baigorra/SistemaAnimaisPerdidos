<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'conecta.php';
$conexao = $conexao; // Usa sua conexão já existente

$email = $_POST['email'];

// Verifica se o e-mail está cadastrado
$verifica = $conexao->prepare("SELECT * FROM usuarios WHERE email = ?");
$verifica->bind_param("s", $email);
$verifica->execute();
$resultado = $verifica->get_result();
$usuario = $resultado->fetch_assoc();

if (!$usuario) {
    die("Email não encontrado!");
}

// Cria token aleatório
$token = bin2hex(random_bytes(32));

// Insere na tabela recuperar_senha
$sql = "INSERT INTO recuperar_senha (email, token) VALUES (?, ?)";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("ss", $email, $token);
$stmt->execute();

// PHPMailer
require_once 'PHPMailer-master/src/PHPMailer.php';
require_once 'PHPMailer-master/src/SMTP.php';
require_once 'PHPMailer-master/src/Exception.php';

$mail = new PHPMailer(true);

try {
   $mail->CharSet = 'UTF-8';
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'francisco.2023318347@aluno.iffar.edu.br';
$mail->Password = 'mdnlqoskzdtpsigf'; // senha de app gerada no Gmail
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // ou 'tls'
$mail->Port = 587;

// 🔹 Importante: desabilita verificação de certificado (evita erro SSL no WAMP)
$mail->SMTPOptions = [
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    ]
];

$mail->setFrom('francisco.2023318347@aluno.iffar.edu.br', 'Sistema de Animais Perdidos');
$mail->addAddress($email);

// 🔹 Gera o link de redefinição de senha corretamente
$link = "http://localhost/sistemaanimaisperdidos/nova_senha.php?email=" 
       . urlencode($email) . "&token=" . urlencode($token);

// 🔹 Corpo do e-mail
$mail->isHTML(true);
$mail->Subject = 'Recuperação de Senha';
$mail->Body = "
    <p>Olá!</p>
    <p>Você solicitou a recuperação da sua conta no nosso sistema.</p>
    <p>Para redefinir sua senha, clique no link abaixo:</p>
    <p><a href='$link'>Clique aqui para redefinir sua senha</a></p>
    <br>
    <p>Atenciosamente,<br>Equipe do Sistema de Animais Perdidos.</p>
";


    $mail->send();
    echo "<script>alert('Um link de recuperação foi enviado para o seu e-mail.'); window.location='login.php';</script>";

} catch (Exception $e) {
    echo "Erro ao enviar email: {$mail->ErrorInfo}";
}
?>
