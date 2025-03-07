<?php
require_once '../../conexao/conexao.php';
require_once '../querys_financeiro/movimento_caixa.php';

$movimentoCaixa = new MovimentoCaixa($connectionmysqlfinanceiro);

// Get date range parameters
$dataInicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : date('Y-m-01');
$dataFim = isset($_GET['data_fim']) ? $_GET['data_fim'] : date('Y-m-t');

// Get movements for the date range
$movimentos = $movimentoCaixa->listarMovimentos('', '', '', $dataInicio, $dataFim);

// Initialize monthly totals
$totalMensalReceitas = 0;
$totalMensalDespesas = 0;
$totalMensalReceitasPagas = 0;
$totalMensalDespesasPagas = 0;
$totalMensalReceitasPendentes = 0;
$totalMensalDespesasPendentes = 0;

// Group movements by day
$movimentosPorDia = [];
foreach ($movimentos as $movimento) {
    $dia = date('Y-m-d', strtotime($movimento['data']));
    if (!isset($movimentosPorDia[$dia])) {
        $movimentosPorDia[$dia] = [
            'receitas' => 0,
            'despesas' => 0,
            'receitas_pagas' => 0,
            'receitas_pendentes' => 0,
            'despesas_pagas' => 0,
            'despesas_pendentes' => 0,
            'movimentos' => []
        ];
    }
    
    if ($movimento['tipo'] == 'R') {
        $movimentosPorDia[$dia]['receitas'] += $movimento['valor'];
        $totalMensalReceitas += $movimento['valor'];
        if ($movimento['pago'] == 'S') {
            $movimentosPorDia[$dia]['receitas_pagas'] += $movimento['valor'];
            $totalMensalReceitasPagas += $movimento['valor'];
        } else {
            $movimentosPorDia[$dia]['receitas_pendentes'] += $movimento['valor'];
            $totalMensalReceitasPendentes += $movimento['valor'];
        }
    } else {
        $movimentosPorDia[$dia]['despesas'] += $movimento['valor'];
        $totalMensalDespesas += $movimento['valor'];
        if ($movimento['pago'] == 'S') {
            $movimentosPorDia[$dia]['despesas_pagas'] += $movimento['valor'];
            $totalMensalDespesasPagas += $movimento['valor'];
        } else {
            $movimentosPorDia[$dia]['despesas_pendentes'] += $movimento['valor'];
            $totalMensalDespesasPendentes += $movimento['valor'];
        }
    }
    
    $movimentosPorDia[$dia]['movimentos'][] = $movimento;
}

// Sort by date
ksort($movimentosPorDia);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fluxo Diário de Caixa</title>
    <link rel="icon" href="/financeiromandareceita/logo-sm.png">
    <link rel="shortcut icon" href="/financeiromandareceita/logo-sm.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .valor-cell { text-align: right; }
        .page-header {
            background-color: #fff;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        .daily-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }
        .daily-card .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 1rem;
        }
        .daily-summary {
            display: flex;
            gap: 1.5rem;
            align-items: flex-start;
            flex-wrap: wrap;
        }
        .daily-summary .date {
            font-size: 1.1rem;
            font-weight: 600;
            min-width: 120px;
        }
        .daily-summary .amount {
            font-weight: 500;
            min-width: 200px;
        }
        .daily-summary .amount.text-warning {
            color: #ffc107 !important;
        }
        .daily-summary .details {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .daily-summary .details .amount {
            font-size: 0.95rem;
            margin-left: 1rem;
        }
        .table-container {
            padding: 1rem;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .monthly-summary {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-top: 2rem;
            padding: 1.5rem;
        }
        .monthly-summary h5 {
            color: #2196F3;
            margin-bottom: 1.5rem;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }
        .summary-row:last-child {
            border-bottom: none;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid #eee;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <?php include '../lateral.php'; ?>
            
            <main class="col-md-9 col-lg-10 px-4 py-3">
                <!-- Cabeçalho da Página -->
                <div class="page-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">Fluxo Diário de Caixa</h4>
                        <small class="text-muted"><?php echo date('d/m/Y', strtotime($dataInicio)) . ' até ' . date('d/m/Y', strtotime($dataFim)); ?></small>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3 align-items-end">
                            <div class="col-auto">
                                <label class="form-label">Data Início</label>
                                <input type="date" name="data_inicio" class="form-control" value="<?php echo $dataInicio; ?>">
                            </div>
                            <div class="col-auto">
                                <label class="form-label">Data Fim</label>
                                <input type="date" name="data_fim" class="form-control" value="<?php echo $dataFim; ?>">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-filter"></i> Filtrar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Movimentos por Dia -->
                <?php foreach ($movimentosPorDia as $dia => $dados): ?>
                <div class="daily-card">
                    <div class="card-header">
                        <div class="daily-summary">
                            <div class="date">
                                <?php echo date('d/m/Y', strtotime($dia)); ?>
                            </div>
                            <div class="details">
                                <div class="amount text-success">
                                    Receitas: R$ <?php echo number_format($dados['receitas'], 2, ',', '.'); ?>
                                </div>
                                <div class="amount text-success">
                                    <i class="mdi mdi-check-circle"></i> Receitas Pagas: R$ <?php echo number_format($dados['receitas_pagas'], 2, ',', '.'); ?>
                                </div>
                                <div class="amount text-warning">
                                    <i class="mdi mdi-clock-outline"></i> Receitas Pendentes: R$ <?php echo number_format($dados['receitas_pendentes'], 2, ',', '.'); ?>
                                </div>
                            </div>
                            <div class="details">
                                <div class="amount text-danger">
                                    Despesas: R$ <?php echo number_format($dados['despesas'], 2, ',', '.'); ?>
                                </div>
                                <div class="amount text-danger">
                                    <i class="mdi mdi-check-circle"></i> Despesas Pagas: R$ <?php echo number_format($dados['despesas_pagas'], 2, ',', '.'); ?>
                                </div>
                                <div class="amount text-warning">
                                    <i class="mdi mdi-clock-outline"></i> Despesas Pendentes: R$ <?php echo number_format($dados['despesas_pendentes'], 2, ',', '.'); ?>
                                </div>
                            </div>
                            <div class="details">
                                <?php
                                    $saldo_real = $dados['receitas_pagas'] - $dados['despesas_pagas'];
                                ?>
                                <div class="amount <?php echo ($saldo_real >= 0) ? 'text-success' : 'text-danger'; ?>">
                                    <i class="mdi mdi-cash"></i> <strong>Saldo Real: R$ <?php echo number_format($saldo_real, 2, ',', '.'); ?></strong>
                                </div>
                            </div>
                            <div class="amount <?php echo ($dados['receitas'] - $dados['despesas'] >= 0) ? 'text-success' : 'text-danger'; ?>">
                                <i class="mdi mdi-chart-line"></i> <strong>Saldo Previsto: R$ <?php echo number_format($dados['receitas'] - $dados['despesas'], 2, ',', '.'); ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="table-container">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Descrição</th>
                                        <th>Tipo</th>
                                        <th class="text-end">Valor</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dados['movimentos'] as $movimento): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($movimento['nome']); ?></td>
                                        <td><?php echo $movimento['tipo'] == 'R' ? 'Receita' : 'Despesa'; ?></td>
                                        <td class="text-end <?php echo $movimento['tipo'] == 'R' ? 'text-success' : 'text-danger'; ?>">
                                            R$ <?php echo number_format($movimento['valor'], 2, ',', '.'); ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($movimento['pago'] == 'S'): ?>
                                                <span class="badge bg-success">Pago</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Pendente</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Resumo Mensal -->
                <div class="monthly-summary">
                    <h5>Fechamento do Período - <?php echo date('d/m/Y', strtotime($dataInicio)) . ' até ' . date('d/m/Y', strtotime($dataFim)); ?></h5>
                    
                    <div class="summary-row">
                        <span>Total de Receitas:</span>
                        <span class="text-success">R$ <?php echo number_format($totalMensalReceitas, 2, ',', '.'); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Receitas Pagas:</span>
                        <span class="text-success">R$ <?php echo number_format($totalMensalReceitasPagas, 2, ',', '.'); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Receitas Pendentes:</span>
                        <span class="text-warning">R$ <?php echo number_format($totalMensalReceitasPendentes, 2, ',', '.'); ?></span>
                    </div>
                    
                    <div class="summary-row mt-3">
                        <span>Total de Despesas:</span>
                        <span class="text-danger">R$ <?php echo number_format($totalMensalDespesas, 2, ',', '.'); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Despesas Pagas:</span>
                        <span class="text-danger">R$ <?php echo number_format($totalMensalDespesasPagas, 2, ',', '.'); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Despesas Pendentes:</span>
                        <span class="text-warning">R$ <?php echo number_format($totalMensalDespesasPendentes, 2, ',', '.'); ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Saldo Total do Período:</span>
                        <span class="<?php echo ($totalMensalReceitas - $totalMensalDespesas >= 0) ? 'text-success' : 'text-danger'; ?>">
                            R$ <?php echo number_format($totalMensalReceitas - $totalMensalDespesas, 2, ',', '.'); ?>
                        </span>
                    </div>
                    <div class="summary-row">
                        <span>Saldo Realizado (Pagos):</span>
                        <span class="<?php echo ($totalMensalReceitasPagas - $totalMensalDespesasPagas >= 0) ? 'text-success' : 'text-danger'; ?>">
                            R$ <?php echo number_format($totalMensalReceitasPagas - $totalMensalDespesasPagas, 2, ',', '.'); ?>
                        </span>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>