<?php
require_once '../config.php';

$response = ['status' => 'error', 'message' => 'ID do contrato não fornecido.'];

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM contratos WHERE id = ?");
        $stmt->execute([$id]);
        $contrato = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($contrato) {
            echo json_encode($contrato);
            exit;
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