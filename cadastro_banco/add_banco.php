<?php
require '../../conexao/conexao.php';
require '../../conexao/acesso.php';
require '../../sessao.php';
require '../../configuacoes.php';
require '../querys_financeiro/contas.php';

$contas = new Contas($connectionmysqlfinanceiro);
$contas->addNovaConta();

if (isset($_SESSION['message'])) {
    echo "<script>alert('" . $_SESSION['message'] . "');</script>";
    unset($_SESSION['message']); // Limpa a mensagem após exibição
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<?php
$pageTitle = "Adicionar Nova Conta/Banco";
?>

<head>
    <link rel="icon" href="/financeiromandareceita/logo-sm.png">
    <link rel="shortcut icon" href="/financeiromandareceita/logo-sm.png">
</head>

<body>
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
                                    <h4 class="page-title">Adicionar Nova Conta/Banco</h4>
                                </div>
                                <div class="col-6 text-right">
                                    <button onclick="history.back()" class="btn btn-light">
                                        <i class="mdi mdi-keyboard-backspace"></i> Voltar
                                    </button>
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
                                                        <th>Nome da Conta</th>
                                                        <th>Agência</th>
                                                        <th>Limite Inicial</th>
                                                        <th>Status</th>
                                                    </tr>
                                                    <tr class="text-center">
                                                        <th>
                                                            <input type="text" name="nome_conta" id="nome_conta" class="form-control" required>
                                                        </th>
                                                        <th>
                                                            <input type="text" name="agencia" id="agencia" class="form-control">
                                                        </th>
                                                        <th>
                                                            <input type="text" name="limite_inicial" id="limite_inicial" class="form-control money" value="0,00">
                                                        </th>
                                                        <th>
                                                            <select name="ativo" id="ativo" class="form-control">
                                                                <option value="S">Ativo</option>
                                                                <option value="N">Inativo</option>
                                                            </select>
                                                        </th>
                                                    </tr>
                                                </thead>
                                            </table>

                                            <div class="col-12 text-right">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="mdi mdi-plus"></i> Inserir Dados
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Vendor js -->
    <script src="../assets/js/vendor.min.js"></script>
    <!-- App js -->
    <script src="../assets/js/app.min.js"></script>
    
    <!-- Adiciona máscara de moeda -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.money').mask('#.##0,00', {reverse: true});
        });
    </script>
</body>

</html>