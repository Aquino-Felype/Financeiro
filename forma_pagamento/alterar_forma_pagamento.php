<?php
require '../../conexao/conexao.php';
require '../../sessao.php';
require '../../conexao/acesso.php';
require '../../configuacoes.php';
require '../querys_financeiro/forma-pagamento.php';

$formas_pagamento = new FormasPagamento($connectionmysqlfinanceiro);

// Verifica se foi passado um ID válido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = 'ID da forma de pagamento não fornecido';
    header('Location: index.php');
    exit();
}

// Busca os dados da forma de pagamento
$id = $_GET['id'];
$forma_pagamento_data = $formas_pagamento->listarFormasPagamento($id);

if (empty($forma_pagamento_data)) {
    $_SESSION['message'] = 'Forma de pagamento não encontrada';
    header('Location: index.php');
    exit();
}

$forma_pagamento_atual = $forma_pagamento_data[0];

// Processa o update
$formas_pagamento->updateFormaPagamento();

if (isset($_SESSION['message'])) {
    echo "<script>alert('" . $_SESSION['message'] . "');</script>";
    unset($_SESSION['message']); // Limpa a mensagem após exibição
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<?php
$pageTitle = "Alterar Forma de Pagamento";
?>

<head>
    <link rel="icon" href="/financeiromandareceita/logo-sm.png">
    <link rel="shortcut icon" href="/financeiromandareceita/logo-sm.png">
</head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">

<body>
    <!-- Start Page Content here -->
    <div class="container-fluid">
        <div class="row">
            <?php
            include('../lateral.php');
            ?>

            <main class="col-md-9 col-lg-10 content">
                <div class="wrapper candidato">
                    <div class="container-fluid">
                        <div class="card-box mt-3">
                            <div class="row">
                                <div class="col-6 text-left">
                                    <h4 class="page-title">Alterar forma de pagamento</h4>
                                </div>

                                <div class="col-6 text-right">
                                    <button onclick="history.back()" class="btn btn-light"><i class="mdi mdi-keyboard-backspace"></i> Voltar</button>
                                </div>

                                <div class="col-12">
                                    <hr>
                                </div>

                                <div class="col-12">
                                    <form method="post">
                                        <input type="hidden" name="id_forma_pagamento" value="<?php echo $forma_pagamento_atual['id']; ?>">
                                        
                                        <div class="form-row mb-3">
                                            <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th width="97">Nome Forma de pagamento</th>
                                                        <th width="89" nowrap="nowrap">Observação</th>
                                                    </tr>
                                                    <tr class="text-center">
                                                        <th>
                                                            <input type="text" name="nome_forma_pagamento" id="nome_forma_pagamento" 
                                                                   class="form-control" value="<?php echo $forma_pagamento_atual['nome_forma_pagamento']; ?>">
                                                        </th>
                                                        <th>
                                                            <input type="text" class="form-control" name="observacao" id="observacao" 
                                                                   value="<?php echo $forma_pagamento_atual['observacao']; ?>">
                                                        </th>
                                                    </tr>
                                                </thead>
                                            </table>

                                            <div class="col-12 text-right">
                                                <button type="submit" class="btn btn-primary"><i class="mdi mdi-content-save"></i> Salvar Alterações</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- end container -->
            </main>
        </div>
    </div>

    <!-- Vendor js -->
    <script src="../assets/js/vendor.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
    <script src="../assets/libs/bootstrap-select/bootstrap-select.min.js"></script>
    <!-- App js -->
    <script src="../assets/js/app.min.js"></script>
</body>
</html>
