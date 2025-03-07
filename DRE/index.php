<?php
require '../../conexao/conexao.php';
require '../../sessao.php';
require '../../configuacoes.php';
require '../querys_financeiro/categorias.php';
require '../querys_financeiro/movimento_caixa.php';

$categoriasObj = new Categorias($connectionmysqlfinanceiro);
$movimentoObj = new MovimentoCaixa($connectionmysqlfinanceiro);

// Pegar o mês e ano atual por padrão
$mes = date('m');
$ano = date('Y');

// Permitir seleção de mês e ano via GET
if (isset($_GET['mes']) && isset($_GET['ano'])) {
    $mes = $_GET['mes'];
    $ano = $_GET['ano'];
}

$todasCategorias = $categoriasObj->listarCategorias();

// Separar categorias operacionais e não operacionais
$categoriasOperacionais = array_filter($todasCategorias, function ($cat) {
    return $cat['operacional'] == 'S';
});

$categoriasNaoOperacionais = array_filter($todasCategorias, function ($cat) {
    return $cat['operacional'] == 'N' && $cat['patrimonio_empresa'] == 'N';
});

// Função para calcular o total de uma categoria
function calcularTotalCategoria($movimentos, $id_categoria, $tipo_categoria)
{
    $total = 0;
    foreach ($movimentos as $movimento) {
        if ($movimento['id_categoria'] == $id_categoria) {
            $valor = $movimento['valor'];
            // Se tiver parcelas, considera apenas a parcela do mês
            if ($movimento['n_parcela'] > 1) {
                $valor = $valor / $movimento['n_parcela'];
            }
            // Se for despesa, valor é negativo
            if ($tipo_categoria == 'D') {
                $valor = -$valor;
            }
            $total += $valor;
        }
    }
    return $total;
}

// Função para calcular o total de uma subcategoria
function calcularTotalSubcategoria($movimentos, $id_subcategoria, $tipo_categoria)
{
    $total = 0;
    foreach ($movimentos as $movimento) {
        if ($movimento['id_subcategoria'] == $id_subcategoria) {
            $valor = $movimento['valor'];
            // Se tiver parcelas, considera apenas a parcela do mês
            if ($movimento['n_parcela'] > 1) {
                $valor = $valor / $movimento['n_parcela'];
            }
            // Se for despesa, valor é negativo
            if ($tipo_categoria == 'D') {
                $valor = -$valor;
            }
            $total += $valor;
        }
    }
    return $total;
}

// Função para calcular projeção de valores a receber
function calcularProjecaoReceber($movimentos, $id_categoria)
{
    $total = 0;
    foreach ($movimentos as $movimento) {
        if ($movimento['id_categoria'] == $id_categoria && $movimento['pago'] == 'N' && $movimento['tipo'] == 'R') {
            $total += $movimento['valor'];
        }
    }
    return $total;
}

function calcularProjecaoReceberSubcategoria($movimentos, $id_subcategoria)
{
    $total = 0;
    foreach ($movimentos as $movimento) {
        if ($movimento['id_subcategoria'] == $id_subcategoria && $movimento['pago'] == 'N' && $movimento['tipo'] == 'R') {
            $total += $movimento['valor'];
        }
    }
    return $total;
}

// Função para calcular o total de receitas
function calcularTotalReceitas($movimentos)
{
    $total = 0;
    foreach ($movimentos as $movimento) {
        if ($movimento['tipo'] == 'R') {
            $valor = $movimento['valor'];
            if ($movimento['n_parcela'] > 1) {
                $valor = $valor / $movimento['n_parcela'];
            }
            $total += $valor;
        }
    }
    return $total;
}

// Buscar todos os movimentos do mês/ano selecionado
$movimentos = $movimentoObj->listarMovimentos('', $mes, $ano);

// Calcular total de receitas para percentuais
$totalReceitas = calcularTotalReceitas($movimentos);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <link rel="icon" href="/mandareceita/pages/configuracoes/logo-sm.png">
    <link rel="shortcut icon" href="/mandareceita/pages/configuracoes/logo-sm.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/5.4.55/css/materialdesignicons.min.css">
    <style>
        .valor-negativo {
            color: red;
        }

        .valor-positivo {
            color: green;
        }

        .total {
            font-weight: bold;
            background-color: #e9ecef;
        }

        .categoria {
            font-weight: bold;
        }

        .subcategoria td:first-child {
            padding-left: 30px;
        }
    </style>
</head>

<?php $pageTitle = 'DRE - Demonstração do Resultado do Exercício'; ?>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Menu Lateral -->
            <?php include('../lateral.php'); ?>

            <!-- Conteúdo Principal -->
            <main class="col-md-9 col-lg-10 content">
                <div class="wrapper">
                    <div class="container-fluid">
                        <div class="card-box mt-3">
                            <div class="row">
                                <div class="col-12 text-left mb-4">
                                    <h4 class="page-title"><?php echo $pageTitle; ?></h4>
                                </div>
                            </div>

                            <!-- Seletor de mês e ano -->
                            <form class="mb-4" method="GET">
                                <div class="form-row">
                                    <div class="col-md-3">
                                        <select name="mes" class="form-control">
                                            <?php
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
                                            foreach ($meses as $num => $nome) {
                                                $selected = ($num == $mes) ? 'selected' : '';
                                                echo "<option value='$num' $selected>$nome</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="ano" class="form-control">
                                            <?php
                                            $anoAtual = date('Y');
                                            for ($i = $anoAtual - 2; $i <= $anoAtual + 2; $i++) {
                                                $selected = ($i == $ano) ? 'selected' : '';
                                                echo "<option value='$i' $selected>$i</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <input type="hidden" name="form_submited" value="1">

                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary">Filtrar</button>
                                    </div>
                                    <div class="col-md-2">
                                        <a href="exportar_csv.php?mes=<?php echo $mes; ?>&ano=<?php echo $ano; ?>" class="btn btn-success">
                                            <i class="mdi mdi-file-excel"></i> Excel
                                        </a>
                                    </div>
                                </div>
                            </form>

                            <?php
                            if (isset($_GET['form_submited'])):
                            ?>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Descrição</th>
                                            <th class="text-right">Valor (R$)</th>
                                            <th class="text-right">Projeção a Receber (R$)</th>
                                            <th class="text-right">% da Receita</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Categorias Operacionais -->
                                        <?php
                                        $totalOperacional = 0;
                                        foreach ($categoriasOperacionais as $categoria):
                                            $totalCategoria = calcularTotalCategoria($movimentos, $categoria['id'], $categoria['tipo']);
                                            $totalOperacional += $totalCategoria;
                                            $isReceita = $categoria['tipo'] == 'R';
                                            $projecaoReceber = $isReceita ? calcularProjecaoReceber($movimentos, $categoria['id']) : 0;
                                            $percentualReceita = $totalReceitas > 0 ? (abs($totalCategoria) / $totalReceitas) * 100 : 0;
                                        ?>
                                            <tr class="categoria">
                                                <td><?php echo htmlspecialchars($categoria['nome']); ?></td>
                                                <td class="text-right <?php echo $isReceita ? 'text-success' : 'text-danger'; ?>">
                                                    <?php
                                                    if (!$isReceita) {
                                                        echo "-" . number_format(abs($totalCategoria), 2, ',', '.');
                                                    } else {
                                                        echo number_format($totalCategoria, 2, ',', '.');
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text-right text-success">
                                                    <?php
                                                    if ($isReceita) {
                                                        echo $projecaoReceber > 0 ? number_format($projecaoReceber, 2, ',', '.') : '-';
                                                    } else {
                                                        echo '-';
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text-right">
                                                    <?php
                                                    if ($isReceita) {
                                                        echo number_format($percentualReceita, 2, ',', '.') . '%';
                                                    } else {
                                                        echo '-';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php if (!empty($categoria['subcategorias'])): ?>
                                                <?php for ($i = 0; $i < count($categoria['subcategorias']); $i++): ?>
                                                    <?php
                                                    $subcategoriaNome = $categoria['subcategorias'][$i];
                                                    $subcategoriaId = $categoria['subcategorias_ids'][$i];
                                                    $totalSubcategoria = calcularTotalSubcategoria($movimentos, $subcategoriaId, $categoria['tipo']);
                                                    $projecaoReceberSub = $isReceita ? calcularProjecaoReceberSubcategoria($movimentos, $subcategoriaId) : 0;
                                                    $percentualReceitaSub = $totalReceitas > 0 ? (abs($totalSubcategoria) / $totalReceitas) * 100 : 0;
                                                    ?>
                                                    <tr class="subcategoria">
                                                        <td><?php echo htmlspecialchars($subcategoriaNome); ?></td>
                                                        <td class="text-right <?php echo $isReceita ? 'text-success' : 'text-danger'; ?>">
                                                            <?php
                                                            if (!$isReceita) {
                                                                echo "-" . number_format(abs($totalSubcategoria), 2, ',', '.');
                                                            } else {
                                                                echo number_format($totalSubcategoria, 2, ',', '.');
                                                            }
                                                            ?>
                                                        </td>
                                                        <td class="text-right text-success">
                                                            <?php
                                                            if ($isReceita) {
                                                                echo $projecaoReceberSub > 0 ? number_format($projecaoReceberSub, 2, ',', '.') : '-';
                                                            } else {
                                                                echo '-';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td class="text-right">
                                                            <?php
                                                            if ($isReceita) {
                                                                echo number_format($percentualReceitaSub, 2, ',', '.') . '%';
                                                            } else {
                                                                echo '-';
                                                            }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php endfor; ?>
                                            <?php endif; ?>
                                        <?php endforeach; ?>

                                        <!-- Total Operacional -->
                                        <tr class="total">
                                            <td>Total Operacional</td>
                                            <td class="text-right <?php echo $totalOperacional >= 0 ? 'text-success' : 'text-danger'; ?>">
                                                <?php
                                                if ($totalOperacional < 0) {
                                                    echo "-" . number_format(abs($totalOperacional), 2, ',', '.');
                                                } else {
                                                    echo number_format($totalOperacional, 2, ',', '.');
                                                }
                                                ?>
                                            </td>
                                            <td class="text-right">-</td>
                                            <td class="text-right">-</td>
                                        </tr>

                                        <!-- Categorias Não Operacionais -->
                                        <?php
                                        $totalNaoOperacional = 0;
                                        foreach ($categoriasNaoOperacionais as $categoria):
                                            $totalCategoria = calcularTotalCategoria($movimentos, $categoria['id'], $categoria['tipo']);
                                            $totalNaoOperacional += $totalCategoria;
                                            $isReceita = $categoria['tipo'] == 'R';
                                        ?>
                                            <tr class="categoria">
                                                <td><?php echo htmlspecialchars($categoria['nome']); ?></td>
                                                <td class="text-right <?php echo $isReceita ? 'text-success' : 'text-danger'; ?>">
                                                    <?php
                                                    if (!$isReceita) {
                                                        echo "-" . number_format(abs($totalCategoria), 2, ',', '.');
                                                    } else {
                                                        echo number_format($totalCategoria, 2, ',', '.');
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text-right text-success">
                                                    <?php
                                                    if ($isReceita) {
                                                        echo $projecaoReceber > 0 ? number_format($projecaoReceber, 2, ',', '.') : '-';
                                                    } else {
                                                        echo '-';
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text-right">
                                                    <?php
                                                    if ($isReceita) {
                                                        echo number_format($percentualReceita, 2, ',', '.') . '%';
                                                    } else {
                                                        echo '-';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php if (!empty($categoria['subcategorias'])): ?>
                                                <?php for ($i = 0; $i < count($categoria['subcategorias']); $i++): ?>
                                                    <?php
                                                    $subcategoriaNome = $categoria['subcategorias'][$i];
                                                    $subcategoriaId = $categoria['subcategorias_ids'][$i];
                                                    $totalSubcategoria = calcularTotalSubcategoria($movimentos, $subcategoriaId, $categoria['tipo']);
                                                    $projecaoReceberSub = $isReceita ? calcularProjecaoReceberSubcategoria($movimentos, $subcategoriaId) : 0;
                                                    $percentualReceitaSub = $totalReceitas > 0 ? (abs($totalSubcategoria) / $totalReceitas) * 100 : 0;
                                                    ?>
                                                    <tr class="subcategoria">
                                                        <td><?php echo htmlspecialchars($subcategoriaNome); ?></td>
                                                        <td class="text-right <?php echo $isReceita ? 'text-success' : 'text-danger'; ?>">
                                                            <?php
                                                            if (!$isReceita) {
                                                                echo "-" . number_format(abs($totalSubcategoria), 2, ',', '.');
                                                            } else {
                                                                echo number_format($totalSubcategoria, 2, ',', '.');
                                                            }
                                                            ?>
                                                        </td>
                                                        <td class="text-right text-success">
                                                            <?php
                                                            if ($isReceita) {
                                                                echo $projecaoReceberSub > 0 ? number_format($projecaoReceberSub, 2, ',', '.') : '-';
                                                            } else {
                                                                echo '-';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td class="text-right">
                                                            <?php
                                                            if ($isReceita) {
                                                                echo number_format($percentualReceitaSub, 2, ',', '.') . '%';
                                                            } else {
                                                                echo '-';
                                                            }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php endfor; ?>
                                            <?php endif; ?>
                                        <?php endforeach; ?>

                                        <!-- Total Não Operacional -->
                                        <tr class="total">
                                            <td>Total Não Operacional</td>
                                            <td class="text-right <?php echo $totalNaoOperacional >= 0 ? 'text-success' : 'text-danger'; ?>">
                                                <?php
                                                if ($totalNaoOperacional < 0) {
                                                    echo "-" . number_format(abs($totalNaoOperacional), 2, ',', '.');
                                                } else {
                                                    echo number_format($totalNaoOperacional, 2, ',', '.');
                                                }
                                                ?>
                                            </td>
                                            <td class="text-right">-</td>
                                            <td class="text-right">-</td>
                                        </tr>

                                        <!-- Resultado do Exercício -->
                                        <tr class="total">
                                            <td>Resultado do Exercício</td>
                                            <td class="text-right <?php echo ($totalOperacional + $totalNaoOperacional) >= 0 ? 'text-success' : 'text-danger'; ?>">
                                                <?php 
                                                $resultadoExercicio = $totalOperacional + $totalNaoOperacional;
                                                if ($resultadoExercicio < 0) {
                                                    echo "-" . number_format(abs($resultadoExercicio), 2, ',', '.');
                                                } else {
                                                    echo number_format($resultadoExercicio, 2, ',', '.');
                                                }
                                                ?>
                                            </td>
                                            <td class="text-right">-</td>
                                            <td class="text-right">-</td>
                                        </tr>

                                        <!-- Separador para Contas Patrimoniais -->
                                        <tr>
                                            <td colspan="4" class="bg-light text-center font-weight-bold">Contas Patrimoniais</td>
                                        </tr>

                                        <!-- Categorias Patrimoniais -->
                                        <?php
                                        $totalPatrimonial = 0;
                                        $categoriasPatrimoniais = array_filter($todasCategorias, function($cat) {
                                            return $cat['operacional'] == 'N' && $cat['patrimonio_empresa'] == 'S';
                                        });

                                        foreach ($categoriasPatrimoniais as $categoria):
                                            $totalCategoria = calcularTotalCategoria($movimentos, $categoria['id'], $categoria['tipo']);
                                            $totalPatrimonial += $totalCategoria;
                                            $isReceita = $categoria['tipo'] == 'R';
                                            $projecaoReceber = $isReceita ? calcularProjecaoReceber($movimentos, $categoria['id']) : 0;
                                            $percentualReceita = $totalReceitas > 0 ? (abs($totalCategoria) / $totalReceitas) * 100 : 0;
                                        ?>
                                            <tr class="categoria">
                                                <td><?php echo htmlspecialchars($categoria['nome']); ?></td>
                                                <td class="text-right <?php echo $isReceita ? 'text-success' : 'text-danger'; ?>">
                                                    <?php 
                                                    if (!$isReceita) {
                                                        echo "-" . number_format(abs($totalCategoria), 2, ',', '.');
                                                    } else {
                                                        echo number_format($totalCategoria, 2, ',', '.');
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text-right text-success">
                                                    <?php echo $isReceita ? number_format($projecaoReceber, 2, ',', '.') : '-'; ?>
                                                </td>
                                                <td class="text-right">
                                                    <?php echo number_format($percentualReceita, 2, ',', '.'); ?>%</td>
                                            </tr>

                                            <?php if (!empty($categoria['subcategorias'])): ?>
                                                <?php for ($i = 0; $i < count($categoria['subcategorias']); $i++): ?>
                                                    <?php 
                                                    $subcategoriaNome = $categoria['subcategorias'][$i];
                                                    $subcategoriaId = $categoria['subcategorias_ids'][$i];
                                                    $totalSubcategoria = calcularTotalSubcategoria($movimentos, $subcategoriaId, $categoria['tipo']);
                                                    $projecaoReceberSub = $isReceita ? calcularProjecaoReceberSubcategoria($movimentos, $subcategoriaId) : 0;
                                                    $percentualReceitaSub = $totalReceitas > 0 ? (abs($totalSubcategoria) / $totalReceitas) * 100 : 0;
                                                    ?>
                                                    <tr class="subcategoria">
                                                        <td><?php echo htmlspecialchars($subcategoriaNome); ?></td>
                                                        <td class="text-right <?php echo $isReceita ? 'text-success' : 'text-danger'; ?>">
                                                            <?php 
                                                            if (!$isReceita) {
                                                                echo "-" . number_format(abs($totalSubcategoria), 2, ',', '.');
                                                            } else {
                                                                echo number_format($totalSubcategoria, 2, ',', '.');
                                                            }
                                                            ?>
                                                        </td>
                                                        <td class="text-right text-success">
                                                            <?php echo $isReceita ? number_format($projecaoReceberSub, 2, ',', '.') : '-'; ?>
                                                        </td>
                                                        <td class="text-right">
                                                            <?php echo number_format($percentualReceitaSub, 2, ',', '.'); ?>%</td>
                                                    </tr>
                                                <?php endfor; ?>
                                            <?php endif; ?>
                                        <?php endforeach; ?>

                                        <!-- Total Patrimonial -->
                                        <tr class="total">
                                            <td>Total Patrimonial</td>
                                            <td class="text-right <?php echo $totalPatrimonial >= 0 ? 'text-success' : 'text-danger'; ?>">
                                                <?php 
                                                if ($totalPatrimonial < 0) {
                                                    echo "-" . number_format(abs($totalPatrimonial), 2, ',', '.');
                                                } else {
                                                    echo number_format($totalPatrimonial, 2, ',', '.');
                                                }
                                                ?>
                                            </td>
                                            <td class="text-right">-</td>
                                            <td class="text-right">-</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <?php endif?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

</html>