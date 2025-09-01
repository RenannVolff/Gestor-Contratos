<?php
// /api/contratos/atualizar.php
require_once '../config.php';

$response = ['status' => 'error', 'message' => 'Dados inválidos ou ID não fornecido.'];
$upload_dir = '../../uploads/contratos/';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $numero_contrato = trim($_POST['numero_contrato'] ?? '');
    $entidade_nome = trim($_POST['entidade_nome'] ?? ''); // Modificado
    $solucao_nome = trim($_POST['solucao_nome'] ?? '');   // Modificado
    $data_inicio = $_POST['data_inicio'] ?? null;
    $data_vencimento = $_POST['data_vencimento'] ?? null;
    $valor_mensal = $_POST['valor_mensal'] ?? null;

    try {
        // Verifica se um novo PDF foi enviado para substituição
        if (isset($_FILES['anexo_pdf']) && $_FILES['anexo_pdf']['error'] == 0) {
            // 1. Busca o caminho do PDF antigo para excluí-lo
            $stmt = $pdo->prepare("SELECT caminho_anexo_pdf FROM contratos WHERE id = ?");
            $stmt->execute([$id]);
            $contrato_antigo = $stmt->fetch();
            $caminho_antigo_completo = $contrato_antigo && !empty($contrato_antigo['caminho_anexo_pdf']) ? '../../' . $contrato_antigo['caminho_anexo_pdf'] : null;

            // 2. Salva o novo arquivo
            $file_tmp_path = $_FILES['anexo_pdf']['tmp_name'];
            $file_name = uniqid('contrato_', true) . '.pdf';
            $dest_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($file_tmp_path, $dest_path)) {
                $caminho_novo_pdf = 'uploads/contratos/' . $file_name;

                // 3. Atualiza o registro no banco com o novo caminho do PDF
                $sql = "UPDATE contratos SET numero_contrato = ?, entidade_nome = ?, solucao_nome = ?, data_inicio = ?, data_vencimento = ?, valor_mensal = ?, caminho_anexo_pdf = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$numero_contrato, $entidade_nome, $solucao_nome, $data_inicio, $data_vencimento, $valor_mensal, $caminho_novo_pdf, $id]);

                // 4. Apaga o arquivo antigo, se ele existia
                if ($caminho_antigo_completo && file_exists($caminho_antigo_completo)) {
                    unlink($caminho_antigo_completo);
                }
            } else {
                throw new Exception('Erro ao mover o novo arquivo PDF.');
            }
        } else {
            // Se nenhum novo PDF foi enviado, atualiza apenas os outros dados
            $sql = "UPDATE contratos SET numero_contrato = ?, entidade_nome = ?, solucao_nome = ?, data_inicio = ?, data_vencimento = ?, valor_mensal = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$numero_contrato, $entidade_nome, $solucao_nome, $data_inicio, $data_vencimento, $valor_mensal, $id]);
        }

        $response = ['status' => 'success', 'message' => 'Contrato atualizado com sucesso!'];
        http_response_code(200);

    } catch (Exception $e) {
        $response['message'] = 'Erro ao atualizar: ' . $e->getMessage();
        http_response_code(500);
    }
} else {
    http_response_code(400);
}

echo json_encode($response);