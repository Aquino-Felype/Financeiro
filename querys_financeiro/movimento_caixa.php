<?php

if (file_exists('../conexao/conexao.php')) {
    include('../conexao/conexao.php');
}

class MovimentoCaixa
{
    private $mysqlConnection;

    public function __construct($mysqlConnection)
    {
        $this->mysqlConnection = $mysqlConnection;
    }

    public function listarMovimentos($tipo = '', $mes = '', $ano = '', $data_inicial = '', $data_final = '', $status = '', $id_conta = '', $id_forma_pagamento = '', $id_categoria = '', $id_subcategoria = '', $id = '')
    {
        try {
            $sql = "SELECT * FROM tbl_movimento_caixa WHERE 1=1";
            $params = [];
            $types = "";

            if (!empty($tipo)) {
                $sql .= " AND tipo = ?";
                $params[] = $tipo;
                $types .= 's';
            }

            if (!empty($mes) && !empty($ano)) {
                $sql .= " AND DATE_FORMAT(data, '%m') = ? AND DATE_FORMAT(data, '%Y') = ?";
                $params[] = $mes;
                $params[] = $ano;
                $types .= 'ss';
            }

            if (!empty($data_inicial) && !empty($data_final)) {
                $sql .= " AND data BETWEEN ? AND ?";
                $params[] = $data_inicial;
                $params[] = $data_final;
                $types .= 'ss';
            }

            if (!empty($status)) {
                $sql .= " AND pago = ?";
                $params[] = $status;
                $types .= 's';
            }

            if (!empty($id_conta)) {
                $sql .= " AND id_conta_banco = ?";
                $params[] = $id_conta;
                $types .= 'i';
            }

            if (!empty($id_forma_pagamento)) {
                $sql .= " AND id_forma_pagamento = ?";
                $params[] = $id_forma_pagamento;
                $types .= 'i';
            }

            if (!empty($id_categoria)) {
                $sql .= " AND id_categoria = ?";
                $params[] = $id_categoria;
                $types .= 'i';
            }

            if (!empty($id_subcategoria)) {
                $sql .= " AND id_subcategoria = ?";
                $params[] = $id_subcategoria;
                $types .= 'i';
            }

            if (!empty($id)) {
                $sql .= " AND id = ?";
                $params[] = $id;
                $types .= 'i';
            }

            $sql .= " ORDER BY data ASC, parcela_atual ASC";

            $stmt = $this->mysqlConnection->prepare($sql);
            if (!$stmt) {
                throw new Exception("Erro na preparação da consulta: " . $this->mysqlConnection->error);
            }

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            if (!$stmt->execute()) {
                throw new Exception("Erro na execução da consulta: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $movimentos = [];

            while ($row = $result->fetch_assoc()) {
                // Adiciona informações de parcela ao registro
                $row['total_parcelas'] = $row['n_parcela'];
                $movimentos[] = $row;
            }

            $stmt->close();
            return $movimentos;

        } catch (Exception $e) {
            error_log("Erro em listarMovimentos: " . $e->getMessage());
            throw $e;
        }
    }

    public function addMovimento($nome, $valor, $data, $tipo, $id_forma_pagamento, $id_categoria, $id_subcategoria = null, $id_conta = null, $n_parcelas = null, $desconto = null, $pago = null)
    {
        try {
            // Verificar conexão
            if ($this->mysqlConnection->connect_errno) {
                throw new Exception("Falha na conexão com o MySQL: " . $this->mysqlConnection->connect_error);
            }

            // Validações básicas
            if (empty($nome) || empty($valor) || empty($data) || empty($tipo) || empty($id_forma_pagamento) || empty($id_categoria) || empty($id_conta)) {
                throw new Exception('Todos os campos obrigatórios devem ser preenchidos');
            }

            // Formata a data para o formato do MySQL
            $data_formatada = date('Y-m-d', strtotime(str_replace('/', '-', $data)));
            if ($data_formatada === false) {
                throw new Exception('Data inválida');
            }

            // Validação do valor
            $valor_original = $valor;
            $valor = str_replace(['.', ','], ['', '.'], $valor);
            if (!is_numeric($valor)) {
                throw new Exception("Valor inválido. Original: $valor_original, Após formatação: $valor");
            }
            $valor = floatval($valor);

            // Validação do tipo
            if (!in_array($tipo, ['R', 'D'])) {
                throw new Exception('Tipo inválido: ' . $tipo);
            }

            if (!$this->mysqlConnection->begin_transaction()) {
                throw new Exception('Erro ao iniciar transação: ' . $this->mysqlConnection->error);
            }

            $data_cadastro = date('Y-m-d H:i:s');
            
            // Se não houver parcelas definidas, define como 1
            $n_parcelas = $n_parcelas ?? 1;
            
            // Formata o desconto
            $desconto = $desconto ? str_replace(['.', ','], ['', '.'], $desconto) : 0;
            $desconto = floatval($desconto);
            
            // Calcula o valor por parcela (sem aplicar o desconto aqui)
            $valor_parcela = $valor / $n_parcelas;
            
            try {
                // Primeiro, insere o registro principal para obter o id_conta_pai
                $query = "INSERT INTO tbl_movimento_caixa (nome, valor, data, data_cadastro, tipo, id_forma_pagamento, id_categoria, id_subcategoria, id_conta_banco, n_parcela, parcela_atual, desconto, pago) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $stmt = $this->mysqlConnection->prepare($query);
                if (!$stmt) {
                    throw new Exception('Erro na preparação da consulta: ' . $this->mysqlConnection->error);
                }

                // Calcula o desconto por parcela
                $desconto_por_parcela = $desconto / $n_parcelas;

                // Nome da primeira parcela
                $nome_parcela = $nome;
                if ($n_parcelas > 1) {
                    $nome_parcela .= " (1/{$n_parcelas})";
                }

                $parcela_atual = 1;

                // Insere a primeira parcela
                if (!$stmt->bind_param(
                    "sdssssiiiiiis",
                    $nome_parcela,
                    $valor_parcela,
                    $data_formatada,
                    $data_cadastro,
                    $tipo,
                    $id_forma_pagamento,
                    $id_categoria,
                    $id_subcategoria,
                    $id_conta,
                    $n_parcelas,
                    $parcela_atual, // parcela_atual
                    $desconto_por_parcela,
                    $pago
                )) {
                    throw new Exception('Erro no bind_param: ' . $stmt->error);
                }

                if (!$stmt->execute()) {
                    throw new Exception('Erro ao executar a consulta: ' . $stmt->error . ' (Errno: ' . $stmt->errno . ')');
                }

                // Obtém o ID do primeiro registro para usar como id_conta_pai
                $id_conta_pai = $this->mysqlConnection->insert_id;

                // Se houver mais parcelas, insere as demais
                if ($n_parcelas > 1) {
                    $query = "INSERT INTO tbl_movimento_caixa (nome, valor, data, data_cadastro, tipo, id_forma_pagamento, id_categoria, id_subcategoria, id_conta_banco, n_parcela, parcela_atual, desconto, pago, id_conta_pai) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                    $stmt = $this->mysqlConnection->prepare($query);
                    if (!$stmt) {
                        throw new Exception('Erro na preparação da consulta: ' . $this->mysqlConnection->error);
                    }

                    // Insere as parcelas restantes
                    for ($parcela_atual = 2; $parcela_atual <= $n_parcelas; $parcela_atual++) {
                        // Calcula a data da parcela
                        $data_parcela = date('Y-m-d', strtotime($data_formatada . ' + ' . ($parcela_atual - 1) . ' months'));
                        
                        // Nome com número da parcela
                        $nome_parcela = $nome . " ({$parcela_atual}/{$n_parcelas})";

                        if (!$stmt->bind_param(
                            "sdsssiiiiiiisi",
                            $nome_parcela,
                            $valor_parcela,
                            $data_parcela,
                            $data_cadastro,
                            $tipo,
                            $id_forma_pagamento,
                            $id_categoria,
                            $id_subcategoria,
                            $id_conta,
                            $n_parcelas,
                            $parcela_atual,
                            $desconto_por_parcela,
                            $pago,
                            $id_conta_pai
                        )) {
                            throw new Exception('Erro no bind_param: ' . $stmt->error);
                        }

                        if (!$stmt->execute()) {
                            throw new Exception('Erro ao executar a consulta: ' . $stmt->error . ' (Errno: ' . $stmt->errno . ')');
                        }
                    }
                }

                $stmt->close();

                if (!$this->mysqlConnection->commit()) {
                    throw new Exception('Erro ao finalizar transação: ' . $this->mysqlConnection->error);
                }

                return true;
            } catch (Exception $e) {
                if ($this->mysqlConnection->connect_errno == 0) {
                    $this->mysqlConnection->rollback();
                }
                throw $e;
            }
        } catch (Exception $e) {
            throw new Exception('Erro ao adicionar movimento: ' . $e->getMessage());
        }
    }

    public function getMovimentoPorId($id)
    {
        try {
            error_log("Buscando movimento com ID: " . $id);
            
            $query = "SELECT m.*, c.nome_conta, fp.nome as nome_forma_pagamento, cat.nome as nome_categoria,
                             subcat.nome_subcategoria 
                      FROM tbl_movimento_caixa m
                      LEFT JOIN tbl_contas c ON m.id_conta_banco = c.id
                      LEFT JOIN tbl_formas_pagamento fp ON m.id_forma_pagamento = fp.id
                      LEFT JOIN tbl_categorias cat ON m.id_categoria = cat.id
                      LEFT JOIN tbl_subcategorias subcat ON m.id_subcategoria = subcat.id
                      WHERE m.id = ? OR (m.id_conta_pai = ? AND m.parcela_atual = 1)";

            $stmt = $this->mysqlConnection->prepare($query);
            if (!$stmt) {
                throw new Exception('Erro na preparação da consulta: ' . $this->mysqlConnection->error);
            }

            $stmt->bind_param("ii", $id, $id);
            if (!$stmt->execute()) {
                throw new Exception('Erro ao executar a consulta: ' . $stmt->error);
            }

            $result = $stmt->get_result();
            $movimento = $result->fetch_assoc();

            if (!$movimento) {
                error_log("Movimento não encontrado para o ID: " . $id);
                // Vamos fazer uma consulta direta para debug
                $query_debug = "SELECT * FROM tbl_movimento_caixa WHERE id = ?";
                $stmt_debug = $this->mysqlConnection->prepare($query_debug);
                $stmt_debug->bind_param("i", $id);
                $stmt_debug->execute();
                $result_debug = $stmt_debug->get_result();
                $movimento_debug = $result_debug->fetch_assoc();
                error_log("Debug - Resultado da consulta direta: " . print_r($movimento_debug, true));
                $stmt_debug->close();
                return null;
            }

            error_log("Movimento encontrado: " . print_r($movimento, true));
            $stmt->close();
            return $movimento;
        } catch (Exception $e) {
            error_log('Erro ao buscar movimento: ' . $e->getMessage());
            return null;
        }
    }

    public function updateMovimento()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->mysqlConnection->begin_transaction();

            try {
                $id = $_POST['id'];
                error_log("ID do movimento a ser atualizado: " . $id);
                error_log("POST data: " . print_r($_POST, true));

                // Remove a parte das parcelas antigas do nome, se existir
                $nome = $_POST['nome'];
                if (preg_match('/^(.*?)\s*\([0-9]+\/[0-9]+\)/', $nome, $matches)) {
                    $nome = trim($matches[1]); // Pega apenas o nome base, sem as parcelas
                }

                $valor = str_replace(['R$', '.', ','], ['', '', '.'], $_POST['valor']);
                $data = $_POST['data'];
                $tipo = $_POST['tipo'];
                $id_forma_pagamento = $_POST['id_forma_pagamento'];
                $id_categoria = $_POST['id_categoria'];
                $id_subcategoria = !empty($_POST['id_subcategoria']) ? $_POST['id_subcategoria'] : null;
                $id_conta = $_POST['id_conta'];
                $desconto = str_replace(['.', ','], ['', '.'], $_POST['desconto']);
                $desconto = floatval($desconto);
                $n_parcelas = (int)$_POST['n_parcelas'];

                // Busca o movimento atual e suas parcelas existentes
                $movimento_atual = $this->getMovimentoPorId($id);
                if (!$movimento_atual) {
                    // Tenta buscar diretamente da tabela
                    $query = "SELECT * FROM tbl_movimento_caixa WHERE id = ?";
                    $stmt = $this->mysqlConnection->prepare($query);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $movimento_atual = $result->fetch_assoc();
                    $stmt->close();

                    if (!$movimento_atual) {
                        throw new Exception("Movimento não encontrado para o ID: " . $id);
                    }
                }

                error_log("Movimento atual encontrado: " . print_r($movimento_atual, true));
                
                // Busca todas as parcelas existentes
                $query = "SELECT * FROM tbl_movimento_caixa WHERE id = ? OR id_conta_pai = ? ORDER BY parcela_atual";
                $stmt = $this->mysqlConnection->prepare($query);
                $stmt->bind_param("ii", $id, $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $parcelas_existentes = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();

                // Se está alterando o número de parcelas
                if ($n_parcelas != $movimento_atual['n_parcela']) {
                    error_log("Alterando número de parcelas de {$movimento_atual['n_parcela']} para {$n_parcelas}");
                    
                    // Calcula o novo valor por parcela
                    $valor_parcela = $valor / $n_parcelas;
                    $desconto_por_parcela = $desconto / $n_parcelas;
                    
                    // Atualiza as parcelas existentes
                    $query = "UPDATE tbl_movimento_caixa 
                            SET nome = ?,
                                valor = ?,
                                desconto = ?,
                                n_parcela = ?
                            WHERE id = ? OR id_conta_pai = ?";

                    $stmt = $this->mysqlConnection->prepare($query);
                    if (!$stmt) {
                        throw new Exception("Erro na preparação da consulta: " . $this->mysqlConnection->error);
                    }

                    // Busca todas as parcelas existentes para atualizar
                    $query_parcelas = "SELECT * FROM tbl_movimento_caixa WHERE id = ? OR id_conta_pai = ? ORDER BY parcela_atual";
                    $stmt_parcelas = $this->mysqlConnection->prepare($query_parcelas);
                    $stmt_parcelas->bind_param("ii", $id, $id);
                    $stmt_parcelas->execute();
                    $result_parcelas = $stmt_parcelas->get_result();
                    
                    while ($parcela = $result_parcelas->fetch_assoc()) {
                        $nome_parcela = $nome . " ({$parcela['parcela_atual']}/{$n_parcelas})";
                        $stmt->bind_param(
                            "sddiii",
                            $nome_parcela,
                            $valor_parcela,
                            $desconto_por_parcela,
                            $n_parcelas,
                            $parcela['id'],
                            $parcela['id']
                        );
                        if (!$stmt->execute()) {
                            throw new Exception("Erro ao atualizar parcela {$parcela['parcela_atual']}: " . $stmt->error);
                        }
                    }
                    $stmt_parcelas->close();
                    $stmt->close();

                    // Se aumentou o número de parcelas, cria as novas
                    if ($n_parcelas > $movimento_atual['n_parcela']) {
                        $query = "INSERT INTO tbl_movimento_caixa (
                            nome, valor, data, data_cadastro, tipo, 
                            id_forma_pagamento, id_categoria, id_subcategoria, 
                            id_conta_banco, n_parcela, parcela_atual, desconto, 
                            pago, id_conta_pai
                        ) VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, 'N', ?)";

                        $stmt = $this->mysqlConnection->prepare($query);
                        if (!$stmt) {
                            throw new Exception("Erro na preparação da consulta: " . $this->mysqlConnection->error);
                        }

                        // Insere apenas as parcelas adicionais
                        for ($parcela = $movimento_atual['n_parcela'] + 1; $parcela <= $n_parcelas; $parcela++) {
                            $nome_parcela = $nome . " ({$parcela}/{$n_parcelas})";
                            $data_parcela = date('Y-m-d', strtotime($data . " +" . ($parcela - 1) . " months"));

                            if ($id_subcategoria === null) {
                                $id_subcategoria = 0;
                            }

                            $stmt->bind_param(
                                "sdssiiiiiiid",
                                $nome_parcela,
                                $valor_parcela,
                                $data_parcela,
                                $tipo,
                                $id_forma_pagamento,
                                $id_categoria,
                                $id_subcategoria,
                                $id_conta,
                                $n_parcelas,
                                $parcela,
                                $desconto_por_parcela,
                                $id
                            );

                            if (!$stmt->execute()) {
                                throw new Exception("Erro ao inserir parcela {$parcela}: " . $stmt->error);
                            }
                        }
                        $stmt->close();
                    } else {
                        // Se diminuiu o número de parcelas, remove as excedentes
                        $query = "DELETE FROM tbl_movimento_caixa 
                                WHERE (id = ? OR id_conta_pai = ?) 
                                AND parcela_atual > ?";
                        
                        $stmt = $this->mysqlConnection->prepare($query);
                        if (!$stmt) {
                            throw new Exception("Erro na preparação da consulta: " . $this->mysqlConnection->error);
                        }
                        
                        $stmt->bind_param("iii", $id, $id, $n_parcelas);
                        if (!$stmt->execute()) {
                            throw new Exception("Erro ao remover parcelas excedentes: " . $stmt->error);
                        }
                        $stmt->close();
                    }
                } else {
                    // Se não está alterando o número de parcelas, apenas atualiza os valores
                    $query = "UPDATE tbl_movimento_caixa 
                             SET nome = ?,
                                 valor = ?,
                                 data = ?,
                                 tipo = ?,
                                 id_forma_pagamento = ?,
                                 id_categoria = ?,
                                 id_subcategoria = ?,
                                 id_conta_banco = ?,
                                 desconto = ?
                             WHERE id = ? OR id_conta_pai = ?";

                    $stmt = $this->mysqlConnection->prepare($query);
                    if (!$stmt) {
                        throw new Exception("Erro na preparação da consulta: " . $this->mysqlConnection->error);
                    }

                    if ($id_subcategoria === null) {
                        $id_subcategoria = 0;
                    }

                    $valor_parcela = $valor / $movimento_atual['n_parcela'];
                    $desconto_por_parcela = $desconto / $movimento_atual['n_parcela'];

                    $stmt->bind_param(
                        "sdssiiiiiii",
                        $nome,
                        $valor_parcela,
                        $data,
                        $tipo,
                        $id_forma_pagamento,
                        $id_categoria,
                        $id_subcategoria,
                        $id_conta,
                        $desconto_por_parcela,
                        $id,
                        $id
                    );

                    if (!$stmt->execute()) {
                        throw new Exception("Erro ao atualizar movimento: " . $stmt->error);
                    }
                    $stmt->close();
                }

                $this->mysqlConnection->commit();
                $_SESSION['message'] = 'Movimento atualizado com sucesso!';
                $_SESSION['message_type'] = 'success';
                
                $mes = date('m', strtotime($data));
                $ano = date('Y', strtotime($data));
                header("Location: /mandareceita/pages/financeiro/financeiro/index.php?tipo=$tipo&mes=$mes&ano=$ano");
                exit();
            } catch (Exception $e) {
                $this->mysqlConnection->rollback();
                $_SESSION['message'] = $e->getMessage();
                $_SESSION['message_type'] = 'error';
                
                header("Location: /mandareceita/pages/financeiro/financeiro/alteracao_movimento.php?id=" . $id);
                exit();
            }
        }
    }

    public function deletaMovimento($id = null)
    {
        if ($id === null && isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] === 'delete') {
            $id = $_GET['id'];
        }

        if ($id) {
            try {
                // Inicia a transação
                $this->mysqlConnection->begin_transaction();

                // Primeiro, obtém as informações do movimento
                $query_info = "SELECT id, id_conta_pai, n_parcela FROM tbl_movimento_caixa WHERE id = ?";
                $stmt_info = $this->mysqlConnection->prepare($query_info);
                if (!$stmt_info) {
                    throw new Exception('Erro na preparação da consulta MySQL: ' . $this->mysqlConnection->error);
                }

                $stmt_info->bind_param("i", $id);
                if (!$stmt_info->execute()) {
                    throw new Exception('Erro ao obter informações do movimento: ' . $stmt_info->error);
                }

                $result = $stmt_info->get_result();
                $movimento = $result->fetch_assoc();
                $stmt_info->close();

                if (!$movimento) {
                    throw new Exception('Movimento não encontrado');
                }

                // Se solicitado para excluir todas as parcelas e o movimento tem parcelas
                if (isset($_GET['all_installments']) && $_GET['all_installments'] == 1 && 
                    ($movimento['id_conta_pai'] !== null || $movimento['n_parcela'] > 1)) {
                    
                    // Se é um movimento filho, usa o id_conta_pai, senão usa o próprio id
                    $id_para_excluir = $movimento['id_conta_pai'] ?? $id;
                    
                    // Deleta todas as parcelas relacionadas
                    $query = "DELETE FROM tbl_movimento_caixa WHERE id = ? OR id_conta_pai = ?";
                    $stmt = $this->mysqlConnection->prepare($query);
                    if (!$stmt) {
                        throw new Exception('Erro na preparação da consulta MySQL: ' . $this->mysqlConnection->error);
                    }

                    $stmt->bind_param("ii", $id_para_excluir, $id_para_excluir);
                } else {
                    // Deleta apenas o registro específico
                    $query = "DELETE FROM tbl_movimento_caixa WHERE id = ?";
                    $stmt = $this->mysqlConnection->prepare($query);
                    if (!$stmt) {
                        throw new Exception('Erro na preparação da consulta MySQL: ' . $this->mysqlConnection->error);
                    }

                    $stmt->bind_param("i", $id);
                }

                if (!$stmt->execute()) {
                    throw new Exception('Erro ao remover Movimento: ' . $stmt->error);
                }

                $stmt->close();
                $this->mysqlConnection->commit();
                
                if (isset($_GET['id'])) {
                    $_SESSION['message'] = 'Movimento(s) removido(s) com sucesso!';
                    $_SESSION['message_type'] = 'success';

                    $mes = date('m');
                    $ano = date('Y');
                    
                    header("Location: /mandareceita/pages/financeiro/financeiro/index.php?tipo={$_GET['tipo']}&mes=$mes&ano=$ano");
                    exit();
                }
                
                return true;

            } catch (Exception $e) {
                $this->mysqlConnection->rollback();
                
                if (isset($_GET['id'])) {
                    $_SESSION['message'] = 'Erro ao remover movimento: ' . $e->getMessage();
                    $_SESSION['message_type'] = 'error';
                    
                    header("Location: /mandareceita/pages/financeiro/financeiro/index.php?tipo={$_GET['tipo']}");
                    exit();
                }
                
                throw $e;
            }
        }
        return false;
    }

    public function atualizarPagamento($id, $valor_pago, $juros, $data_pagamento)
    {
        try {
            $this->mysqlConnection->begin_transaction();

            $query = "UPDATE tbl_movimento_caixa 
                     SET pago = 'S',
                         valor_pago = ?,
                         juros = ?,
                         data_pagamento = ?
                     WHERE id = ?";

            $stmt = $this->mysqlConnection->prepare($query);
            if (!$stmt) {
                throw new Exception("Erro na preparação da consulta: " . $this->mysqlConnection->error);
            }

            $stmt->bind_param("ddsi", $valor_pago, $juros, $data_pagamento, $id);

            if (!$stmt->execute()) {
                throw new Exception("Erro ao atualizar pagamento: " . $stmt->error);
            }

            $stmt->close();
            $this->mysqlConnection->commit();
            return true;
        } catch (Exception $e) {
            $this->mysqlConnection->rollback();
            throw $e;
        }
    }
}
