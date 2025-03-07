<?php
require '../../conexao/conexao.php';
require '../../sessao.php';
require '../../conexao/acesso.php';
require '../../configuacoes.php';

require '../querys_financeiro/forma-pagamento.php';

$formas_pagamento = new FormasPagamento($connectionmysqlfinanceiro);

// Process form submission
$formas_pagamento->addNovaFormaPagamento();

// Check for messages and display them only if they haven't been displayed yet
if (isset($_SESSION['error_message']) && !isset($_SESSION['message_displayed'])) {
    echo "<script>alert('" . htmlspecialchars($_SESSION['error_message'], ENT_QUOTES) . "');</script>";
    $_SESSION['message_displayed'] = true;
    unset($_SESSION['error_message']);
} else if (isset($_SESSION['success_message']) && !isset($_SESSION['message_displayed'])) {
    echo "<script>
        alert('" . htmlspecialchars($_SESSION['success_message'], ENT_QUOTES) . "');
        window.location.href = 'index.php';
    </script>";
    $_SESSION['message_displayed'] = true;
    unset($_SESSION['success_message']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<?php
$pageTitle = "Cadastrar Forma de Pagamento"; // Definir o título da página aqui
?>

<head>
    <link rel="icon" href="/financeiromandareceita/logo-sm.png">
    <link rel="shortcut icon" href="/financeiromandareceita/logo-sm.png">

</head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">


<body>


    <!-- ============================================================== -->
    <!-- Start Page Content here -->
    <!-- ============================================================== -->
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
                                    <h4 class="page-title">Cadastrar nova forma de pagamento</h4>
                                </div>

                                <div class="col-6 text-right">
                                    <button onclick="history.back()" class="btn btn-light"><i class="mdi mdi-keyboard-backspace"></i> Voltar</button>
                                </div>

                                <div class="col-12">
                                    <hr>
                                </div>

                                <div class="col-12">
                                    <form method="post">
                                        <div class="form-row mb-3">

                                            <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th width="97">Nome Forma de pagamento</th>
                                                        <th width="89" nowrap="nowrap">Observação </th>
                                                        
                                                    </tr>
                                                    <tr class="text-center">
                                                        <th>
                                                            <input type="text" name="nome_forma_pagamento" required id="nome_forma_pagamento" class="form-control">
                                                        </th>
                                                        <th><input type="text" class="form-control" required name="observacao" id="observacao"></th>
                                                        
                                                    </tr>
                                                </thead>
                                            </table>

                                            <div class="col-12 text-right">
                                                <!--  <a href="index.php" class="btn btn-warning mr-2"> Limpar</a> -->
                                                <button type="submit" class="btn btn-primary"><i class="mdi mdi-plus"></i> Inserir Dados</button>
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

    <!-- end wrapper -->

    <!-- ============================================================== -->
    <!-- End Page content -->
    <!-- ============================================================== -->

    <!-- jQuery (precisa para o Chosen) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Chosen JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
    <!-- Inicializar Chosen -->
    <script>
        var $j = jQuery.noConflict();
        $j(document).ready(function() {
            $j('#id_cidade').chosen();
        });
    </script>


    <!-- Vendor js -->
    <script src="../../../assets/js/vendor.min.js"></script>

    <!-- knob plugin -->
    <script src="../../../assets/libs/jquery-knob/jquery.knob.min.js"></script>
    <!-- App js-->
    <script src="../../../assets/js/app.min.js"></script>

    <script src="../../geral/javascript.js"></script>


</body>

</html>