<?php
require_once '../config.php';

$upload_dir = '../../uploads/contratos/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$response = ['status' => 'error', 'message' => 'Dados inválidos.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entidade_id = $_POST['entidade_id'] ?? null;
    $solucao_id = $_POST['solucao_id'] ?? null;
    $numero_contrato = $_POST['numero_contrato'] ?? null;
    $data_inicio = $_POST['data_inicio'] ?? null;
    $data_vencimento = $_POST['data_vencimento'] ?? null;
    $valor_mensal = $_POST['valor_mensal'] ?? null;
    
    $caminho_anexo_pdf = null;

    if (!$entidade_id || !$solucao_id || !$numero_contrato || !$data_inicio || !$data_vencimento || !$valor_mensal) {
        http_response_code(400);
        echo json_encode(['message' => 'Todos os campos são obrigatórios.']);
        exit;
    }

    if (isset($_FILES['anexo_pdf']) && $_FILES['anexo_pdf']['error'] == 0) {
        $file_tmp_path = $_FILES['anexo_pdf']['tmp_name'];
        $file_name = $_FILES['anexo_pdf']['name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

        if (strtolower($file_ext) === 'pdf') {
            $new_file_name = uniqid('contrato_', true) . '.' . $file_ext;
            $dest_path = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp_path, $dest_path)) {
                $caminho_anexo_pdf = 'uploads/contratos/' . $new_file_name;
            } else {
                $response['message'] = 'Erro ao mover o arquivo enviado.';
                http_response_code(500);
                echo json_encode($response);
                exit;
            }
        } else {
            $response['message'] = 'Formato de arquivo inválido. Apenas PDF é permitido.';
            http_response_code(400);
            echo json_encode($response);
            exit;
        }
    }

    try {
        $sql = "INSERT INTO contratos (entidade_id, solucao_id, numero_contrato, data_inicio, data_vencimento, valor_mensal, caminho_anexo_pdf) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $entidade_id, 
            $solucao_id, 
            $numero_contrato, 
            $data_inicio, 
            $data_vencimento, 
            $valor_mensal, 
            $caminho_anexo_pdf
        ]);

        $response = ['status' => 'success', 'message' => 'Contrato cadastrado com sucesso!'];
        http_response_code(201);
    } catch (PDOException $e) {
        $response['message'] = 'Erro ao cadastrar contrato: ' . $e->getMessage();
        http_response_code(500);
    }
} else {
    $response['message'] = 'Método de requisição inválido.';
    http_response_code(405);
}

echo json_encode($response);