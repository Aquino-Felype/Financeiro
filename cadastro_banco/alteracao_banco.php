<?php
require '../../conexao/conexao.php';
require '../../conexao/acesso.php';
require '../../configuacoes.php';
require '../querys_financeiro/contas.php';

$contas = new Contas($connectionmysqlfinanceiro);

// Verifica se foi passado um ID válido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = 'ID da conta não fornecido';
    header('Location: index.php');
    exit();
}

// Busca os dados da conta
$id = $_GET['id'];
$conta_data = $contas->listarContas($id);

if (empty($conta_data)) {
    $_SESSION['message'] = 'Conta não encontrada';
    header('Location: index.php');
    exit();
}

$conta_atual = $conta_data[0];

// Processa o update
$contas->updateConta();

if (isset($_SESSION['message'])) {
    echo "<script>alert('" . $_SESSION['message'] . "');</script>";
    unset($_SESSION['message']); // Limpa a mensagem após exibição
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<?php
$pageTitle = "Alterar Conta/Banco";
?>

<head>
    <link rel="icon" href="/mandareceita/pages/configuracoes/logo-sm.png">
    <link rel="shortcut icon" href="/mandareceita/pages/configuracoes/logo-sm.png">
</head>

<body>
    <style>
        .chosen-container {
            width: 100% !important;
        }

        .chosen-container .chosen-single {
            height: calc(1.5em + 0.75rem + 2px);
            line-height: 1.5;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            color: #495057;
            background-color: #fff;
            background-image: none;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.075);
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .chosen-container .chosen-single div b {
            background: none;
            display: inline-block;
            margin-top: 0.25em;
            vertical-align: middle;
        }

        .chosen-container .chosen-single:hover,
        .chosen-container .chosen-single:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .chosen-container .chosen-drop {
            margin-top: 0.25rem;
            border-radius: 0.25rem;
            border: 1px solid #ced4da;
            box-shadow: 0 4px 5px rgba(0, 0, 0, 0.15);
            background-color: #fff;
        }

        .chosen-container .chosen-search input[type="text"] {
            font-size: 1rem;
            height: calc(1.5em + 0.75rem + 2px);
            padding: 0.375rem 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.075);
            width: 100%;
        }

        .chosen-container .chosen-results {
            max-height: 300px;
            overflow-y: auto;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .chosen-container .chosen-results li {
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            color: #495057;
        }

        .chosen-container .chosen-results li.highlighted {
            background-color: #007bff;
            color: #fff;
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            <?php
            include('../lateral.php');
            ?>

            <main class="col-md-9 col-lg-10 content">
                <div class="wrapper candidato">
                    <div class="container-fluid">
                        <div class="card-box mt-3">
                            <div class="row">
                                <div class="col-6 text-left">
                                    <h4 class="page-title">Alterar Conta/Banco</h4>
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
                                    <form method="post">
                                        <input type="hidden" name="id_conta" value="<?php echo $conta_atual['id']; ?>">

                                        <div class="form-row mb-3">
                                            <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>Nome da Conta</th>
                                                        <th>Agência</th>
                                                        <th>Limite Inicial</th>
                                                        <th>Status</th>
                                                    </tr>
                                                    <tr class="text-center">
                                                        <th>
                                                            <input type="text" name="nome_conta" id="nome_conta"
                                                                class="form-control" required
                                                                value="<?php echo $conta_atual['nome_conta']; ?>">
                                                        </th>
                                                        <th>
                                                            <input type="text" name="agencia" id="agencia"
                                                                class="form-control"
                                                                value="<?php echo $conta_atual['agencia']; ?>">
                                                        </th>
                                                        <th>
                                                            <input type="text" name="limite_inicial"
                                                                id="limite_inicial" class="form-control"
                                                                value="<?php echo number_format($conta_atual['limite_inicial'], 2, ',', '.'); ?>"
                                                                oninput="this.value = this.value.replace(/[^0-9.,]/g, '');">
                                                        </th>

                                                        <th>
                                                            <select name="ativo" id="ativo" class="form-control">
                                                                <option value="S" <?php echo $conta_atual['ativo'] == 'S' ? 'selected' : ''; ?>>Ativo</option>
                                                                <option value="N" <?php echo $conta_atual['ativo'] == 'N' ? 'selected' : ''; ?>>Inativo</option>
                                                            </select>
                                                        </th>
                                                    </tr>
                                                </thead>
                                            </table>

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
    <script src="../../../assets/js/vendor.min.js"></script>
    <!-- App js-->
    <script src="../../../assets/js/app.min.js"></script>

    <!-- jQuery (precisa para o Chosen) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Chosen JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
    <!-- Inicializar Chosen -->
    <script>
        var $j = jQuery.noConflict();
        $j(document).ready(function() {
            $j('#id_cidade').chosen();
        });
    </script>

    <?php
    include('../../geral/mensagens_modais.php');
    ?>
</body>

</html>