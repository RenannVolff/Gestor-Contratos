<?php
require_once '../config.php';

$response = ['status' => 'error', 'message' => 'ID do contrato não fornecido.'];

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        $stmt = $pdo->prepare("SELECT caminho_anexo_pdf FROM contratos WHERE id = ?");
        $stmt->execute([$id]);
        $contrato = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($contrato) {
            $deleteStmt = $pdo->prepare("DELETE FROM contratos WHERE id = ?");
            $deleteStmt->execute([$id]);

            if ($deleteStmt->rowCount() > 0 && !empty($contrato['caminho_anexo_pdf'])) {
                $caminho_fisico = '../../' . $contrato['caminho_anexo_pdf'];
                if (file_exists($caminho_fisico)) {
                    unlink($caminho_fisico);
                }
            }
            
            $response = ['status' => 'success', 'message' => 'Contrato excluído com sucesso!'];
            http_response_code(200);

        } else {
            $response['message'] = 'Contrato não encontrado.';
            http_response_code(404);
        }

    } catch (PDOException $e) {
        $response['message'] = 'Erro no banco de dados: ' . $e->getMessage();
        http_response_code(500);
    }
} else {
    http_response_code(400);
}

echo json_encode($response);