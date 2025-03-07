<?php
require '../../conexao/conexao.php';
require '../../configuacoes.php';

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <link rel="icon" href="../mandareceita/pages/configuracoes/logo-sm.png">
    <link rel="shortcut icon" href="/mandareceita/pages/configuracoes/logo-sm.png">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->

    <!--Material Icon -->
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/5.4.55/css/materialdesignicons.min.css">
</head>

<?php $pageTitle = 'Financeiro - Manda Receita'; ?>

<body>

    <div class="container-fluid">
        <div class="row">
            <!-- Menu Lateral -->
            <?php include('../lateral.php'); ?>

            <!-- Conteúdo Principal -->
            <main class="col-md-9 col-lg-10 content" style="background-color: #ffffff;">

                <div class="wrapper candidato">
                    <div class="container-fluid">
                        <div class="card-box mt-3">
                            <div class="row align-items-center">
                                <div class="col-12 text-left mb-4">
                                    <h4 class="page-title">Área Financeiro</h4>
                                    <hr>
                                </div>
                                

                            <hr>

                            <div class="col-12" style="display: flex; justify-content: center;">
                               <img src="logo_simples_vetor.png" alt="" style="height: 150px;">

                            </div>
                        </div>
                    </div>
                </div>



            </main>
        </div>
    </div>


    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>