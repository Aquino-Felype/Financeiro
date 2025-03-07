<?php
require '../../conexao/conexao.php';
require '../../conexao/acesso.php';
require '../../configuacoes.php';
require '../querys_financeiro/movimento_caixa.php';
require '../querys_financeiro/forma-pagamento.php';
require '../querys_financeiro/categorias.php';
require '../querys_financeiro/contas.php';

$movimentoCaixa = new MovimentoCaixa($connectionmysqlfinanceiro);
$formas_pagamento = new FormasPagamento($connectionmysqlfinanceiro);
$categorias = new Categorias($connectionmysqlfinanceiro);
$contas = new Contas($connectionmysqlfinanceiro);

// Obtém os parâmetros de filtro
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'D';
$mes = isset($_GET['mes']) ? $_GET['mes'] : date('m');
$ano = isset($_GET['ano']) ? $_GET['ano'] : date('Y');
$data_inicial = isset($_GET['data_inicial']) ? $_GET['data_inicial'] : '';
$data_final = isset($_GET['data_final']) ? $_GET['data_final'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$id_conta = isset($_GET['id_conta']) ? $_GET['id_conta'] : '';
$id_forma_pagamento = isset($_GET['id_forma_pagamento']) ? $_GET['id_forma_pagamento'] : '';
$id_categoria = isset($_GET['id_categoria']) ? $_GET['id_categoria'] : '';
$id_subcategoria = isset($_GET['id_subcategoria']) ? $_GET['id_subcategoria'] : '';

// Carrega as listas para os filtros
$lista_formas_pagamento = $formas_pagamento->listarFormasPagamento();
$lista_categorias = $categorias->listarCategorias('', "AND tipo = '$tipo'");
$lista_subcategorias = $categorias->listarSubcategorias($id_categoria);
$lista_contas = $contas->listarContas();

// Lista os movimentos com filtros
$movimentoCaixa->deletaMovimento();

$movimentos = $movimentoCaixa->listarMovimentos($tipo, $mes, $ano, $data_inicial, $data_final, $status, $id_conta, $id_forma_pagamento, $id_categoria, $id_subcategoria);

// Processa o pagamento
if (isset($_GET['pagar']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $valor_pago = str_replace(['.', ','], ['', '.'], $_GET['valor_pago']);
    $juros = str_replace(['.', ','], ['', '.'], $_GET['juros']);
    $data_pagamento = $_GET['data_pagamento'];

    try {
        if ($movimentoCaixa->atualizarPagamento($id, $valor_pago, $juros, $data_pagamento)) {
            $_SESSION['message'] = 'Pagamento confirmado com sucesso!';
        } else {
            $_SESSION['message'] = 'Erro ao confirmar o pagamento.';
        }
    } catch (Exception $e) {
        $_SESSION['message'] = 'Erro ao confirmar o pagamento: ' . $e->getMessage();
    }
    
    // Redireciona mantendo todos os filtros
    $params = [
        'tipo' => $tipo,
        'mes' => $mes,
        'ano' => $ano,
        'status' => $status,
        'id_conta' => $id_conta,
        'id_forma_pagamento' => $id_forma_pagamento,
        'id_categoria' => $id_categoria,
        'id_subcategoria' => $id_subcategoria
    ];

    // Remove parâmetros vazios para manter a URL limpa
    $params = array_filter($params, function($value) {
        return $value !== '';
    });

    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?') . '?' . http_build_query($params));
    exit;
}

// Processa a exclusão do movimento
if (isset($_GET['excluir']) && isset($_GET['id'])) {
    try {
        if ($movimentoCaixa->deletaMovimento($_GET['id'])) {
            $_SESSION['message'] = 'Movimento excluído com sucesso!';
        } else {
            $_SESSION['message'] = 'Erro ao excluir o movimento.';
        }
    } catch (Exception $e) {
        $_SESSION['message'] = 'Erro ao excluir o movimento: ' . $e->getMessage();
    }
    
    // Redireciona para limpar os parâmetros da URL
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?') . '?' . http_build_query([
        'tipo' => $tipo,
        'mes' => $mes,
        'ano' => $ano,
        'status' => $status,
        'id_conta' => $id_conta,
        'id_forma_pagamento' => $id_forma_pagamento,
        'id_categoria' => $id_categoria,
        'id_subcategoria' => $id_subcategoria
    ]));
    exit;
}

if (isset($_SESSION['message'])) {
    echo "<script>alert('" . $_SESSION['message'] . "');</script>";
    unset($_SESSION['message']);
}

$pageTitle = 'Financeiro';
$meses = [
    '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
    '04' => 'Abril', '05' => 'Maio', '06' => 'Junho',
    '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro',
    '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="icon" href="/financeiromandareceita/logo-sm.png">
    <link rel="shortcut icon" href="/financeiromandareceita/logo-sm.png">
    <style>
        .valor-cell { text-align: right; }
        
        /* Cabeçalho da página */
        .page-header {
            background-color: #fff;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        
        /* Navegação por mês */
        .month-nav {
            background-color: #fff;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        .month-nav .btn {
            margin: 0 2px;
            padding: 0.4rem 0.8rem;
            font-size: 0.9rem;
        }
        .month-nav .btn.active {
            background-color: #2196F3;
            color: white;
            border-color: #2196F3;
        }
        .year-selector {
            background-color: #f8f9fa;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
        }
        .year-selector span {
            font-weight: bold;
            min-width: 60px;
            text-align: center;
            color: #000;
        }
        .year-selector .btn-outline-secondary {
            color: #000;
            border-color: #000;
        }
        .year-selector .btn-outline-secondary:hover {
            background-color: #000;
            color: #fff;
            border-color: #000;
        }
        
        /* Seção de filtros */
        .filters-section {
            background-color: #fff;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        .filters-section label {
            font-weight: 500;
            color: #555;
            margin-bottom: 0.5rem;
        }
        .filters-section .form-control {
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .filters-section .form-control:focus {
            border-color: #2196F3;
            box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.25);
        }
        
        /* Tabela de dados */
        .table-container {
            background-color: #fff;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            color: #495057;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0,0,0,.02);
        }
        
        /* Botões */
        .btn-primary {
            background-color: #2196F3;
            border-color: #2196F3;
        }
        .btn-primary:hover {
            background-color: #1976D2;
            border-color: #1976D2;
        }
        .btn-outline-primary {
            color: #2196F3;
            border-color: #2196F3;
        }
        .btn-outline-primary:hover {
            background-color: #2196F3;
            border-color: #2196F3;
        }
        .btn-warning {
            color: #fff;
            background-color: #FF9800;
            border-color: #FF9800;
        }
        .btn-warning:hover {
            background-color: #F57C00;
            border-color: #F57C00;
        }
        
        /* Responsividade */
        @media (max-width: 768px) {
            .month-nav .btn {
                padding: 0.3rem 0.6rem;
                font-size: 0.8rem;
            }
            .filters-section {
                padding: 1rem;
            }
        }
    </style>
</head>

<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <?php include('../lateral.php'); ?>

            <main class="col-md-9 col-lg-10 px-4 py-3">
                <!-- Cabeçalho da Página -->
                <div class="page-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">
                            <?php echo $tipo == 'R' ? 'Contas a Receber' : 'Contas a Pagar'; ?>
                        </h4>
                        <small class="text-muted">Gerenciamento de <?php echo $tipo == 'R' ? 'recebimentos' : 'pagamentos'; ?></small>
                    </div>
                    <div>
                        <a href="add_movimento.php?tipo=<?php echo $tipo; ?>" class="btn btn-primary">
                            <i class="mdi mdi-plus"></i>
                            Novo <?php echo $tipo == 'R' ? 'Recebimento' : 'Pagamento'; ?>
                        </a>
                    </div>
                </div>

                <!-- Navegação por Mês/Ano -->
                <div class="month-nav">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="year-selector mb-2 mb-md-0">
                            <button onclick="changeYear(-1)" class="btn btn-outline-secondary btn-sm" style="background-color: black;">
                                <i class="mdi mdi-chevron-left"></i>
                            </button>
                            <span class="mx-3"><?php echo $ano; ?></span>
                            <button onclick="changeYear(1)" class="btn btn-outline-secondary btn-sm" style="background-color: black;">
                                <i class="mdi mdi-chevron-right"></i>
                            </button>
                        </div>
                        <div class="months-buttons">
                            <?php foreach ($meses as $num => $nome): ?>
                                <a href="?tipo=<?php echo $tipo; ?>&mes=<?php echo $num; ?>&ano=<?php echo $ano; ?>" 
                                   class="btn btn-outline-primary <?php echo $mes == $num ? 'active' : ''; ?>" style="color: #000 !important;">
                                    <?php echo substr($nome, 0, 3); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Filtros Avançados -->
                <div class="filters-section">
                    <form method="GET" id="filterForm" class="row g-3">
                        <input type="hidden" name="tipo" value="<?php echo $tipo; ?>">
                        <input type="hidden" name="mes" value="<?php echo $mes; ?>">
                        <input type="hidden" name="ano" value="<?php echo $ano; ?>">
                        
                        <div class="col-md-3">
                            <label>Data Inicial</label>
                            <input type="date" name="data_inicial" class="form-control" value="<?php echo $data_inicial; ?>">
                        </div>
                        <div class="col-md-3">
                            <label>Data Final</label>
                            <input type="date" name="data_final" class="form-control" value="<?php echo $data_final; ?>">
                        </div>
                        <div class="col-md-3">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">Todos</option>
                                <option value="S" <?php echo $status == 'S' ? 'selected' : ''; ?>>Pago/Liquidado</option>
                                <option value="N" <?php echo $status == 'N' ? 'selected' : ''; ?>>Em Aberto</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Conta</label>
                            <select name="id_conta" class="form-control">
                                <option value="">Todas</option>
                                <?php foreach ($lista_contas as $conta): ?>
                                    <option value="<?php echo $conta['id']; ?>" <?php echo $id_conta == $conta['id'] ? 'selected' : ''; ?>>
                                        <?php echo $conta['nome_conta']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Forma de Pagamento</label>
                            <select name="id_forma_pagamento" class="form-control">
                                <option value="">Todas</option>
                                <?php foreach ($lista_formas_pagamento as $forma):
                                    print_r($forma); ?>
                                    <option value="<?php echo $forma['id']; ?>" <?php echo $id_forma_pagamento == $forma['id'] ? 'selected' : ''; ?>>
                                        <?php echo $forma['nome_forma_pagamento']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Categoria</label>
                            <select name="id_categoria" id="id_categoria" class="form-control">
                                <option value="">Todas</option>
                                <?php foreach ($lista_categorias as $categoria): ?>
                                    <option value="<?php echo $categoria['id']; ?>" <?php echo $id_categoria == $categoria['id'] ? 'selected' : ''; ?>>
                                        <?php echo $categoria['nome']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Subcategoria</label>
                            <select name="id_subcategoria" id="id_subcategoria" class="form-control">
                                <option value="">Todas</option>
                                <?php if ($id_categoria && isset($lista_subcategorias)): ?>
                                    <?php foreach ($lista_subcategorias as $subcategoria): ?>
                                        <option value="<?php echo $subcategoria['id']; ?>" <?php echo $id_subcategoria == $subcategoria['id'] ? 'selected' : ''; ?>>
                                            <?php echo $subcategoria['nome_subcategoria']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <input type="hidden" name="form_submited" value="1">

                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-filter"></i> Filtrar
                                </button>
                                <a href="?tipo=<?php echo $tipo; ?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano; ?>" 
                                   class="btn btn-secondary">
                                    <i class="mdi mdi-refresh"></i> Limpar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Tabela de Movimentos -->
                <div class="table-container">
                    <div class="table-responsive">
                        <?php if(isset($_GET['form_submited']) && $_GET['form_submited'] == 1){?>
                        <table class="table table-striped table-bordered dt-responsive nowrap">
                            <thead>
                                <tr class="text-center">
                                    <th>Nome</th>
                                    <th>Valor</th>
                                    <th>Data Vencimento</th>
                                    <th>Forma de Pagamento</th>
                                    <th>Categoria</th>
                                    <th>Subcategoria</th>
                                    <th width="120">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($movimentos as $movimento): 
                                    $listarFormasPagamento = $formas_pagamento->listarFormasPagamento($movimento['id_forma_pagamento'])[0];
                                    $listarCategorias = $categorias->listarCategorias($movimento['id_categoria'])[0];
                                    $listarSubCategorias = $categorias->listarSubcategorias($movimento['id_categoria'], $movimento['id_subcategoria'])[0];
                                ?>
                                    <tr>
                                        <td><?php echo $movimento['nome']; ?></td>
                                        <td class="valor-cell">
                                            <?php 
                                            $valor_final = $movimento['valor'] - $movimento['desconto'];
                                            echo 'R$ ' . number_format($valor_final, 2, ',', '.');
                                            
                                            if ($movimento['total_parcelas'] > 1): ?>
                                                <br>
                                                <small class="text-muted">
                                                    <?php echo 'Parcela Atual: '. $movimento['parcela_atual']; ?>/<?php echo $movimento['total_parcelas']; ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php echo date('d/m/Y', strtotime($movimento['data'])); ?>
                                        </td>
                                        <td><?php echo $listarFormasPagamento['nome_forma_pagamento']; ?></td>
                                        <td><?php echo $listarCategorias['nome']; ?></td>
                                        <td><?php echo $listarSubCategorias['nome_subcategoria']; ?></td>
                                        <td class="text-center">
                                            <a href="alteracao_movimento.php?id=<?php echo $movimento['id']; ?>" 
                                               class="table-actions link-view" title="Editar">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <?php if ($movimento['pago'] !== 'S'): ?>
                                            <button class="table-actions link-view pagar-btn" title="Pagar" 
                                                    onclick="abrirModalPagamento({
                                                        id: <?php echo $movimento['id']; ?>,
                                                        nome: '<?php echo addslashes($movimento['nome']); ?>',
                                                        valor: <?php echo $movimento['valor']; ?>,
                                                        desconto: <?php echo $movimento['desconto'] ?? 0; ?>
                                                    })">
                                                <i class="mdi mdi-cash-check"></i>
                                            </button>
                                            <?php endif; ?>
                                            <button class="table-actions link-view excluir-btn" 
                                                    type="button"
                                                    data-id="<?php echo $movimento['id']; ?>" 
                                                    data-tipo="<?php echo $tipo; ?>" 
                                                    data-id-conta-pai="<?php echo $movimento['id_conta_pai'] ?? 'null'; ?>" 
                                                    data-n-parcela="<?php echo $movimento['n_parcela']; ?>">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php } ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal de Pagamento -->
    <div class="modal fade" id="modalPagamento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Pagamento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Movimento</label>
                        <input type="text" class="form-control" id="movimentoNome" readonly>
                    </div>
                    <div class="form-group">
                        <label>Valor Original</label>
                        <input type="text" class="form-control" id="valorOriginal" readonly>
                    </div>
                    <div class="form-group">
                        <label>Desconto</label>
                        <input type="text" class="form-control money" id="desconto" value="0,00">
                    </div>
                    <div class="form-group">
                        <label>Valor com Desconto</label>
                        <input type="text" class="form-control" id="valorComDesconto" readonly>
                    </div>
                    <div class="form-group">
                        <label>Valor Pago *</label>
                        <input type="text" class="form-control money" id="valorPago" required>
                    </div>
                    <div class="form-group">
                        <label>Juros</label>
                        <input type="text" class="form-control money" id="juros" value="0,00">
                    </div>
                    <div class="form-group">
                        <label>Data do Pagamento *</label>
                        <input type="date" class="form-control" id="dataPagamento" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="confirmarPagamento()">Confirmar Pagamento</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Exclusão -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Exclusão</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Como você deseja excluir este movimento?</p>
                    <input type="hidden" id="deleteMovimentoId">
                    <input type="hidden" id="deleteMovimentoTipo">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="deleteOnlyThis">Excluir Apenas Este</button>
                    <button type="button" class="btn btn-danger" id="deleteAllInstallments" style="display: none;">
                        Excluir Todas as Parcelas
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendor js -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- App js -->
    <script src="../assets/js/app.min.js"></script>
    
    <script>
        // Função para formatar valores monetários
        function formatMoney(value) {
            if (!value) return '0,00';
            return value.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // Função para converter valor monetário em número
        function parseMoneyToNumber(value) {
            if (!value) return 0;
            return Number(value.replace(/\./g, '').replace(',', '.'));
        }

        // Atualiza o valor com desconto quando o desconto é alterado
        document.getElementById('desconto').addEventListener('input', function() {
            const valorOriginal = parseMoneyToNumber(document.getElementById('valorOriginal').value);
            const desconto = parseMoneyToNumber(this.value);
            const valorComDesconto = valorOriginal - desconto;
            document.getElementById('valorComDesconto').value = formatMoney(valorComDesconto);
            document.getElementById('valorPago').value = formatMoney(valorComDesconto);
        });

        function abrirModalPagamento(dados) {
            document.getElementById('movimentoNome').value = dados.nome;
            document.getElementById('valorOriginal').value = formatMoney(dados.valor);
            document.getElementById('desconto').value = formatMoney(dados.desconto);
            
            // Calcula o valor com desconto
            const valorComDesconto = dados.valor - dados.desconto;
            document.getElementById('valorComDesconto').value = formatMoney(valorComDesconto);
            document.getElementById('valorPago').value = formatMoney(valorComDesconto);
            
            document.getElementById('juros').value = '0,00';
            document.getElementById('dataPagamento').value = new Date().toISOString().split('T')[0];
            movimentoParaPagar = dados.id;
            
            $('#modalPagamento').modal('show');
        }

        function confirmarPagamento() {
            const valorPago = document.getElementById('valorPago').value;
            const juros = document.getElementById('juros').value;
            const dataPagamento = document.getElementById('dataPagamento').value;
            const desconto = document.getElementById('desconto').value;

            if (!valorPago || !dataPagamento) {
                alert('Por favor, preencha todos os campos obrigatórios');
                return;
            }

            // Pega os parâmetros atuais da URL
            const urlParams = new URLSearchParams(window.location.search);
            const tipo = urlParams.get('tipo') || 'D';
            const mes = urlParams.get('mes') || '<?php echo date('m'); ?>';
            const ano = urlParams.get('ano') || '<?php echo date('Y'); ?>';
            const status = urlParams.get('status') || '';
            const id_conta = urlParams.get('id_conta') || '';
            const id_forma_pagamento = urlParams.get('id_forma_pagamento') || '';
            const id_categoria = urlParams.get('id_categoria') || '';
            const id_subcategoria = urlParams.get('id_subcategoria') || '';

            // Constrói a URL mantendo os filtros atuais
            window.location.href = `index.php?id=${movimentoParaPagar}&pagar=1&valor_pago=${valorPago}&juros=${juros}&data_pagamento=${dataPagamento}&desconto=${desconto}&tipo=${tipo}&mes=${mes}&ano=${ano}&status=${status}&id_conta=${id_conta}&id_forma_pagamento=${id_forma_pagamento}&id_categoria=${id_categoria}&id_subcategoria=${id_subcategoria}`;
        }

        function changeYear(delta) {
            const urlParams = new URLSearchParams(window.location.search);
            const currentYear = parseInt(urlParams.get('ano')) || <?php echo date('Y'); ?>;
            urlParams.set('ano', currentYear + delta);
            window.location.href = '?' + urlParams.toString();
        }

        document.getElementById('id_categoria').addEventListener('change', function() {
            var categoriaId = this.value;
            var subcategoriaSelect = document.getElementById('id_subcategoria');

            // Limpa as opções atuais
            subcategoriaSelect.innerHTML = '<option value="">Carregando...</option>';

            if (categoriaId) {
                // Faz a requisição AJAX
                fetch(`get_subcategorias.php?id_categoria=${categoriaId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Limpa o select
                        subcategoriaSelect.innerHTML = '<option value="">Todas</option>';

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
                subcategoriaSelect.innerHTML = '<option value="">Todas</option>';
            }
        });

        // Carrega as subcategorias iniciais se houver uma categoria selecionada
        document.addEventListener('DOMContentLoaded', function() {
            const categoriaSelect = document.getElementById('id_categoria');
            if (categoriaSelect.value) {
                categoriaSelect.dispatchEvent(new Event('change'));
            }
        });

        let movimentoParaExcluir = null;
        let tipoMovimento = null;

        document.addEventListener('DOMContentLoaded', function() {
            const excluirBtns = document.querySelectorAll('.excluir-btn');
            excluirBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    movimentoParaExcluir = this.getAttribute('data-id');
                    tipoMovimento = this.getAttribute('data-tipo');
                    const idContaPai = this.getAttribute('data-id-conta-pai');
                    const nParcelas = this.getAttribute('data-n-parcela');
                    
                    // Mostra o botão de excluir todas as parcelas apenas se for um movimento parcelado
                    const opcaoTodasParcelas = document.getElementById('opcaoTodasParcelas');
                    if (nParcelas > 1 || (idContaPai && idContaPai !== 'null')) {
                        $('#deleteAllInstallments').show();
                    } else {
                        $('#deleteAllInstallments').hide();
                    }
                    
                    $('#confirmDeleteModal').modal('show');
                });
            });
        });

        jQuery(document).ready(function($) {
            // Inicialmente esconde o botão de excluir todas as parcelas
            $('#deleteAllInstallments').hide();

            // Quando clicar no botão de excluir
            $('.excluir-btn').on('click', function() {
                var id = $(this).data('id');
                var tipo = $(this).data('tipo');
                var idContaPai = $(this).data('id-conta-pai');
                var nParcela = $(this).data('n-parcela');

                $('#deleteMovimentoId').val(id);
                $('#deleteMovimentoTipo').val(tipo);

                // Mostra o botão de excluir todas as parcelas apenas se for um movimento parcelado
                if (nParcela > 1 || (idContaPai && idContaPai !== 'null')) {
                    $('#deleteAllInstallments').show();
                } else {
                    $('#deleteAllInstallments').hide();
                }

                $('#confirmDeleteModal').modal('show');
            });

            // Excluir apenas o movimento atual
            $('#deleteOnlyThis').on('click', function() {
                var id = $('#deleteMovimentoId').val();
                var tipo = $('#deleteMovimentoTipo').val();
                window.location.href = 'index.php?id=' + id + '&tipo=' + tipo + '&action=delete';
            });

            // Excluir todas as parcelas
            $('#deleteAllInstallments').on('click', function() {
                var id = $('#deleteMovimentoId').val();
                var tipo = $('#deleteMovimentoTipo').val();
                window.location.href = 'index.php?id=' + id + '&tipo=' + tipo + '&action=delete&all_installments=1';
            });
        });
    </script>
</body>
</html>