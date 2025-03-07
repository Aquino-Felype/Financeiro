<?php
require '../../conexao/conexao.php';
require '../../sessao.php';
require '../../configuacoes.php';

require '../querys_financeiro/contas.php';

$contas = new Contas($connectionmysqlfinanceiro);

$lista_contas = $contas->listarContas();

$contas->deletaConta();

if (isset($_SESSION['message'])) {
    echo "<script>alert('" . $_SESSION['message'] . "');</script>";
    unset($_SESSION['message']); // Limpa a mensagem após exibição
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <link rel="icon" href="/mandareceita/pages/configuracoes/logo-sm.png">
    <link rel="shortcut icon" href="/mandareceita/pages/configuracoes/logo-sm.png">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->

    <!--Material Icon -->
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/5.4.55/css/materialdesignicons.min.css">
</head>

<?php $pageTitle = 'Configuração de Metas - Manda Receita'; ?>

<body>

    <div class="container-fluid">
        <div class="row">
            <!-- Menu Lateral -->
            <?php include('../lateral.php'); ?>

            <!-- Conteúdo Principal -->
            <main class="col-md-9 col-lg-10 content">
                <div class="wrapper candidato">
                    <div class="container-fluid">
                        <div class="card-box mt-3">
                            <div class="row">
                                <div class="col-6 text-left mb-4">
                                    <h4 class="page-title">Lista Contas/Bancos</h4>

                                </div>
                                <div class="col-6 text-right">

                                    <a href="add_banco.php" class="btn btn-light" alt="Incluir Filiais" title="Incluir Filiais"><i class="mdi mdi-plus"></i> Adicionar Conta/Banco</a>


                                </div>
                                <div class="col-12" style="margin-top: -15px;">
                                    <hr>
                                </div>
                            </div>
                            <div class="col-12">
                                <table class="table table-striped table-bordered dt-responsive nowrap">
                                    <tbody>
                                        <tr>
                                            <th valign="middle" nowrap="nowrap">Nome da Conta</th>
                                            <th valign="middle" nowrap="nowrap">Agência</th>
                                            <th valign="middle" nowrap="nowrap">Saldo</th>
                                            <th valign="middle" nowrap="nowrap">Status</th>
                                            <th valign="middle" nowrap="nowrap" style="text-align: center">Ações</th>
                                        </tr>

                                        <?php foreach ($lista_contas as $conta) { ?>
                                            <tr>
                                                <td width="25%"><?php echo $conta['nome_conta']; ?></td>
                                                <td width="25%"><?php echo $conta['agencia']; ?></td>
                                                <td width="25%"><?php echo 'R$ ' . number_format($conta['limite_inicial'], 2, ',', '.'); ?></td>
                                                <td width="25%"><?php echo $conta['ativo'] == 'S' ? 'Ativo' : 'Inativo'; ?></td>
                                                <td width="25%" align="center" nowrap="nowrap">

                                                    <a href="alteracao_banco.php?id=<?php echo $conta['id']; ?>" class="table-actions link-view" title="Alterar Conta/Banco"><i class="mdi mdi mdi-account-edit"></i></a>

                                                    <button onclick="confirmarExclusao(<?php echo $conta['id']; ?>)" type="button" class="table-actions link-view excluir-btn" title="Excluir Conta/Banco">
                                                        <i class="mdi mdi-delete-forever"></i>
                                                    </button>

                                                </td>
                                            </tr>
                                        <?php } ?>

                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        function confirmarExclusao(id) {
            if (confirm('Tem certeza que deseja excluir esta conta?')) {
                window.location.href = 'index.php?id=' + id + '&action=delete';
            }
        }
    </script>

</body>

</html>