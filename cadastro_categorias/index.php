<?php
require '../../conexao/conexao.php';
require '../../conexao/acesso.php';
require '../../configuacoes.php';
require '../querys_financeiro/categorias.php';

$categorias = new Categorias($connectionmysqlfinanceiro);
$lista_categorias = $categorias->listarCategorias();

$categorias->deletaCategoria();

if (isset($_SESSION['message'])) {
    echo "<script>alert('" . $_SESSION['message'] . "');</script>";
    unset($_SESSION['message']); // Limpa a mensagem após exibição
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<?php
$pageTitle = "Lista de Categorias";
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
                                    <h4 class="page-title">Lista de Categorias</h4>
                                </div>
                                <div class="col-6 text-right">
                                    <a href="add_categoria.php" class="btn btn-primary">
                                        <i class="mdi mdi-plus"></i> Nova Categoria
                                    </a>
                                </div>

                                <div class="col-12">
                                    <hr>
                                </div>

                                <div class="col-12">
                                    <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap">
                                        <thead>
                                            <tr class="text-center">
                                                <th>Nome da Categoria</th>
                                                <th>Tipo</th>
                                                <th>Operacional</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($lista_categorias as $categoria) : ?>
                                                <tr>
                                                    <td width="40%"><?php echo $categoria['nome']; ?></td>
                                                    <td width="20%"><?php echo $categoria['tipo'] == 'R' ? 'Receita' : 'Despesa'; ?></td>
                                                    <td width="20%"><?php echo $categoria['operacional'] == 'S' ? 'Sim' : 'Não'; ?></td>
                                                    <td width="20%" class="text-center">
                                                        <a href="alteracao_categoria.php?id=<?php echo $categoria['id']; ?>" 
                                                           class="table-actions link-view" title="Alterar Categoria">
                                                            <i class="mdi mdi-pencil"></i>
                                                        </a>
                                                        <button onclick="window.location.href='index.php?id=<?php echo $categoria['id']; ?>';" 
                                                                type="button" class="table-actions link-view excluir-btn" 
                                                                title="Excluir Categoria">
                                                            <i class="mdi mdi-delete-forever"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
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
</body>
</html>
