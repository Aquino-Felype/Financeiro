<?php
require '../../conexao/conexao.php';
require '../../conexao/acesso.php';
require '../../configuacoes.php';
require '../querys_financeiro/categorias.php';

$categorias = new Categorias($connectionmysqlfinanceiro);

// Verifica se foi passado um ID válido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = 'ID da categoria não fornecido';
    header('Location: index.php');
    exit();
}

// Busca os dados da categoria
$id = $_GET['id'];
$categoria_data = $categorias->listarCategorias($id);

if (empty($categoria_data)) {
    $_SESSION['message'] = 'Categoria não encontrada';
    header('Location: index.php');
    exit();
}

$categoria_atual = $categoria_data[0];

// Processa o update
$categorias->updateCategoria();

if (isset($_SESSION['message'])) {
    echo "<script>alert('" . $_SESSION['message'] . "');</script>";
    unset($_SESSION['message']); // Limpa a mensagem após exibição
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<?php
$pageTitle = "Alterar Categoria";
?>

<head>
    <link rel="icon" href="/financeiromandareceita/logo-sm.png">
    <link rel="shortcut icon" href="/financeiromandareceita/logo-sm.png">
    <style>
        .subcategoria-container {
            margin-top: 10px;
        }
        .subcategoria-row {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }
        .subcategoria-row .btn {
            margin-left: 5px;
        }
    </style>
</head>

<body>
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
                                    <h4 class="page-title">Alterar Categoria</h4>
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
                                    <form method="post" id="categoriaForm">
                                        <input type="hidden" name="id_categoria" value="<?php echo $categoria_atual['id']; ?>">
                                        
                                        <div class="form-row mb-3">
                                            <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>Nome da Categoria</th>
                                                        <th>Tipo</th>
                                                        <th>Operacional</th>
                                                        <th>Patrimônio da Empresa</th>
                                                    </tr>
                                                    <tr class="text-center">
                                                        <th>
                                                            <input type="text" name="nome" id="nome" 
                                                                   class="form-control" required 
                                                                   value="<?php echo $categoria_atual['nome']; ?>">
                                                        </th>
                                                        <th>
                                                            <select name="tipo" id="tipo" class="form-control" required>
                                                                <option value="">Selecione</option>
                                                                <option value="R" <?php echo ($categoria_atual['tipo'] == 'R') ? 'selected' : ''; ?>>Receita</option>
                                                                <option value="D" <?php echo ($categoria_atual['tipo'] == 'D') ? 'selected' : ''; ?>>Despesa</option>
                                                            </select>
                                                        </th>
                                                        <th>
                                                            <select name="operacional" id="operacional" class="form-control" required>
                                                                <option value="">Selecione</option>
                                                                <option value="S" <?php echo ($categoria_atual['operacional'] == 'S') ? 'selected' : ''; ?>>Sim</option>
                                                                <option value="N" <?php echo ($categoria_atual['operacional'] == 'N') ? 'selected' : ''; ?>>Não</option>
                                                            </select>
                                                        </th>
                                                        <th>
                                                            <select name="patrimonio_empresa" id="patrimonio_empresa" class="form-control" required>
                                                                <option value="">Selecione</option>
                                                                <option value="S" <?php echo ($categoria_atual['patrimonio_empresa'] == 'S') ? 'selected' : ''; ?>>Sim</option>
                                                                <option value="N" <?php echo ($categoria_atual['patrimonio_empresa'] == 'N') ? 'selected' : ''; ?>>Não</option>
                                                            </select>
                                                        </th>
                                                    </tr>
                                                </thead>
                                            </table>

                                            <div class="col-12">
                                                <h5>Subcategorias</h5>
                                                <div id="subcategorias-container" class="subcategoria-container">
                                                    <?php if (!empty($categoria_atual['subcategorias'])): ?>
                                                        <?php foreach ($categoria_atual['subcategorias'] as $index => $subcategoria): ?>
                                                            <div class="subcategoria-row">
                                                                <input type="text" name="subcategorias[]" class="form-control" 
                                                                       value="<?php echo htmlspecialchars($subcategoria); ?>" 
                                                                       placeholder="Nome da Subcategoria">
                                                                <button type="button" class="btn btn-danger btn-sm remove-subcategoria">
                                                                    <i class="mdi mdi-minus"></i>
                                                                </button>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <div class="subcategoria-row">
                                                            <input type="text" name="subcategorias[]" class="form-control" 
                                                                   placeholder="Nome da Subcategoria">
                                                            <button type="button" class="btn btn-danger btn-sm remove-subcategoria" style="display: none;">
                                                                <i class="mdi mdi-minus"></i>
                                                            </button>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <button type="button" class="btn btn-info btn-sm mt-2" id="add-subcategoria">
                                                    <i class="mdi mdi-plus"></i> Adicionar Subcategoria
                                                </button>
                                            </div>

                                            <div class="col-12 text-right mt-3">
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
    <script src="../assets/js/vendor.min.js"></script>
    <!-- App js -->
    <script src="../assets/js/app.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('subcategorias-container');
            const addButton = document.getElementById('add-subcategoria');

            // Função para atualizar a visibilidade dos botões de remover
            function updateRemoveButtons() {
                const rows = container.querySelectorAll('.subcategoria-row');
                rows.forEach(row => {
                    const removeButton = row.querySelector('.remove-subcategoria');
                    removeButton.style.display = rows.length > 1 ? 'block' : 'none';
                });
            }

            // Adicionar nova subcategoria
            addButton.addEventListener('click', function() {
                const newRow = document.createElement('div');
                newRow.className = 'subcategoria-row';
                newRow.innerHTML = `
                    <input type="text" name="subcategorias[]" class="form-control" placeholder="Nome da Subcategoria">
                    <button type="button" class="btn btn-danger btn-sm remove-subcategoria">
                        <i class="mdi mdi-minus"></i>
                    </button>
                `;
                container.appendChild(newRow);
                updateRemoveButtons();
            });

            // Remover subcategoria
            container.addEventListener('click', function(e) {
                if (e.target.closest('.remove-subcategoria')) {
                    e.target.closest('.subcategoria-row').remove();
                    updateRemoveButtons();
                }
            });

            // Validação do formulário
            document.getElementById('categoriaForm').addEventListener('submit', function(e) {
                const nome = document.getElementById('nome').value.trim();
                const tipo = document.getElementById('tipo').value;
                const operacional = document.getElementById('operacional').value;
                const patrimonio_empresa = document.getElementById('patrimonio_empresa').value;

                if (!nome || !tipo || !operacional || !patrimonio_empresa) {
                    e.preventDefault();
                    alert('Por favor, preencha todos os campos obrigatórios.');
                }
            });

            // Inicializa o estado dos botões de remover
            updateRemoveButtons();
        });
    </script>
</body>
</html>
