<?php
// teste_email.php

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->isSMTP();
    $mail->Host       = 'smtp-relay.brevo.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'engs-renannvolff@camporeal.edu.br';
    $mail->Password   = '';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom('engs-renannvolff@camporeal.edu.br', 'Teste do Sistema');
    $mail->addAddress('renannfeliperfv@gmail.com');

    $mail->isHTML(true);
    $mail->Subject = 'Teste de Envio PHPMailer e Brevo';
    $mail->Body    = 'Se você recebeu este e-mail, a configuração está <b>correta!</b>';
    $mail->AltBody = 'Se você recebeu este e-mail, a configuração está correta!';

    $mail->send();
    echo 'E-mail de teste enviado com sucesso!';
} catch (Exception $e) {
    echo "A mensagem não pôde ser enviada. Mailer Error: {$mail->ErrorInfo}";
}