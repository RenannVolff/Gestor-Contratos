<?php
require_once '../config.php';

$response = ['status' => 'error', 'message' => 'Dados inválidos.'];
$upload_dir = '../../uploads/contratos/';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $numero_contrato = $_POST['numero_contrato'];
    $entidade_id = $_POST['entidade_id'];
    $solucao_id = $_POST['solucao_id'];
    $data_inicio = $_POST['data_inicio'];
    $data_vencimento = $_POST['data_vencimento'];
    $valor_mensal = $_POST['valor_mensal'];

    try {
        if (isset($_FILES['anexo_pdf']) && $_FILES['anexo_pdf']['error'] == 0) {
            $stmt = $pdo->prepare("SELECT caminho_anexo_pdf FROM contratos WHERE id = ?");
            $stmt->execute([$id]);
            $contrato_antigo = $stmt->fetch();
            $caminho_antigo = $contrato_antigo ? '../../' . $contrato_antigo['caminho_anexo_pdf'] : null;

            $file_tmp_path = $_FILES['anexo_pdf']['tmp_name'];
            $file_name = uniqid('contrato_', true) . '.pdf';
            $dest_path = $upload_dir . $file_name;

            if (move_uploaded_file($file_tmp_path, $dest_path)) {
                $caminho_anexo_pdf = 'uploads/contratos/' . $file_name;

                $sql = "UPDATE contratos SET numero_contrato = ?, entidade_id = ?, solucao_id = ?, data_inicio = ?, data_vencimento = ?, valor_mensal = ?, caminho_anexo_pdf = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$numero_contrato, $entidade_id, $solucao_id, $data_inicio, $data_vencimento, $valor_mensal, $caminho_anexo_pdf, $id]);

                if ($caminho_antigo && file_exists($caminho_antigo)) {
                    unlink($caminho_antigo);
                }

            } else {
                throw new Exception('Erro ao mover o novo arquivo PDF.');
            }
        } else {
            $sql = "UPDATE contratos SET numero_contrato = ?, entidade_id = ?, solucao_id = ?, data_inicio = ?, data_vencimento = ?, valor_mensal = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$numero_contrato, $entidade_id, $solucao_id, $data_inicio, $data_vencimento, $valor_mensal, $id]);
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