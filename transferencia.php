<?php
require '../../conexao/conexao.php';
require '../../conexao/acesso.php';
require '../../configuacoes.php';
require '../querys_financeiro/transferencias.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transferencias = new Transferencias($connectionmysqlfinanceiro);

    try {
        // Pega os dados do POST
        $id_conta_banco_origem = $_POST['id_conta_banco_origem'] ?? '';
        $id_conta_banco_destino = $_POST['id_conta_banco_destino'] ?? '';
        $valor = $_POST['valor'] ?? '';
        $data = $_POST['data'] ?? date('Y-m-d');

        // Validações básicas
        if (empty($id_conta_banco_origem) || empty($id_conta_banco_destino) || empty($valor)) {
            throw new Exception("Todos os campos são obrigatórios");
        }

        // Converte IDs para inteiros para garantir comparação correta
        $id_conta_banco_origem = (int)$id_conta_banco_origem;
        $id_conta_banco_destino = (int)$id_conta_banco_destino;

        if ($id_conta_banco_origem === $id_conta_banco_destino) {
            throw new Exception("A conta de origem e destino não podem ser a mesma");
        }

        if (!is_numeric($valor) || $valor <= 0) {
            throw new Exception("O valor deve ser um número positivo");
        }

        // Realiza a transferência
        if ($transferencias->addNovaTransferencia($id_conta_banco_origem, $id_conta_banco_destino, $valor, $data)) {
            $_SESSION['message'] = 'Transferência realizada com sucesso!';
            $_SESSION['message_type'] = 'success';
            
            // Exibe a mensagem antes do redirecionamento
            echo "<script>
                alert('" . $_SESSION['message'] . "');
                window.location.href = 'index.php';
            </script>";
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['message'] = 'Erro: ' . $e->getMessage();
        $_SESSION['message_type'] = 'error';
        
        // Exibe a mensagem de erro antes do redirecionamento
        echo "<script>
            alert('" . $_SESSION['message'] . "');
            window.location.href = 'index.php';
        </script>";
        exit();
    }

    // Redireciona de volta (só será executado se algo der errado com o JavaScript)
    header('Location: index.php');
    exit();
}
