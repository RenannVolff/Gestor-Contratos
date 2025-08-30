<?php
require_once 'config.php';

if ($pdo) {
    echo json_encode(['status' => 'success', 'message' => 'Conexão com o banco de dados bem-sucedida!']);
}
?>