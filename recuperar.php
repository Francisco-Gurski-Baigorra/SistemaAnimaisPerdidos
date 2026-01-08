<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'conecta.php';

// Verifica se o email veio pelo POST
if (!isset($_POST['email'])) {
    header("Location: recuperar_senha.php");
    exit;
}

// Limpa a variável para evitar erros no SQL (Segurança básica)
$email = mysqli_real_escape_string($conexao, $_POST['email']);

// --- 1. VERIFICA SE O EMAIL EXISTE (Lógica Simples) ---
$sql_busca = "SELECT * FROM usuarios WHERE email = '$email'";
$resultado_busca = mysqli_query($conexao, $sql_busca);
$usuario = mysqli_fetch_assoc($resultado_busca);

if (!$usuario) {
    echo "<script>alert('❌ Este email não está cadastrado!'); window.history.back();</script>";
    exit;
}

// --- 2. GERA O TOKEN ---
$token = bin2hex(random_bytes(32));

// --- 3. SALVA O TOKEN NO BANCO (Lógica Simples) ---
$sql_token = "INSERT INTO recuperar_senha (email, token) VALUES ('$email', '$token')";
mysqli_query($conexao, $sql_token);

// --- 4. CONFIGURAÇÃO E ENVIO DO EMAIL (PHPMailer) ---
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
    $mail->SMTPSecure = 'tls'; // Simplificado
    $mail->Port = 587;

    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    $mail->setFrom('francisco.2023318347@aluno.iffar.edu.br', 'Sistema de Animais Perdidos');
    $mail->addAddress($email);

    // Cria o link
    $link = "http://localhost/SistemaAnimaisPerdidos/nova_senha.php?email=" . urlencode($email) . "&token=" . urlencode($token);

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
    echo "Erro ao enviar email: " . $mail->ErrorInfo;
}
?>