<?php

if (file_exists('../conexao/conexao.php')) {
    include('../conexao/conexao.php');
}

class FormasPagamento
{
    private $mysqlConnection;

    // Construtor que recebe a conexão e a armazena em uma propriedade
    public function __construct($mysqlConnection)
    {
        $this->mysqlConnection = $mysqlConnection;
    }

    public function listarFormasPagamento($id = '', $sql = '')
    {
        // Consulta SQL padrão para buscar todas as FormasPagamento
        $sql = "SELECT id AS id, nome_forma_pagamento AS nome_forma_pagamento, observacao AS observacao FROM tbl_formas_pagamento WHERE 1=1 $sql";

        // Armazena os parâmetros e seus tipos para o bind
        $params = [];
        $types = '';

        // Adiciona condição de ID se fornecido
        if (!empty($id)) {
            $sql .= " AND id = ?";
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

        $tiposFormasPagamento = [];
        while ($row = $result->fetch_assoc()) {
            $tiposFormasPagamento[] = $row;
        }

        // Fecha a consulta
        $stmt->close();

        return $tiposFormasPagamento;
    }

    public function addNovaFormaPagamento()
    {
        // Verifica se os dados foram enviados via POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Verifica se os campos necessários estão presentes
            if (isset($_POST['nome_forma_pagamento']) && isset($_POST['observacao'])) {

                // Atribui os valores do POST
                $nome_forma_pagamento = $_POST['nome_forma_pagamento'];
                $observacao = $_POST['observacao'];

                // Verifica se já existe uma forma de pagamento com o mesmo nome
                $check_query = "SELECT COUNT(*) as count FROM tbl_formas_pagamento WHERE nome_forma_pagamento = ?";
                $check_stmt = $this->mysqlConnection->prepare($check_query);
                
                if (!$check_stmt) {
                    echo "<script>alert('Erro na preparação da consulta MySQL: " . addslashes($this->mysqlConnection->error) . "');</script>";
                    return;
                }

                $check_stmt->bind_param("s", $nome_forma_pagamento);
                $check_stmt->execute();
                $result = $check_stmt->get_result();
                $row = $result->fetch_assoc();
                
                if ($row['count'] > 0) {
                    echo "<script>alert('Já existe uma forma de pagamento com este nome.');</script>";
                    return;
                }
                
                $check_stmt->close();

                // Prepara a consulta SQL de inserção
                $query = "INSERT INTO tbl_formas_pagamento (nome_forma_pagamento, observacao) VALUES (?, ?)";
                $stmt = $this->mysqlConnection->prepare($query);

                if (!$stmt) {
                    echo "<script>alert('Erro na preparação da consulta MySQL: " . addslashes($this->mysqlConnection->error) . "');</script>";
                    return;
                }

                // Vincula os parâmetros
                $stmt->bind_param("ss", $nome_forma_pagamento, $observacao);

                // Executa a consulta
                if ($stmt->execute()) {
                    echo "<script>
                        alert('Forma de pagamento adicionada com sucesso!');
                        window.location.href = 'index.php';
                    </script>";
                } else {
                    echo "<script>alert('Erro ao adicionar forma de pagamento: " . addslashes($stmt->error) . "');</script>";
                }

                // Fecha a declaração
                $stmt->close();
                return;
            } else {
                echo "<script>alert('Os campos Nome Forma de pagamento e Observação são obrigatórios.');</script>";
                return;
            }
        }
    }

    public function updateFormaPagamento()
    {
        // Verifica se os dados foram enviados via POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Verifica se os campos necessários estão presentes
            if (isset($_POST['id_forma_pagamento']) && isset($_POST['nome_forma_pagamento']) && isset($_POST['observacao'])) {

                // Atribui os valores do POST
                $id_forma_pagamento = $_POST['id_forma_pagamento'];  
                $nome_forma_pagamento = $_POST['nome_forma_pagamento'];
                $observacao = $_POST['observacao'];

                // Verifica se já existe uma forma de pagamento com o mesmo nome (exceto o registro atual)
                $check_query = "SELECT COUNT(*) as count FROM tbl_formas_pagamento WHERE nome_forma_pagamento = ? AND id != ?";
                $check_stmt = $this->mysqlConnection->prepare($check_query);
                
                if (!$check_stmt) {
                    echo "<script>alert('Erro na preparação da consulta MySQL: " . addslashes($this->mysqlConnection->error) . "');</script>";
                    return;
                }

                $check_stmt->bind_param("si", $nome_forma_pagamento, $id_forma_pagamento);
                $check_stmt->execute();
                $result = $check_stmt->get_result();
                $row = $result->fetch_assoc();
                
                if ($row['count'] > 0) {
                    echo "<script>alert('Já existe uma forma de pagamento com este nome.');</script>";
                    return;
                }
                
                $check_stmt->close();

                // Prepara a consulta SQL de atualização
                $query = "UPDATE tbl_formas_pagamento SET nome_forma_pagamento = ?, observacao = ? WHERE id = ?";
                $stmt = $this->mysqlConnection->prepare($query);

                if (!$stmt) {
                    echo "<script>alert('Erro na preparação da consulta MySQL: " . addslashes($this->mysqlConnection->error) . "');</script>";
                    return;
                }

                // Vincula os parâmetros
                $stmt->bind_param("ssi", $nome_forma_pagamento, $observacao, $id_forma_pagamento);

                // Executa a consulta
                if ($stmt->execute()) {
                    echo "<script>
                        alert('Forma de pagamento atualizada com sucesso!');
                        window.location.href = 'index.php';
                    </script>";
                } else {
                    echo "<script>alert('Erro ao atualizar forma de pagamento: " . addslashes($stmt->error) . "');</script>";
                }

                // Fecha a declaração
                $stmt->close();
                return;
            } else {
                echo "<script>alert('Os campos Nome Forma de pagamento e Observação são obrigatórios.');</script>";
                return;
            }
        }
    }

    public function deletaFormasPagamento($mysqlConnection)
    {
        // Verifica se o ID foi enviado via GET
        if (isset($_GET['id'])) {

            // Escapa o valor do GET
            $id_forma_pagamento = $mysqlConnection->real_escape_string($_GET['id']);

            // Prepara a consulta SQL de exclusão
            $query = "DELETE FROM tbl_formas_pagamento WHERE id = ?";

            // Prepara a declaração
            $stmt = $mysqlConnection->prepare($query);
            if (!$stmt) {
                die('Erro na preparação da consulta MySQL: ' . $mysqlConnection->error);
            }

            // Vincula o parâmetro
            $stmt->bind_param("i", $id_forma_pagamento);

            // Executa a consulta
            if ($stmt->execute()) {
                // Mensagem de sucesso
                echo "<script>alert('Forma de pagamento removida com sucesso!');</script>";

                // Redireciona para recarregar a página sem o parâmetro de ID
                echo "<script>window.location.href = window.location.pathname;</script>";
            } else {
                echo "Erro ao remover Forma de pagamento: " . $stmt->error;
            }

            // Fecha a declaração
            $stmt->close();
        }
    }
}
