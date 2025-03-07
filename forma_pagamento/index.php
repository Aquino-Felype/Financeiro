<?php
require '../../conexao/conexao.php';
require '../../sessao.php';
require '../../configuacoes.php';

require '../querys_financeiro/forma-pagamento.php';

$formas_pagamento = new FormasPagamento($connectionmysqlfinanceiro);
$sql = '';

if (isset($_GET['formas_pagamento'])) {
    $sql .= " AND nome_forma_pagamento LIKE '%" . $_GET['formas_pagamento'] . "%'";
}

$listarFormasPagamento = $formas_pagamento->listarFormasPagamento('', $sql);

$formas_pagamento->deletaFormasPagamento($connectionmysqlfinanceiro);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <link rel="icon" href="/financeiromandareceita/logo-sm.png">
    <link rel="shortcut icon" href="/financeiromandareceita/logo-sm.png">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->

    <!--Material Icon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <!--Material Icon -->
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/5.4.55/css/materialdesignicons.min.css">
</head>

<?php $pageTitle = 'Forma de Pagamento - Financeiro da Manda Receita'; ?>

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
                                    <h4 class="page-title">Lista Formas de pagamento</h4>

                                </div>
                                <div class="col-6 text-right">

                                    <a href="add_forma_pagamento.php" class="btn btn-light" alt="Incluir Filiais" title="Incluir Filiais"><i class="mdi mdi-plus"></i> Adicionar Forma de Pagamento</a>
                                </div>

                                <div class="col-12" style="margin-top: -25px;">
                                    <hr>
                                </div>

                                <form method="get" action="index.php">
                                    <div class="col-12">
                                        <div class="form-row mb-12">
                                            <div class="form-group col-md-7">
                                                <label class="col-form-label">Formas de Pagamento</label>
                                                <input type="text" class="form-control" name="formas_pagamento" value="<?php echo isset($_GET['formas_pagamento']) ? htmlspecialchars($_GET['formas_pagamento']) : ''; ?>" id="formas_pagamento">
                                            </div>

                                            <div class="col-5 text-right" style="margin-top: 35px;">
                                                <button type="submit" class="btn btn-primary"><i class="mdi mdi-magnify"></i> Pesquisar</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                            </div>




                            <div class="col-12">
                                <table class="table table-striped table-bordered dt-responsive nowrap">
                                    <tbody>
                                        <tr>
                                            <th valign="middle" nowrap="nowrap">Id / Nome </th>
                                            <th valign="middle" nowrap="nowrap">Observação</th>
                                            <th valign="middle" nowrap="nowrap" style="text-align: center">Ações</th>
                                        </tr>

                                        <?php foreach ($listarFormasPagamento as $forma_pagamento) { ?>
                                            <tr>
                                                <td width="45%"><?php echo htmlspecialchars($forma_pagamento['id'] . ' - ' . $forma_pagamento['nome_forma_pagamento']); ?></td>
                                                <td width="45%"><?php echo htmlspecialchars($forma_pagamento['observacao']); ?></td>
                                                <td width="10%" align="center" nowrap="nowrap">

                                                    <a href="alterar_forma_pagamento.php?id=<?php echo $forma_pagamento['id']; ?>" class="table-actions link-view" title="Alterar Filiais"><i class="mdi mdi mdi-account-edit"></i></a>

                                                    <button onclick="window.location.href='index.php?id=<?php echo $forma_pagamento['id']; ?>';" type="button" class="table-actions link-view excluir-btn" title="Excluir Meta">
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

</body>

</html>