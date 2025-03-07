<?php
require '../../conexao/conexao.php';
require '../querys_financeiro/categorias.php';
require '../querys_financeiro/movimento_caixa.php';

$categoriasObj = new Categorias($connectionmysqlfinanceiro);
$movimentoObj = new MovimentoCaixa($connectionmysqlfinanceiro);

// Pegar o mês e ano da URL
$mes = isset($_GET['mes']) ? $_GET['mes'] : date('m');
$ano = isset($_GET['ano']) ? $_GET['ano'] : date('Y');

$todasCategorias = $categoriasObj->listarCategorias();
$movimentos = $movimentoObj->listarMovimentos('', $mes, $ano);

// Separar categorias
$categoriasOperacionais = array_filter($todasCategorias, function($cat) {
    return $cat['operacional'] == 'S';
});

$categoriasNaoOperacionais = array_filter($todasCategorias, function($cat) {
    return $cat['operacional'] == 'N';
});

// Funções de cálculo
function calcularTotalCategoria($movimentos, $id_categoria) {
    $total = 0;
    foreach ($movimentos as $movimento) {
        if ($movimento['id_categoria'] == $id_categoria) {
            $valor = $movimento['valor'];
            if ($movimento['tipo'] == 'S') {
                $valor = -$valor;
            }
            if ($movimento['n_parcela'] > 1) {
                $valor = $valor / $movimento['n_parcela'];
            }
            $total += $valor;
        }
    }
    return $total;
}

function calcularTotalSubcategoria($movimentos, $id_subcategoria) {
    $total = 0;
    foreach ($movimentos as $movimento) {
        if ($movimento['id_subcategoria'] == $id_subcategoria) {
            $valor = $movimento['valor'];
            if ($movimento['tipo'] == 'S') {
                $valor = -$valor;
            }
            if ($movimento['n_parcela'] > 1) {
                $valor = $valor / $movimento['n_parcela'];
            }
            $total += $valor;
        }
    }
    return $total;
}

// Configurar cabeçalho para download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=DRE_' . $mes . '_' . $ano . '.csv');

// Criar arquivo CSV
$output = fopen('php://output', 'w');

// Adicionar BOM para Excel reconhecer UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Meses
$meses = [
    '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
    '04' => 'Abril', '05' => 'Maio', '06' => 'Junho',
    '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro',
    '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
];

// Função para escrever linha
function writeRow($output, $cells) {
    // Converter números para o formato brasileiro
    $cells = array_map(function($cell) {
        if (is_numeric($cell)) {
            return number_format($cell, 2, ',', '.');
        }
        return $cell;
    }, $cells);
    
    // Juntar células com ponto-e-vírgula e adicionar quebra de linha
    fwrite($output, implode(';', $cells) . "\r\n");
}

// Título e cabeçalhos
writeRow($output, ['DRE - Demonstração do Resultado do Exercício']);
writeRow($output, [$meses[$mes] . '/' . $ano]);
writeRow($output, ['']);
writeRow($output, ['Descrição', 'Valor (R$)', 'Tipo']);

// Receitas Operacionais
$totalOperacional = 0;

foreach ($categoriasOperacionais as $categoria) {
    $totalCategoria = calcularTotalCategoria($movimentos, $categoria['id']);
    $totalOperacional += $totalCategoria;
    
    writeRow($output, [$categoria['nome'], $totalCategoria, $totalCategoria >= 0 ? 'Receita' : 'Despesa']);
    
    if (!empty($categoria['subcategorias'])) {
        foreach ($categoria['subcategorias'] as $index => $subcategoria) {
            $totalSubcategoria = calcularTotalSubcategoria($movimentos, $categoria['subcategorias_ids'][$index]);
            writeRow($output, ['  ' . $subcategoria, $totalSubcategoria, $totalSubcategoria >= 0 ? 'Receita' : 'Despesa']);
        }
    }
}

writeRow($output, ['']);
writeRow($output, ['Total Operacional', $totalOperacional, $totalOperacional >= 0 ? 'Receita' : 'Despesa']);
writeRow($output, ['']);

// Receitas Não Operacionais
$totalNaoOperacional = 0;

foreach ($categoriasNaoOperacionais as $categoria) {
    $totalCategoria = calcularTotalCategoria($movimentos, $categoria['id']);
    $totalNaoOperacional += $totalCategoria;
    
    writeRow($output, [$categoria['nome'], $totalCategoria, $totalCategoria >= 0 ? 'Receita' : 'Despesa']);
    
    if (!empty($categoria['subcategorias'])) {
        foreach ($categoria['subcategorias'] as $index => $subcategoria) {
            $totalSubcategoria = calcularTotalSubcategoria($movimentos, $categoria['subcategorias_ids'][$index]);
            writeRow($output, ['  ' . $subcategoria, $totalSubcategoria, $totalSubcategoria >= 0 ? 'Receita' : 'Despesa']);
        }
    }
}

writeRow($output, ['']);
writeRow($output, ['Total Não Operacional', $totalNaoOperacional, $totalNaoOperacional >= 0 ? 'Receita' : 'Despesa']);
writeRow($output, ['']);

// Resultado Final
$resultadoFinal = $totalOperacional + $totalNaoOperacional;
writeRow($output, ['Resultado do Exercício', $resultadoFinal, $resultadoFinal >= 0 ? 'Receita' : 'Despesa']);
writeRow($output, ['']);
writeRow($output, ['']);

// Informações do Relatório
writeRow($output, ['Informações do Relatório']);
writeRow($output, ['Gerado em', date('d/m/Y H:i:s')]);
writeRow($output, ['Período', $meses[$mes] . '/' . $ano]);

fclose($output);
