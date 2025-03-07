<?php

if (file_exists('../../conexao/conexao.php')) {
    include('../../conexao/conexao.php');
}

class Contas
{
    private $mysqlConnection;

    // Construtor que recebe a conexão e a armazena em uma propriedade
    public function __construct($mysqlConnection)
    {
        $this->mysqlConnection = $mysqlConnection;
    }

    public function listarContas($id = '', $sql = '')
    {
        // Consulta SQL padrão para buscar todas as contas
        $sql = "SELECT id, nome_conta, agencia, limite_inicial, ativo FROM tbl_contas WHERE 1=1 $sql";

        // Armazena os parâmetros e seus tipos para o bind
        $params = [];
        $types = '';

        // Condicional para verificar se os parâmetros foram passados e construir a query e os parâmetros adequados
        if (!empty($id)) {
            $sql .= " AND tbl_contas.id = ?";
            $params[] = $id;
            $types .= 'i';  // Tipo 'i' para integer
        }

        // Prepara a consulta
        $stmt = $this->mysqlConnection->prepare($sql);
        if (!$stmt) {
            die('Erro na preparação da consulta MySQL: ' . $this->mysqlConnection->error);
        }

        // Se houver parâmetros, fazemos o bind deles
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        // Executa a consulta
        if (!$stmt->execute()) {
            die('Erro na execução da consulta MySQL: ' . $stmt->error);
        }

        // Obtém o resultado
        $result = $stmt->get_result();
        if (!$result) {
            die('Erro ao obter resultado MySQL: ' . $stmt->error);
        }

        $contas = [];
        while ($row = $result->fetch_assoc()) {
            $contas[] = $row;
        }

        // Fecha a consulta
        $stmt->close();

        return $contas;
    }

    public function addNovaConta()
    {
        // Verifica se os dados foram enviados via POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Verifica se os campos necessários estão presentes
            if (isset($_POST['nome_conta'])) {

                // Atribui os valores do POST
                $nome_conta = $_POST['nome_conta'];
                $agencia = $_POST['agencia'] ?? '';

                // Trata o valor do limite inicial para aceitar vírgula e ponto
                $limite_inicial = $_POST['limite_inicial'] ?? '0,00';
                $limite_inicial = str_replace('.', '', $limite_inicial); // Remove pontos de milhar
                $limite_inicial = str_replace(',', '.', $limite_inicial); // Converte vírgula em ponto
                $limite_inicial = sprintf('%.2f', (float)$limite_inicial); // Garante precisão de 2 casas decimais

                $ativo = $_POST['ativo'] ?? 'S';

                // Verifica se já existe uma conta com o mesmo nome
                $check_query = "SELECT COUNT(*) as count FROM tbl_contas WHERE nome_conta = ?";
                $check_stmt = $this->mysqlConnection->prepare($check_query);

                if (!$check_stmt) {
                    echo "<script>alert('Erro na preparação da consulta MySQL: " . addslashes($this->mysqlConnection->error) . "');</script>";
                    return;
                }

                $check_stmt->bind_param("s", $nome_conta);
                $check_stmt->execute();
                $result = $check_stmt->get_result();
                $row = $result->fetch_assoc();

                if ($row['count'] > 0) {
                    echo "<script>alert('Já existe uma conta com este nome.');</script>";
                    return;
                }

                $check_stmt->close();

                // Prepara a consulta SQL de inserção
                $query = "INSERT INTO tbl_contas (nome_conta, agencia, limite_inicial, ativo) VALUES (?, ?, ?, ?)";
                $stmt = $this->mysqlConnection->prepare($query);

                if (!$stmt) {
                    echo "<script>alert('Erro na preparação da consulta MySQL: " . addslashes($this->mysqlConnection->error) . "');</script>";
                    return;
                }

                // Vincula os parâmetros
                $stmt->bind_param("ssds", $nome_conta, $agencia, $limite_inicial, $ativo);

                // Executa a consulta
                if ($stmt->execute()) {
                    echo "<script>
                        alert('Conta adicionada com sucesso!');
                        window.location.href = 'index.php';
                    </script>";
                } else {
                    echo "<script>alert('Erro ao adicionar conta: " . addslashes($stmt->error) . "');</script>";
                }

                // Fecha a declaração
                $stmt->close();
                return;
            } else {
                echo "<script>alert('O campo Nome da Conta é obrigatório.');</script>";
                return;
            }
        }
    }

    public function updateConta()
    {
        // Verifica se os dados foram enviados via POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Verifica se os campos necessários estão presentes
            if (isset($_POST['id_conta']) && isset($_POST['nome_conta'])) {

                // Atribui os valores do POST
                $id_conta = $_POST['id_conta'];
                $nome_conta = $_POST['nome_conta'];
                $agencia = $_POST['agencia'] ?? '';

                // Trata o valor do limite inicial para aceitar vírgula e ponto
                $limite_inicial = $_POST['limite_inicial'] ?? '0,00';
                $limite_inicial = str_replace('.', '', $limite_inicial); // Remove pontos de milhar
                $limite_inicial = str_replace(',', '.', $limite_inicial); // Converte vírgula em ponto
                $limite_inicial = sprintf('%.2f', (float)$limite_inicial); // Garante precisão de 2 casas decimais

                $ativo = $_POST['ativo'] ?? 'S';

                // Verifica se já existe uma conta com o mesmo nome (exceto o registro atual)
                $check_query = "SELECT COUNT(*) as count FROM tbl_contas WHERE nome_conta = ? AND id != ?";
                $check_stmt = $this->mysqlConnection->prepare($check_query);

                if (!$check_stmt) {
                    echo "<script>alert('Erro na preparação da consulta MySQL: " . addslashes($this->mysqlConnection->error) . "');</script>";
                    return;
                }

                $check_stmt->bind_param("si", $nome_conta, $id_conta);
                $check_stmt->execute();
                $result = $check_stmt->get_result();
                $row = $result->fetch_assoc();

                if ($row['count'] > 0) {
                    echo "<script>alert('Já existe uma conta com este nome.');</script>";
                    return;
                }

                $check_stmt->close();

                // Prepara a consulta SQL de atualização
                $query = "UPDATE tbl_contas SET nome_conta = ?, agencia = ?, limite_inicial = ?, ativo = ? WHERE id = ?";
                $stmt = $this->mysqlConnection->prepare($query);

                if (!$stmt) {
                    echo "<script>alert('Erro na preparação da consulta MySQL: " . addslashes($this->mysqlConnection->error) . "');</script>";
                    return;
                }

                // Vincula os parâmetros
                $stmt->bind_param("ssdsi", $nome_conta, $agencia, $limite_inicial, $ativo, $id_conta);

                // Executa a consulta
                if ($stmt->execute()) {
                    echo "<script>
                        alert('Conta atualizada com sucesso!');
                        window.location.href = 'index.php';
                    </script>";
                } else {
                    echo "<script>alert('Erro ao atualizar conta: " . addslashes($stmt->error) . "');</script>";
                }

                // Fecha a declaração
                $stmt->close();
                return;
            } else {
                echo "<script>alert('Os campos ID e Nome da Conta são obrigatórios.');</script>";
                return;
            }
        }
    }

    public function deletaConta()
    {
        // Verifica se o ID e a ação foram enviados via GET
        if (isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] === 'delete') {
            try {
                $this->mysqlConnection->begin_transaction();

                // Escapa o valor do GET
                $id_conta = $this->mysqlConnection->real_escape_string($_GET['id']);

                // Primeiro, verifica se a conta está sendo usada em algum movimento
                $check_query = "SELECT COUNT(*) as count FROM tbl_movimento_caixa WHERE id_conta_banco = ?";
                $check_stmt = $this->mysqlConnection->prepare($check_query);
                
                if (!$check_stmt) {
                    throw new Exception('Erro na preparação da consulta de verificação: ' . $this->mysqlConnection->error);
                }

                $check_stmt->bind_param("i", $id_conta);
                $check_stmt->execute();
                $result = $check_stmt->get_result();
                $row = $result->fetch_assoc();
                $check_stmt->close();

                if ($row['count'] > 0) {
                    throw new Exception('Esta conta não pode ser excluída pois está sendo usada em movimentos financeiros.');
                }

                // Se não houver movimentos, procede com a exclusão
                $query = "DELETE FROM tbl_contas WHERE id = ?";
                $stmt = $this->mysqlConnection->prepare($query);
                
                if (!$stmt) {
                    throw new Exception('Erro na preparação da consulta de exclusão: ' . $this->mysqlConnection->error);
                }

                $stmt->bind_param("i", $id_conta);

                if (!$stmt->execute()) {
                    throw new Exception('Erro ao excluir a conta: ' . $stmt->error);
                }

                $stmt->close();
                $this->mysqlConnection->commit();
                
                $_SESSION['message'] = 'Conta removida com sucesso!';
                $_SESSION['message_type'] = 'success';
                
                header("Location: index.php");
                exit();

            } catch (Exception $e) {
                $this->mysqlConnection->rollback();
                $_SESSION['message'] = $e->getMessage();
                $_SESSION['message_type'] = 'error';
                
                header("Location: index.php");
                exit();
            }
        }
    }

    public function ListaBancosBrasil($id = '', $sql = '')
    {
        // Consulta SQL padrão para buscar todas as contas
        $sql = "SELECT * FROM bancos WHERE 1=1 $sql";

        // Armazena os parâmetros e seus tipos para o bind
        $params = [];
        $types = '';

        // Condicional para verificar se os parâmetros foram passados e construir a query e os parâmetros adequados
        if (!empty($id)) {
            $sql .= " AND bancos.id = ?";
            $params[] = $id;
            $types .= 'i';  // Tipo 'i' para integer
        }

        // Prepara a consulta
        $stmt = $this->mysqlConnection->prepare($sql);
        if (!$stmt) {
            die('Erro na preparação da consulta MySQL: ' . $this->mysqlConnection->error);
        }

        // Se houver parâmetros, fazemos o bind deles
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        // Executa a consulta
        if (!$stmt->execute()) {
            die('Erro na execução da consulta MySQL: ' . $stmt->error);
        }

        // Obtém o resultado
        $result = $stmt->get_result();
        if (!$result) {
            die('Erro ao obter resultado MySQL: ' . $stmt->error);
        }

        $contas = [];
        while ($row = $result->fetch_assoc()) {
            $contas[] = $row;
        }

        // Fecha a consulta
        $stmt->close();

        return $contas;
    }
}
