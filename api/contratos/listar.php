<?php
// /api/contratos/listar.php
require_once '../config.php';

try {
    // A consulta agora é muito mais simples, sem JOINs.
    $stmt = $pdo->query("
        SELECT 
            id, 
            numero_contrato, 
            entidade_nome,       -- Nova coluna
            solucao_nome,        -- Nova coluna
            data_inicio, 
            data_vencimento, 
            valor_mensal, 
            caminho_anexo_pdf
        FROM contratos
        ORDER BY data_vencimento ASC
    ");

    $contratos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // O JavaScript já espera 'entidade_nome' e 'solucao_nome', então não precisa mudar nada lá.
    // Apenas renomeamos as colunas no SELECT para manter a compatibilidade.
    // Na verdade, como a coluna agora tem o nome correto, podemos remover os apelidos `AS`.
    echo json_encode($contratos);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Erro ao buscar contratos: ' . $e->getMessage()]);
}