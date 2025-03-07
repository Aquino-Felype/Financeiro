<?php
require '../../conexao/acesso.php';
?>

<head>

    <title><?php echo $pageTitle; ?></title>

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <link href="../../../assets/css/icons.min.css" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="../style-lateral.css">

    <link href="../../../assets/css/style.css" rel="stylesheet" type="text/css" />

    <link href="../../../assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />

    <link href="../../../assets/css/icons.min.css" rel="stylesheet" type="text/css" />

    <!--Material Icon -->
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/5.4.55/css/materialdesignicons.min.css">

    <link href="../../../assets/css/style.css" rel="stylesheet" type="text/css" />

</head>


<nav class="col-md-3 col-lg-2 sidebar">
Financeiro Manda Receita    <ul style="margin-top: 35px;">
        <li class="accordion-item">
            <a class="accordion-header nav-link menu-link">
                <i class="fa fa-circle"></i>
                <span>Cadastros</span>
                <i style="margin-left: 70px;margin-bottom: 5px;" class="fa fa-chevron-down"></i>
            </a>
            <div class="accordion-content">
                <a class="dropdown-item" href="../forma_pagamento/index.php">Formas de pagamento</a>
                <a class="dropdown-item" href="../cadastro_banco/index.php">Contas/Banco</a>
                <a class="dropdown-item" href="../cadastro_categorias/index.php">Cadastro Categorias</a>
            </div>
        </li>
        <li class="accordion-item">
            <a class="accordion-header nav-link menu-link">
                <i class="fa fa-circle"></i>
                <span>Financeiro</span>
                <i style="margin-left: 70px;margin-bottom: 5px;" class="fa fa-chevron-down"></i>
            </a>
            <div class="accordion-content">
                <a class="dropdown-item" href="../financeiro/index.php?tipo=D&mes=<?php echo date('m');?>&ano=<?php echo date('Y');?>">Contas a Pagar</a>
                <a class="dropdown-item" href="../financeiro/index.php?tipo=R&mes=<?php echo date('m');?>&ano=<?php echo date('Y');?>">Contas a Receber</a>
                <a class="dropdown-item" href="../transferencias/index.php">Trasferências</a>
            </div>
        </li>
        <li class="accordion-item">
            <a class="accordion-header nav-link menu-link">
                <i class="fa fa-circle"></i>
                <span>Relatórios</span>
                <i style="margin-left: 70px;margin-bottom: 5px;" class="fa fa-chevron-down"></i>
            </a>
            <div class="accordion-content">
                <a class="dropdown-item" href="../relatorio_contas/index.php?&mes=<?php echo date('m');?>&ano=<?php echo date('Y');?>">Contas</a>
                <a class="dropdown-item" href="../relatorio_transferencias/index.php?mes=<?php echo date('m');?>&ano=<?php echo date('Y');?>">Trasferências</a>
                <a class="dropdown-item" href="../DRE/index.php?mes=<?php echo date('m');?>&ano=<?php echo date('Y');?>">DRE</a>
                <a class="dropdown-item" href="../fluxo-dias/index.php?mes=<?php echo date('m');?>&ano=<?php echo date('Y');?>">Fluxo diário/Mês</a>
            </div>
        </li>
    </ul>

    <!-- Botão Voltar -->
    <button type="button" onclick="window.location.href='../../index.php'" class="btn btn-secondary btn-back">Home - Manda receita</button>
</nav>

<script>
    document.querySelectorAll('.accordion-header').forEach((item) => {
        item.addEventListener('click', function() {
            const parent = this.parentElement;
            parent.classList.toggle('active'); // Adiciona ou remove a classe 'active'
        });
    });
</script>