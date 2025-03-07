<?php
require '../../conexao/conexao.php';
require '../../conexao/acesso.php';
require '../../configuacoes.php';
require '../querys_financeiro/movimento_caixa.php';
require '../querys_financeiro/forma-pagamento.php';
require '../querys_financeiro/categorias.php';

$movimentoCaixa = new MovimentoCaixa($connectionmysqlfinanceiro);
$formas_pagamento = new FormasPagamento($connectionmysqlfinanceiro);
$categorias = new Categorias($connectionmysqlfinanceiro);

$mes = isset($_GET['mes']) ? $_GET['mes'] : date('m');
$ano = isset($_GET['ano']) ? $_GET['ano'] : date('Y');

$movimentos = $movimentoCaixa->listarMovimentos('', $mes, $ano);

$total_receitas = 0;
$total_despesas = 0;
$total_nao_operacional = 0;
$movimentos_nao_operacionais = [];
$movimentos_operacionais = [];

// Separar movimentos operacionais e não operacionais
foreach ($movimentos as $movimento) {
    $categoria = $categorias->listarCategorias($movimento['id_categoria'])[0];
    // Calcula o valor real considerando o desconto
    $valor_real = $movimento['valor'] - ($movimento['desconto'] ?? 0);
    
    if ($categoria['operacional'] === 'N') {
        $movimentos_nao_operacionais[] = [
            'movimento' => $movimento,
            'categoria' => $categoria
        ];
        if ($movimento['tipo'] == 'R') {
            $total_nao_operacional += $valor_real;
        } else {
            $total_nao_operacional -= $valor_real;
        }
    } else {
        $movimentos_operacionais[] = $movimento;
        if ($movimento['tipo'] == 'R') {
            $total_receitas += $valor_real;
        } else {
            $total_despesas += $valor_real;
        }
    }
}

$pageTitle = 'Relatório de Contas'; 

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
        .movimento-despesa td {
            color: #dc3545 !important;
        }

        .movimento-receita td {
            color: #28a745 !important;
        }

        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }

        .total-receitas {
            color: #28a745;
        }

        .total-despesas {
            color: #dc3545;
        }

        .saldo-positivo {
            color: #28a745;
            font-weight: bold;
        }

        .saldo-negativo {
            color: #dc3545;
            font-weight: bold;
        }

        .valor-cell {
            text-align: right;
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

    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php include('../lateral.php'); ?>

            <main class="col-md-9 col-lg-10 content">

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
                                <a href="?&mes=<?php echo $num; ?>&ano=<?php echo $ano; ?>" 
                                   class="btn btn-outline-primary <?php echo $mes == $num ? 'active' : ''; ?>" style="color: #000 !important;">
                                    <?php echo substr($nome, 0, 3); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="card-box mt-3">
                    <div class="row">
                        <div class="col-12">
                            <h4 class="page-title">Relatório de Contas</h4>
                            <hr>
                        </div>

                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Tipo</th>
                                            <th>Categoria</th>
                                            <th>Data Vencimento</th>
                                            <th>Forma de Pagamento</th>
                                            <th>Parcelas</th>
                                            <th class="text-end">Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($movimentos_operacionais as $movimento):
                                            $listarFormasPagamento = $formas_pagamento->listarFormasPagamento($movimento['id_forma_pagamento'])[0];
                                            $categoria = $categorias->listarCategorias($movimento['id_categoria'])[0];
                                            $subcategoria_nome = '';
                                            if (!empty($movimento['id_subcategoria'])) {
                                                $subcategorias = $categorias->listarSubcategorias($movimento['id_categoria'], $movimento['id_subcategoria']);
                                                if (!empty($subcategorias)) {
                                                    $subcategoria_nome = ' > ' . $subcategorias[0]['nome_subcategoria'];
                                                }
                                            }
                                            // Calcula o valor real considerando o desconto
                                            $valor_real = $movimento['valor'] - ($movimento['desconto'] ?? 0);
                                            $classe_movimento = $movimento['tipo'] == 'R' ? 'movimento-receita' : 'movimento-despesa';
                                        ?>
                                            <tr class="<?php echo $classe_movimento; ?>">
                                                <td><?php echo $movimento['nome']; ?></td>
                                                <td><?php echo $movimento['tipo'] == 'R' ? 'Receita' : 'Despesa'; ?></td>
                                                <td><?php echo $categoria['nome'] . $subcategoria_nome; ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($movimento['data'])); ?></td>
                                                <td><?php echo $listarFormasPagamento['nome_forma_pagamento']; ?></td>
                                                <td><?php echo $movimento['n_parcela'] > 1 ? $movimento['parcela_atual'] . '/' . $movimento['n_parcela'] : '-'; ?></td>
                                                <td class="valor-cell">
                                                    R$ <?php echo number_format($valor_real, 2, ',', '.'); ?>
                                                    <?php if ($movimento['desconto'] > 0): ?>
                                                        <br><small class="text-muted">(Desconto: R$ <?php echo number_format($movimento['desconto'], 2, ',', '.'); ?>)</small>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="total-row">
                                            <td colspan="6" class="text-end">Total Receitas:</td>
                                            <td class="valor-cell total-receitas">
                                                R$ <?php echo number_format($total_receitas, 2, ',', '.'); ?>
                                            </td>
                                        </tr>
                                        <tr class="total-row">
                                            <td colspan="6" class="text-end">Total Despesas:</td>
                                            <td class="valor-cell total-despesas">
                                                R$ <?php echo number_format($total_despesas, 2, ',', '.'); ?>
                                            </td>
                                        </tr>
                                        <tr class="total-row">
                                            <td colspan="6" class="text-end">Resultado Operacional:</td>
                                            <td class="valor-cell <?php echo ($total_receitas - $total_despesas) >= 0 ? 'saldo-positivo' : 'saldo-negativo'; ?>">
                                                R$ <?php echo number_format($total_receitas - $total_despesas, 2, ',', '.'); ?>
                                            </td>
                                        </tr>
                                        <?php if (!empty($movimentos_nao_operacionais)): ?>
                                            <tr class="total-row">
                                                <td colspan="6" class="text-end">Total Não Operacional:</td>
                                                <td class="valor-cell <?php echo $total_nao_operacional >= 0 ? 'saldo-positivo' : 'saldo-negativo'; ?>">
                                                    R$ <?php echo number_format($total_nao_operacional, 2, ',', '.'); ?>
                                                </td>
                                            </tr>
                                            <tr class="total-row">
                                                <td colspan="6" class="text-end">Resultado Final:</td>
                                                <td class="valor-cell <?php echo ($total_receitas - $total_despesas + $total_nao_operacional) >= 0 ? 'saldo-positivo' : 'saldo-negativo'; ?>">
                                                    R$ <?php echo number_format($total_receitas - $total_despesas + $total_nao_operacional, 2, ',', '.'); ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tfoot>
                                </table>
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
        function changeYear(delta) {
            const urlParams = new URLSearchParams(window.location.search);
            const currentYear = parseInt(urlParams.get('ano')) || <?php echo date('Y'); ?>;
            urlParams.set('ano', currentYear + delta);
            window.location.href = '?' + urlParams.toString();
        }
    </script>
</body>

</html>