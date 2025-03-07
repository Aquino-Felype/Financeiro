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

$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'R';

// Processa o formulário quando enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        error_log('POST recebido: ' . print_r($_POST, true));

        $nome = $_POST['nome'] ?? '';
        $valor = $_POST['valor'] ?? '';
        $data = $_POST['data'] ?? '';
        $tipo = $_POST['tipo'] ?? '';
        $id_forma_pagamento = $_POST['id_forma_pagamento'] ?? '';
        $id_categoria = $_POST['id_categoria'] ?? '';
        $id_subcategoria = !empty($_POST['id_subcategoria']) ? $_POST['id_subcategoria'] : null;
        $id_conta = $_POST['id_conta'] ?? '';
        $n_parcelas = $_POST['n_parcelas'] ?? '';
        $desconto = $_POST['desconto'] ?? '';
        $pago = 'N';

        if (empty($nome) || empty($valor) || empty($data) || empty($tipo) || empty($id_forma_pagamento) || empty($id_categoria) || empty($id_conta)) {
            throw new Exception('Todos os campos obrigatórios devem ser preenchidos.');
        }

        $resultado = $movimentoCaixa->addMovimento(
            $nome,
            $valor,
            $data,
            $tipo,
            $id_forma_pagamento,
            $id_categoria,
            $id_subcategoria,
            $id_conta,
            $n_parcelas, 
            $desconto,
            $pago
        );

        if ($resultado === true) {
            $_SESSION['message'] = 'Movimento adicionado com sucesso!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?tipo=" . $tipo);
            exit();
        } else {
            throw new Exception('Erro ao adicionar movimento.');
        }
    } catch (Exception $e) {
        $_SESSION['message'] = $e->getMessage();
        $_SESSION['message_type'] = 'error';
    }
}

// Lista as formas de pagamento e categorias para o formulário
$lista_formas_pagamento = $formasPagamento->listarFormasPagamento();
$lista_categorias = $categorias->listarCategorias('', "AND tipo = '$tipo'");
$lista_contas = $contas->listarContas('', "AND ativo = 1");

// Exibe mensagem de erro se existir
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

$pageTitle = "Adicionar " . ($tipo == 'R' ? 'Recebimento' : 'Pagamento');
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
                                        Novo <?php echo $tipo == 'R' ? 'Recebimento' : 'Pagamento'; ?>
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
                                        <input type="hidden" name="tipo" value="<?php echo htmlspecialchars($tipo); ?>">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nome">Nome/Descrição</label>
                                                    <input type="text" class="form-control" id="nome" name="nome" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="valor">Valor</label>
                                                    <input type="text" class="form-control money" id="valor" name="valor" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="desconto">Desconto</label>
                                                    <input type="text" class="form-control money" id="desconto" name="desconto" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="data">Data</label>
                                                    <input type="date" class="form-control" id="data" name="data" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="id_conta">Conta</label>
                                                    <select class="form-control" id="id_conta" name="id_conta" required>
                                                        <option value="">Selecione uma conta</option>
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
                                                    <label for="id_forma_pagamento">Forma de Pagamento</label>
                                                    <select class="form-control" name="id_forma_pagamento" id="id_forma_pagamento" required>
                                                        <option value="">Selecione</option>
                                                        <?php foreach ($lista_formas_pagamento as $forma): ?>
                                                            <option value="<?php echo $forma['id']; ?>">
                                                                <?php echo $forma['nome_forma_pagamento']; ?>
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
                                                            echo "<option value='$i'>" . ($i == 1 ? "À vista" : "$i Parcelas") . "</option>";
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
                                                            <option value="<?php echo $categoria['id']; ?>">
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
                                                    </select>
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
        $(document).ready(function() {
            // Máscara para valor monetário
            $('.money').mask('#.##0,00', {
                reverse: true
            });
        });
    </script>

    <script>
        document.getElementById('id_categoria').addEventListener('change', function() {
            var categoriaId = this.value;
            var subcategoriaSelect = document.getElementById('id_subcategoria');

            // Limpa as opções atuais
            subcategoriaSelect.innerHTML = '<option value="">Carregando...</option>';

            if (categoriaId) {
                // Faz a requisição AJAX
                fetch('get_subcategorias.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'id_categoria=' + categoriaId
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Limpa o select
                        subcategoriaSelect.innerHTML = '<option value="">Selecione uma subcategoria</option>';

                        // Adiciona as novas opções
                        data.forEach(function(subcategoria) {
                            var option = document.createElement('option');
                            option.value = subcategoria.id;
                            option.textContent = subcategoria.nome_subcategoria;
                            subcategoriaSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        subcategoriaSelect.innerHTML = '<option value="">Erro ao carregar subcategorias</option>';
                    });
            } else {
                subcategoriaSelect.innerHTML = '<option value="">Selecione uma categoria primeiro</option>';
            }
        });
    </script>
</body>

</html>