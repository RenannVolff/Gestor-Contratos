<?php
/**
 * Script para verificação e envio de alertas de vencimento de contrato.
 * Deve ser executado por uma tarefa agendada (Cron Job).
 */

require __DIR__ . '/vendor/autoload.php';

require __DIR__ . '/api/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

echo "==================================================\n";
echo "Iniciando verificação de alertas... (" . date('Y-m-d H:i:s') . ")\n";
echo "==================================================\n";

try {
    $stmt = $pdo->prepare("
        SELECT 
            a.id AS alerta_id, a.mensagem, a.email_destino,
            c.numero_contrato, c.data_vencimento,
            e.nome_fantasia
        FROM alertas_vencimento a
        JOIN contratos c ON a.contrato_id = c.id
        JOIN entidades e ON c.entidade_id = e.id
        WHERE a.enviado = FALSE AND a.data_alerta <= NOW()
    ");
    $stmt->execute();
    $alertas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($alertas) === 0) {
        echo "Nenhum alerta pendente para enviar no momento.\n";
        exit;
    }

    echo count($alertas) . " alerta(s) encontrado(s) para envio.\n";

    $mail = new PHPMailer(true);

    $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Descomente para ver o log de depuração completo
    $mail->isSMTP();
    $mail->Host       = 'smtp-relay.brevo.com';
    $mail->SMTPAuth   = true;

    $mail->Username   = 'engs-renannvolff@camporeal.edu.br';
    $mail->Password   = '';
    // --------------------------------------------------

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom('engs-renannvolff@camporeal.edu.br', 'Sistema Gestor de Contratos');
    
    foreach ($alertas as $alerta) {
        echo "--------------------------------------------------\n";
        echo "Processando alerta ID #{$alerta['alerta_id']} para {$alerta['email_destino']}...\n";
        
        $mail->clearAddresses();
        $mail->addAddress($alerta['email_destino']);

        $mail->isHTML(true);
        $mail->Subject = 'Alerta de Vencimento de Contrato: ' . $alerta['numero_contrato'];
        
        $mail->Body = "
            <h1 style='color: #c0392b;'>Alerta de Vencimento de Contrato</h1>
            <p>Olá,</p>
            <p>Este é um aviso automático sobre o vencimento de um contrato em seu sistema.</p>
            <p><strong>Mensagem do alerta:</strong> {$alerta['mensagem']}</p>
            <hr>
            <h3 style='color: #34495e;'>Detalhes do Contrato:</h3>
            <ul>
                <li><strong>Número do Contrato:</strong> {$alerta['numero_contrato']}</li>
                <li><strong>Entidade:</strong> {$alerta['nome_fantasia']}</li>
                <li><strong>Data de Vencimento:</strong> " . date('d/m/Y', strtotime($alerta['data_vencimento'])) . "</li>
            </ul>
            <br>
            <p><em>Por favor, tome as ações necessárias. Este e-mail foi enviado automaticamente pelo Sistema Gestor de Contratos.</em></p>
        ";
        
        $mail->AltBody = "Alerta de Vencimento de Contrato.\nMensagem: {$alerta['mensagem']}\nContrato: {$alerta['numero_contrato']}\nEntidade: {$alerta['nome_fantasia']}\nVencimento: " . date('d/m/Y', strtotime($alerta['data_vencimento']));

        $mail->send();
        echo "E-mail enviado com sucesso!\n";
        
        $updateStmt = $pdo->prepare("UPDATE alertas_vencimento SET enviado = TRUE WHERE id = ?");
        $updateStmt->execute([$alerta['alerta_id']]);
        echo "Status do alerta ID #{$alerta['alerta_id']} atualizado no banco de dados.\n";
    }

} catch (Exception $e) {
    echo "\n!!! ERRO !!!\n";
    echo "Erro ao processar alertas: {$e->getMessage()}\n";
    if (isset($mail) && !empty($mail->ErrorInfo)) {
        echo "Detalhes do PHPMailer: {$mail->ErrorInfo}\n";
    }
} catch (PDOException $e) {
    echo "\n!!! ERRO DE BANCO DE DADOS !!!\n";
    echo "Não foi possível conectar ou executar a consulta: {$e->getMessage()}\n";
}

echo "==================================================\n";
echo "Verificação de alertas concluída.\n";
echo "==================================================\n";