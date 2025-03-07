<?php
require '../../conexao/conexao.php';
require '../../conexao/acesso.php';
require '../../configuacoes.php';
require '../querys_financeiro/contas.php';
require '../querys_financeiro/transferencias.php';

$transferencias = new Transferencias($connectionmysqlfinanceiro);

$transferencias->addNovaTransferencia();

$contas = new Contas($connectionmysqlfinanceiro);

$lista_contas = $contas->listarContas('', "AND ativo = 1");

// Exibe mensagens do sistema
if (isset($_SESSION['message'])) {
    echo "<script>alert('" . htmlspecialchars($_SESSION['message']) . "');</script>";
    unset($_SESSION['message']);
}

$pageTitle = 'Transferências';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <link rel="icon" href="/financeiromandareceita/logo-sm.png">
    <link rel="shortcut icon" href="/financeiromandareceita/logo-sm.png">
    <style>
        .valor-cell {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php include('../lateral.php'); ?>

            <main class="col-md-9 col-lg-10 content">
                <div class="wrapper">
                    <div class="container-fluid">
                        <div class="card-box mt-3">
                            <div class="row">
                                <div class="col-6">
                                    <h4 class="page-title">
                                        Transferências
                                    </h4>
                                </div>

                                <div class="col-12">
                                    <hr>
                                </div>

                                <div class="col-12">
                                    <form id="movimentoForm" method="post">


                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="id_conta">Conta Origem</label>
                                                    <select class="form-control" id="id_conta_origem" name="id_conta_banco_origem" required>
                                                        <option value="">Selecione uma conta origem</option>
                                                        <?php foreach ($lista_contas as $conta) : ?>
                                                            <option value="<?php echo $conta['id']; ?>">
                                                                <?php echo htmlspecialchars($conta['nome_conta']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="id_conta">Conta Destino</label>
                                                    <select class="form-control" id="id_conta_banco_destino" name="id_conta_banco_destino" required>
                                                        <option value="">Selecione uma conta destino</option>
                                                        <?php foreach ($lista_contas as $conta) : ?>
                                                            <option value="<?php echo $conta['id']; ?>">
                                                                <?php echo htmlspecialchars($conta['nome_conta']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="valor">Valor</label>
                                                    <input type="text" class="form-control money" id="valor" name="valor" required
                                                        data-thousands="."
                                                        data-decimal=","
                                                        data-precision="2"
                                                        oninput="this.value = this.value.replace(/[^0-9.,]/g, '')">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="data">Data</label>
                                                    <input type="date" class="form-control" id="data" name="data" value="<?php echo date('Y-m-d'); ?>" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12 text-right">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="mdi mdi-plus"></i> Salvar
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

    <script>
        function confirmarExclusao(id, tipo) {
            if (confirm('Tem certeza que deseja excluir este registro?')) {
                window.location.href = 'delete_movimento.php?id=' + id + '&tipo=' + tipo;
            }
        }
    </script>

    <script>
        // Inicializa a máscara de valor monetário
        $(document).ready(function() {
            $('.money').mask('#.##0,00', {
                reverse: true,
                placeholder: "0,00"
            });
        });
    </script>
</body>

</html>