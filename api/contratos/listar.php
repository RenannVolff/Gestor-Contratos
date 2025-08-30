<?php
require_once '../config.php';

try {
    $stmt = $pdo->query("
        SELECT 
            c.id, c.numero_contrato, c.data_inicio, c.data_vencimento, c.valor_mensal, c.caminho_anexo_pdf,
            e.nome_fantasia AS entidade_nome,
            s.nome_solucao AS solucao_nome
        FROM contratos c
        JOIN entidades e ON c.entidade_id = e.id
        JOIN solucoes s ON c.solucao_id = s.id
        ORDER BY c.data_vencimento ASC
    ");

    $contratos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($contratos);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Erro ao buscar contratos: ' . $e->getMessage()]);
}