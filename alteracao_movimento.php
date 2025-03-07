<?php
require '../../conexao/conexao.php';
require '../../conexao/acesso.php';
require '../../configuacoes.php';
require '../querys_financeiro/movimento_caixa.php';
require '../querys_financeiro/forma-pagamento.php';
require '../querys_financeiro/categorias.php';
require '../querys_financeiro/contas.php';

$movimentoCaixa = new MovimentoCaixa($connectionmysqlfinanceiro);
$formasPagamento = new FormasPagamento($connectionmysqlfinanceiro);
$categorias = new Categorias($connectionmysqlfinanceiro);
$contas = new Contas($connectionmysqlfinanceiro);

// Processa o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movimentoCaixa->updateMovimento();
}

$id = $_GET['id'];

$movimentos = $movimentoCaixa->listarMovimentos('', '', '','','','','','','','', $id);

$movimento = !empty($movimentos) ? $movimentos[0] : null;

$tipo = $movimento['tipo'];

// Lista as formas de pagamento e categorias para o formulário
$lista_formas_pagamento = $formasPagamento->listarFormasPagamento();
$lista_categorias = $categorias->listarCategorias('', "AND tipo = '$tipo'");
$lista_subcategorias = $categorias->listarSubcategorias($movimento['id_categoria'], $movimento['id_subcategoria']);
$lista_contas = $contas->listarContas('', "AND ativo = 1");

// Exibe mensagem de erro/sucesso se existir
if (isset($_SESSION['message'])) {
    $alertClass = $_SESSION['message_type'] == 'success' ? 'success' : 'danger';
    echo "<div class='alert alert-{$alertClass} alert-dismissible fade show' role='alert'>
            " . htmlspecialchars($_SESSION['message']) . "
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                <span aria-hidden='true'>&times;</span>
            </button>
          </div>";
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

if ($movimento['tipo'] == 'D') {
    $pageTitle = "Alterar Movimento Despesa";
} else {
    $pageTitle = "Alterar Movimento Recebimento";
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <link rel="icon" href="/financeiromandareceita/logo-sm.png">
    <link rel="shortcut icon" href="/financeiromandareceita/logo-sm.png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <style>
        .form-group {
            margin-bottom: 1rem;
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
                                        Editar <?php echo $tipo == 'R' ? 'Recebimento' : 'Pagamento'; ?>
                                    </h4>
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
                                    <form id="movimentoForm" method="post">
                                        <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
                                        <input type="hidden" name="tipo" value="<?php echo $tipo; ?>">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nome">Nome/Descrição</label>
                                                    <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($movimento['nome']); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="valor">Valor</label>
                                                    <input type="text" class="form-control money" id="valor" name="valor" value="<?php echo number_format($movimento['valor'], 2, ',', '.'); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="desconto">Desconto</label>
                                                    <input type="text" class="form-control money" id="desconto" name="desconto" required value="<?php echo number_format($movimento['desconto'], 2, ',', '.'); ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="data">Data</label>
                                                    <input type="date" class="form-control" id="data" name="data" value="<?php echo $movimento['data']; ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="id_conta">Conta</label>
                                                    <select class="form-control" id="id_conta" name="id_conta" required>
                                                        <option value="">Selecione uma conta</option>
                                                        <?php foreach ($lista_contas as $conta) : ?>
                                                            <option value="<?php echo $conta['id']; ?>" <?php echo $movimento['id_conta_banco'] == $conta['id'] ? 'selected' : ''; ?>>
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
                                                    <label for="id_forma_pagamento">Forma de Pagamento</label>
                                                    <select class="form-control" id="id_forma_pagamento" name="id_forma_pagamento" required>
                                                        <option value="">Selecione uma forma de pagamento</option>
                                                        <?php foreach ($lista_formas_pagamento as $forma) : ?>
                                                            <option value="<?php echo $forma['id']; ?>" <?php echo $movimento['id_forma_pagamento'] == $forma['id'] ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($forma['nome_forma_pagamento']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="n_parcelas">N° de Parcelas</label>
                                                    <select class="form-control" name="n_parcelas" id="n_parcelas" required>
                                                        <?php
                                                        for ($i = 1; $i <= 12; $i++) {
                                                            $selected = ($i == $movimento['n_parcela']) ? 'selected' : '';
                                                            echo "<option value='$i' $selected>" . ($i == 1 ? "À vista" : "$i Parcelas") . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="id_categoria">Categoria</label>
                                                    <select class="form-control" id="id_categoria" name="id_categoria" required>
                                                        <option value="">Selecione uma categoria</option>
                                                        <?php foreach ($lista_categorias as $categoria) : ?>
                                                            <option value="<?php echo $categoria['id']; ?>" <?php echo $movimento['id_categoria'] == $categoria['id'] ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($categoria['nome']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="id_subcategoria">Subcategoria</label>
                                                    <select class="form-control" id="id_subcategoria" name="id_subcategoria">
                                                        <option value="">Selecione uma categoria primeiro</option>
                                                        <?php foreach ($lista_subcategorias as $subcategoria) : ?>
                                                            <option value="<?php echo $subcategoria['id']; ?>" <?php echo $movimento['id_subcategoria'] == $subcategoria['id'] ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($subcategoria['nome_subcategoria']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12 text-right">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="mdi mdi-content-save"></i> Salvar Alterações
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
        $(document).ready(function() {
            // Máscara para valor monetário
            $('.money').mask('#.##0,00', {
                reverse: true
            });

            // Carregar subcategorias quando uma categoria é selecionada
            $('#id_categoria').change(function() {
                var categoriaId = $(this).val();
                var subcategoriaSelect = $('#id_subcategoria');

                subcategoriaSelect.html('<option value="">Carregando...</option>');

                if (categoriaId) {
                    $.ajax({
                        url: 'get_subcategorias.php',
                        type: 'POST',
                        data: {
                            id_categoria: categoriaId
                        },
                        success: function(data) {
                            var subcategorias = JSON.parse(data);
                            var options = '<option value="">Selecione</option>';

                            subcategorias.forEach(function(subcategoria) {
                                options += '<option value="' + subcategoria.id + '">' +
                                    subcategoria.nome_subcategoria + '</option>';
                            });

                            subcategoriaSelect.html(options);
                        },
                        error: function() {
                            subcategoriaSelect.html('<option value="">Erro ao carregar subcategorias</option>');
                        }
                    });
                } else {
                    subcategoriaSelect.html('<option value="">Selecione uma categoria primeiro</option>');
                }
            });
        });
    </script>
</body>

</html>