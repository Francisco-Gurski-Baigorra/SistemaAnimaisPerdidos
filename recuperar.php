<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'conecta.php';

if (!isset($_POST['email'])) {
    header("Location: recuperar_senha.php");
    exit;
}

$email = $_POST['email'];

// Verifica se o email existe
$verifica = $conexao->prepare("SELECT * FROM usuarios WHERE email = ?");
$verifica->bind_param("s", $email);
$verifica->execute();
$resultado = $verifica->get_result();
$usuario = $resultado->fetch_assoc();

if (!$usuario) {
    echo "<script>alert('❌ Este email não está cadastrado!'); window.history.back();</script>";
    exit;
}

// Gera token
$token = bin2hex(random_bytes(32));

// Salva token na tabela recuperar_senha
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
    $mail->Password = 'mdnlqoskzdtpsigf';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];

    $mail->setFrom('francisco.2023318347@aluno.iffar.edu.br', 'Sistema de Animais Perdidos');
    $mail->addAddress($email);

   $link = "http://localhost/SistemaAnimaisPerdidos/nova_senha.php?email="
        . urlencode($email) . "&token=" . urlencode($token);


    $mail->isHTML(true);
    $mail->Subject = 'Recuperação de Senha';
    $mail->Body = "
        <p>Olá!</p>
        <p>Você solicitou a recuperação de senha.</p>
        <p><a href='$link'>Clique aqui para redefinir sua senha</a></p>
    ";

    $mail->send();
    echo "<script>alert('📩 Um link de recuperação foi enviado para seu email!'); window.location='login.php';</script>";

} catch (Exception $e) {
    echo "Erro ao enviar email: {$mail->ErrorInfo}";
}
?>
