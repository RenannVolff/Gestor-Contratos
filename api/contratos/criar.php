<?php
// /api/contratos/criar.php
require_once '../config.php';

// Diretório de uploads
$upload_dir = '../../uploads/contratos/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$response = ['status' => 'error', 'message' => 'Dados inválidos ou método incorreto.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pega os dados do formulário (com as novas colunas de texto)
    $entidade_nome = trim($_POST['entidade_nome'] ?? '');
    $solucao_nome = trim($_POST['solucao_nome'] ?? '');
    $numero_contrato = trim($_POST['numero_contrato'] ?? '');
    $data_inicio = $_POST['data_inicio'] ?? null;
    $data_vencimento = $_POST['data_vencimento'] ?? null;
    $valor_mensal = $_POST['valor_mensal'] ?? null;
    
    $caminho_anexo_pdf = null;

    // Validação básica dos campos
    if (empty($entidade_nome) || empty($solucao_nome) || empty($numero_contrato) || empty($data_inicio) || empty($data_vencimento) || empty($valor_mensal)) {
        http_response_code(400);
        echo json_encode(['message' => 'Todos os campos de texto e data são obrigatórios.']);
        exit;
    }

    // Processa o upload do arquivo PDF, se houver
    if (isset($_FILES['anexo_pdf']) && $_FILES['anexo_pdf']['error'] == 0) {
        $file_tmp_path = $_FILES['anexo_pdf']['tmp_name'];
        $file_name = $_FILES['anexo_pdf']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if ($file_ext === 'pdf') {
            $new_file_name = uniqid('contrato_', true) . '.pdf';
            $dest_path = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp_path, $dest_path)) {
                $caminho_anexo_pdf = 'uploads/contratos/' . $new_file_name;
            } else {
                $response['message'] = 'Erro ao salvar o arquivo enviado.';
                http_response_code(500);
                echo json_encode($response);
                exit;
            }
        }
    }

    // Insere no banco de dados com as novas colunas
    try {
        $sql = "INSERT INTO contratos (numero_contrato, entidade_nome, solucao_nome, data_inicio, data_vencimento, valor_mensal, caminho_anexo_pdf) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $numero_contrato, 
            $entidade_nome, 
            $solucao_nome, 
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
    
    echo json_encode($response);
}