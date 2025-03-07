<?php

if (!isset($connectionmysql)) {
    include('../conexao/conexao.php');
}

class Transferencias
{
    private $mysqlConnection;

    public function __construct($mysqlConnection)
    {
        $this->mysqlConnection = $mysqlConnection;
    }

    public function listarTransferencia($id = '', $sql = '')
    {
        $sql = "SELECT id, id_conta_banco_origem, id_conta_banco_destino, valor, saldo_origem_momento, saldo_destino_momento, data FROM tbl_transferencias WHERE 1=1 $sql";
        $params = [];
        $types = '';

        if (!empty($id)) {
            $sql .= " AND id = ?";
            $params[] = $id;
            $types .= 'i';
        }

        $stmt = $this->mysqlConnection->prepare($sql);
        if (!$stmt) {
            die('Erro na preparação da consulta MySQL: ' . $this->mysqlConnection->error);
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            die('Erro na execução da consulta MySQL: ' . $stmt->error);
        }

        $result = $stmt->get_result();
        $transferencias = [];

        while ($row = $result->fetch_assoc()) {
            $transferencias[] = $row;
        }

        $stmt->close();
        return $transferencias;
    }

    private function getSaldoConta($id_conta)
    {
        $sql = "SELECT limite_inicial FROM tbl_contas WHERE id = ?";
        $stmt = $this->mysqlConnection->prepare($sql);
        if (!$stmt) {
            die('Erro ao preparar consulta de saldo: ' . $this->mysqlConnection->error);
        }
        $stmt->bind_param("i", $id_conta);
        if (!$stmt->execute()) {
            die('Erro ao consultar saldo: ' . $stmt->error);
        }
        $result = $stmt->get_result();
        $conta = $result->fetch_assoc();
        $stmt->close();
        // Retorna o saldo com precisão de 2 casas decimais
        return number_format((float)$conta['limite_inicial'], 2, '.', '');
    }

    private function atualizarSaldoContas($id_conta_origem, $id_conta_destino, $valor)
    {
        // Atualiza o saldo da conta de origem (subtrai o valor)
        $sql_origem = "UPDATE tbl_contas SET limite_inicial = ROUND(limite_inicial - ?, 2) WHERE id = ?";
        $stmt_origem = $this->mysqlConnection->prepare($sql_origem);
        if (!$stmt_origem) {
            die('Erro ao preparar atualização da conta origem: ' . $this->mysqlConnection->error);
        }
        $stmt_origem->bind_param("di", $valor, $id_conta_origem);
        if (!$stmt_origem->execute()) {
            die('Erro ao atualizar conta origem: ' . $stmt_origem->error);
        }
        $stmt_origem->close();

        // Atualiza o saldo da conta de destino (adiciona o valor)
        $sql_destino = "UPDATE tbl_contas SET limite_inicial = ROUND(limite_inicial + ?, 2) WHERE id = ?";
        $stmt_destino = $this->mysqlConnection->prepare($sql_destino);
        if (!$stmt_destino) {
            die('Erro ao preparar atualização da conta destino: ' . $this->mysqlConnection->error);
        }
        $stmt_destino->bind_param("di", $valor, $id_conta_destino);
        if (!$stmt_destino->execute()) {
            die('Erro ao atualizar conta destino: ' . $stmt_destino->error);
        }
        $stmt_destino->close();
    }

    public function addNovaTransferencia()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_conta_banco_origem = $_POST['id_conta_banco_origem'] ?? '';
            $id_conta_banco_destino = $_POST['id_conta_banco_destino'] ?? '';
            // Converte o valor para formato decimal, preservando centavos
            $valor = str_replace(',', '.', str_replace('.', '', $_POST['valor'] ?? ''));
            $data = $_POST['data'] ?? date('Y-m-d');

            try {
                // Validações básicas
                if (empty($id_conta_banco_origem) || empty($id_conta_banco_destino) || empty($valor)) {
                    throw new Exception("Todos os campos são obrigatórios");
                }

                // Converte IDs para inteiros para garantir comparação correta
                $id_conta_banco_origem = (int)$id_conta_banco_origem;
                $id_conta_banco_destino = (int)$id_conta_banco_destino;

                // Validação de contas iguais
                if ($id_conta_banco_origem === $id_conta_banco_destino) {
                    throw new Exception("A conta de origem e destino não podem ser a mesma");
                }

                // Validação do valor com precisão de 2 casas decimais
                if (!is_numeric($valor) || $valor <= 0) {
                    throw new Exception("O valor deve ser um número positivo");
                }
                
                // Formata o valor para ter exatamente 2 casas decimais
                $valor = number_format((float)$valor, 2, '.', '');

                $this->mysqlConnection->begin_transaction();

                // Obtém os saldos atuais antes da transferência
                $saldo_origem = $this->getSaldoConta($id_conta_banco_origem);
                $saldo_destino = $this->getSaldoConta($id_conta_banco_destino);

                // Verifica se há saldo suficiente (comparando com precisão de 2 casas decimais)
                $saldo_apos_transferencia = number_format($saldo_origem - $valor, 2, '.', '');
                if ($saldo_apos_transferencia < 0) {
                    throw new Exception("Saldo insuficiente na conta de origem. Saldo atual: R$ " . number_format($saldo_origem, 2, ',', '.'));
                }

                // Insere a transferência com os saldos do momento
                $query = "INSERT INTO tbl_transferencias (id_conta_banco_origem, id_conta_banco_destino, valor, saldo_origem_momento, saldo_destino_momento, data) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $this->mysqlConnection->prepare($query);
                if (!$stmt) {
                    throw new Exception('Erro na preparação da consulta MySQL: ' . $this->mysqlConnection->error);
                }

                $stmt->bind_param("iiddds", $id_conta_banco_origem, $id_conta_banco_destino, $valor, $saldo_origem, $saldo_destino, $data);
                
                if (!$stmt->execute()) {
                    throw new Exception('Erro ao adicionar transferência: ' . $stmt->error);
                }
                $stmt->close();

                // Atualiza os saldos das contas
                $this->atualizarSaldoContas($id_conta_banco_origem, $id_conta_banco_destino, $valor);

                $this->mysqlConnection->commit();
                
                // Exibe a mensagem antes do redirecionamento
                echo "<script>
                    alert('Transferência executada com sucesso!');
                    window.location.href = 'index.php';
                </script>";
                exit();
            } catch (Exception $e) {
                $this->mysqlConnection->rollback();
                
                // Exibe a mensagem de erro antes do redirecionamento
                echo "<script>
                    alert('" . addslashes($e->getMessage()) . "');
                    window.location.href = 'index.php';
                </script>";
                exit();
            }

            // Fallback para o caso do JavaScript falhar
            header('Location: index.php');
            exit();
        }
    }
}
