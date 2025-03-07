<?php
require '../conexao/conexao.php';
require '../querys/movimento_caixa.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id']) || !isset($data['valor_pago']) || !isset($data['juros']) || !isset($data['data_pagamento'])) {
        throw new Exception('Dados incompletos');
    }

    $movimentoCaixa = new MovimentoCaixa($connectionmysql);
    $movimentoCaixa->atualizarPagamento(
        $data['id'],
        str_replace(['R$', '.', ','], ['', '', '.'], $data['valor_pago']),
        str_replace(['R$', '.', ','], ['', '', '.'], $data['juros']),
        $data['data_pagamento']
    );

    echo json_encode(['success' => true, 'message' => 'Pagamento registrado com sucesso']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
