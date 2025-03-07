<?php
require '../../conexao/conexao.php';
require '../../conexao/acesso.php';
require '../../configuacoes.php';
require '../querys_financeiro/transferencias.php';
require_once '../querys_financeiro/contas.php';

$transferencias = new Transferencias($connectionmysqlfinanceiro);
$contas = new Contas($connectionmysqlfinanceiro);

$mes = isset($_GET['mes']) ? $_GET['mes'] : date('m');
$ano = isset($_GET['ano']) ? $_GET['ano'] : date('Y');

if ($mes <> '' && $ano <> '') {
    $sql = " AND EXTRACT(MONTH FROM data) = '$mes' AND EXTRACT(YEAR FROM data) = '$ano'";
}

$listaTransferencias = $transferencias->listarTransferencia('', $sql);

$meses = [
    '01' => 'Janeiro',
    '02' => 'Fevereiro',
    '03' => 'Março',
    '04' => 'Abril',
    '05' => 'Maio',
    '06' => 'Junho',
    '07' => 'Julho',
    '08' => 'Agosto',
    '09' => 'Setembro',
    '10' => 'Outubro',
    '11' => 'Novembro',
    '12' => 'Dezembro'
];

$total_transferencias = 0;

$pageTitle = 'Relatório de Transferências';
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
        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }
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
                                <a href="?mes=<?php echo $num; ?>&ano=<?php echo $ano; ?>" 
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
                            <h4 class="page-title">Relatório de Transferências</h4>
                            <hr>
                        </div>

                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Conta Origem</th>
                                            <th>Saldo Anterior (Origem)</th>
                                            <th>Saldo Atual (Origem)</th>
                                            <th>Conta Destino</th>
                                            <th>Saldo Anterior (Destino)</th>
                                            <th>Saldo Atual (Destino)</th>
                                            <th class="text-end">Valor Transferido</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        foreach ($listaTransferencias as $transferencia):
                                            $total_transferencias += $transferencia['valor'];
                                            
                                            // Busca informações das contas
                                            $conta_origem = $contas->listarContas($transferencia['id_conta_banco_origem'])[0];
                                            $conta_destino = $contas->listarContas($transferencia['id_conta_banco_destino'])[0];
                                        ?>
                                            <tr>
                                                <td><?php echo date('d/m/Y', strtotime($transferencia['data'])); ?></td>
                                                <td><?php echo $conta_origem['nome_conta'] . ' - ' . $conta_origem['agencia']; ?></td>
                                                <td class="valor-cell">
                                                    R$ <?php echo number_format($transferencia['saldo_origem_momento'], 2, ',', '.'); ?>
                                                </td>
                                                <td class="valor-cell">
                                                    R$ <?php echo number_format($conta_origem['limite_inicial'], 2, ',', '.'); ?>
                                                </td>
                                                <td><?php echo $conta_destino['nome_conta'] . ' - ' . $conta_destino['agencia']; ?></td>
                                                <td class="valor-cell">
                                                    R$ <?php echo number_format($transferencia['saldo_destino_momento'], 2, ',', '.'); ?>
                                                </td>
                                                <td class="valor-cell">
                                                    R$ <?php echo number_format($conta_destino['limite_inicial'], 2, ',', '.'); ?>
                                                </td>
                                                <td class="valor-cell">
                                                    R$ <?php echo number_format($transferencia['valor'], 2, ',', '.'); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="total-row">
                                            <td colspan="7" class="text-end">Total de Transferências:</td>
                                            <td class="valor-cell">
                                                R$ <?php echo number_format($total_transferencias, 2, ',', '.'); ?>
                                            </td>
                                        </tr>
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