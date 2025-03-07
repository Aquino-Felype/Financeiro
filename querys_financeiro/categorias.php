<?php

if (file_exists('../conexao/conexao.php')) {
    include('../conexao/conexao.php');
}

class Categorias
{
    private $mysqlConnection;

    // Construtor que recebe a conexão e a armazena em uma propriedade
    public function __construct($mysqlConnection)
    {
        $this->mysqlConnection = $mysqlConnection;
    }

    public function listarSubcategorias($id_categoria, $id_subcategoria = '', $sql = '')
    {
        $sql = "SELECT id, nome_subcategoria FROM tbl_subcategorias WHERE $sql id_categoria = ?";

        $params = [$id_categoria];
        $types = 'i';

        if (!empty($id_subcategoria)) {
            $sql .= " and tbl_subcategorias.id = ?";
            $params[] = $id_subcategoria;
            $types .= 'i';  // Tipo 'i' para integer
        }

        $stmt = $this->mysqlConnection->prepare($sql);
        if (!$stmt) {
            die('Erro na preparação da consulta MySQL: ' . $this->mysqlConnection->error);
        }

        // Bind all parameters at once
        $stmt->bind_param($types, ...$params);

        if (!$stmt->execute()) {
            die('Erro na execução da consulta MySQL: ' . $stmt->error);
        }

        $result = $stmt->get_result();
        $subcategorias = [];
        while ($row = $result->fetch_assoc()) {
            $subcategorias[] = $row;
        }

        $stmt->close();
        return $subcategorias;
    }

    public function listarCategorias($id = '', $sql = '')
    {
        // Consulta SQL padrão para buscar todas as categorias
        $sql = "SELECT c.id, c.nome, c.tipo, c.operacional, c.patrimonio_empresa, 
                GROUP_CONCAT(DISTINCT s.nome_subcategoria ORDER BY s.nome_subcategoria) as subcategorias,
                GROUP_CONCAT(DISTINCT s.id ORDER BY s.nome_subcategoria) as subcategorias_ids
                FROM tbl_categorias c 
                LEFT JOIN tbl_subcategorias s ON c.id = s.id_categoria 
                WHERE 1=1 $sql 
                GROUP BY c.id";

        // Armazena os parâmetros e seus tipos para o bind
        $params = [];
        $types = '';

        // Condicional para verificar se os parâmetros foram passados e construir a query e os parâmetros adequados
        if (!empty($id)) {
            $sql = str_replace("WHERE 1=1", "WHERE c.id = ?", $sql);
            $params[] = $id;
            $types .= 'i';  // Tipo 'i' para integer
        }

        // Prepara a consulta
        $stmt = $this->mysqlConnection->prepare($sql);
        if (!$stmt) {
            die('Erro na preparação da consulta MySQL: ' . $this->mysqlConnection->error);
        }

        // Se houver parâmetros, fazemos o bind deles
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        // Executa a consulta
        if (!$stmt->execute()) {
            die('Erro na execução da consulta MySQL: ' . $stmt->error);
        }

        // Obtém o resultado
        $result = $stmt->get_result();
        if (!$result) {
            die('Erro ao obter resultado MySQL: ' . $stmt->error);
        }

        $categorias = [];
        while ($row = $result->fetch_assoc()) {
            if ($row['subcategorias']) {
                $row['subcategorias'] = explode(',', $row['subcategorias']);
                $row['subcategorias_ids'] = explode(',', $row['subcategorias_ids']);
            } else {
                $row['subcategorias'] = [];
                $row['subcategorias_ids'] = [];
            }
            $categorias[] = $row;
        }

        // Fecha a consulta
        $stmt->close();

        return $categorias;
    }

    public function addNovaCategoria()
    {
        // Verifica se os dados foram enviados via POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Verifica se os campos necessários estão presentes
            if (isset($_POST['nome']) && isset($_POST['tipo']) && isset($_POST['operacional']) && isset($_POST['patrimonio_empresa'])) {
                $this->mysqlConnection->begin_transaction();

                try {
                    // Atribui os valores do POST
                    $nome = $_POST['nome'];
                    $tipo = $_POST['tipo'];
                    $operacional = $_POST['operacional'];
                    $patrimonio_empresa = $_POST['patrimonio_empresa'];

                    // Verifica se já existe uma categoria com o mesmo nome
                    $check_query = "SELECT COUNT(*) as count FROM tbl_categorias WHERE nome = ?";
                    $check_stmt = $this->mysqlConnection->prepare($check_query);
                    
                    if (!$check_stmt) {
                        throw new Exception('Erro na preparação da consulta MySQL: ' . $this->mysqlConnection->error);
                    }

                    $check_stmt->bind_param("s", $nome);
                    $check_stmt->execute();
                    $result = $check_stmt->get_result();
                    $row = $result->fetch_assoc();
                    $check_stmt->close();

                    if ($row['count'] > 0) {
                        throw new Exception('Já existe uma categoria com este nome.');
                    }

                    // Prepara a consulta SQL de inserção da categoria
                    $query = "INSERT INTO tbl_categorias (nome, tipo, operacional, patrimonio_empresa) VALUES (?, ?, ?, ?)";
                    $stmt = $this->mysqlConnection->prepare($query);

                    if (!$stmt) {
                        throw new Exception('Erro na preparação da consulta MySQL: ' . $this->mysqlConnection->error);
                    }

                    // Vincula os parâmetros
                    $stmt->bind_param("ssss", $nome, $tipo, $operacional, $patrimonio_empresa);

                    // Executa a consulta
                    if (!$stmt->execute()) {
                        throw new Exception('Erro ao adicionar Categoria: ' . $stmt->error);
                    }

                    $categoria_id = $this->mysqlConnection->insert_id;
                    $stmt->close();

                    // Se houver subcategorias, insere-as
                    if (isset($_POST['subcategorias']) && is_array($_POST['subcategorias'])) {
                        $query = "INSERT INTO tbl_subcategorias (nome_subcategoria, id_categoria) VALUES (?, ?)";
                        $stmt = $this->mysqlConnection->prepare($query);

                        if (!$stmt) {
                            throw new Exception('Erro na preparação da consulta MySQL: ' . $this->mysqlConnection->error);
                        }

                        foreach ($_POST['subcategorias'] as $subcategoria) {
                            if (!empty(trim($subcategoria))) {
                                // Verifica se já existe uma subcategoria com o mesmo nome na categoria
                                $check_sub_query = "SELECT COUNT(*) as count FROM tbl_subcategorias WHERE nome_subcategoria = ? AND id_categoria = ?";
                                $check_sub_stmt = $this->mysqlConnection->prepare($check_sub_query);
                                
                                if (!$check_sub_stmt) {
                                    throw new Exception('Erro na preparação da consulta MySQL: ' . $this->mysqlConnection->error);
                                }

                                $check_sub_stmt->bind_param("si", $subcategoria, $categoria_id);
                                $check_sub_stmt->execute();
                                $sub_result = $check_sub_stmt->get_result();
                                $sub_row = $sub_result->fetch_assoc();
                                $check_sub_stmt->close();

                                if ($sub_row['count'] > 0) {
                                    throw new Exception('A subcategoria "' . $subcategoria . '" já existe nesta categoria.');
                                }

                                $stmt->bind_param("si", $subcategoria, $categoria_id);
                                if (!$stmt->execute()) {
                                    throw new Exception('Erro ao adicionar Subcategoria: ' . $stmt->error);
                                }
                            }
                        }
                        $stmt->close();
                    }

                    $this->mysqlConnection->commit();
                    $_SESSION['message'] = 'Categoria adicionada com sucesso!';

                } catch (Exception $e) {
                    $this->mysqlConnection->rollback();
                    $_SESSION['message'] = $e->getMessage();
                }

            } else {
                $_SESSION['message'] = 'Todos os campos são obrigatórios.';
            }

            // Redireciona para a mesma página
            header('Location: index.php');
            exit();
        }
    }

    public function updateCategoria()
    {
        // Verifica se os dados foram enviados via POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Verifica se os campos necessários estão presentes
            if (isset($_POST['id_categoria']) && isset($_POST['nome']) && isset($_POST['tipo']) && isset($_POST['operacional']) && isset($_POST['patrimonio_empresa'])) {
                $this->mysqlConnection->begin_transaction();

                try {
                    // Atribui os valores do POST
                    $id_categoria = $_POST['id_categoria'];
                    $nome = $_POST['nome'];
                    $tipo = $_POST['tipo'];
                    $operacional = $_POST['operacional'];
                    $patrimonio_empresa = $_POST['patrimonio_empresa'];

                    // Verifica se já existe outra categoria com o mesmo nome
                    $check_query = "SELECT COUNT(*) as count FROM tbl_categorias WHERE nome = ? AND id != ?";
                    $check_stmt = $this->mysqlConnection->prepare($check_query);
                    
                    if (!$check_stmt) {
                        throw new Exception('Erro na preparação da consulta MySQL: ' . $this->mysqlConnection->error);
                    }

                    $check_stmt->bind_param("si", $nome, $id_categoria);
                    $check_stmt->execute();
                    $result = $check_stmt->get_result();
                    $row = $result->fetch_assoc();
                    $check_stmt->close();

                    if ($row['count'] > 0) {
                        throw new Exception('Já existe uma categoria com este nome.');
                    }

                    // Prepara a consulta SQL de atualização da categoria
                    $query = "UPDATE tbl_categorias SET nome = ?, tipo = ?, operacional = ?, patrimonio_empresa = ? WHERE id = ?";
                    $stmt = $this->mysqlConnection->prepare($query);

                    if (!$stmt) {
                        throw new Exception('Erro na preparação da consulta MySQL: ' . $this->mysqlConnection->error);
                    }

                    // Vincula os parâmetros
                    $stmt->bind_param("ssssi", $nome, $tipo, $operacional, $patrimonio_empresa, $id_categoria);

                    // Executa a consulta
                    if (!$stmt->execute()) {
                        throw new Exception('Erro ao atualizar Categoria: ' . $stmt->error);
                    }

                    $stmt->close();

                    // Busca as subcategorias existentes
                    $query = "SELECT id, nome_subcategoria FROM tbl_subcategorias WHERE id_categoria = ?";
                    $stmt = $this->mysqlConnection->prepare($query);
                    if (!$stmt) {
                        throw new Exception('Erro na preparação da consulta MySQL: ' . $this->mysqlConnection->error);
                    }
                    $stmt->bind_param("i", $id_categoria);
                    if (!$stmt->execute()) {
                        throw new Exception('Erro ao buscar subcategorias existentes: ' . $stmt->error);
                    }
                    $result = $stmt->get_result();
                    $subcategorias_existentes = [];
                    while ($row = $result->fetch_assoc()) {
                        $subcategorias_existentes[$row['nome_subcategoria']] = $row['id'];
                    }
                    $stmt->close();

                    // Processa as subcategorias do formulário
                    $subcategorias_atualizadas = isset($_POST['subcategorias']) ? array_filter($_POST['subcategorias'], 'trim') : [];
                    $subcategorias_atualizadas = array_map('trim', $subcategorias_atualizadas);

                    // Remove subcategorias que não existem mais no formulário
                    $subcategorias_para_remover = array_diff_key($subcategorias_existentes, array_flip($subcategorias_atualizadas));
                    if (!empty($subcategorias_para_remover)) {
                        $query = "DELETE FROM tbl_subcategorias WHERE id IN (" . implode(',', $subcategorias_para_remover) . ")";
                        if (!$this->mysqlConnection->query($query)) {
                            throw new Exception('Erro ao remover subcategorias antigas: ' . $this->mysqlConnection->error);
                        }
                    }

                    // Adiciona novas subcategorias
                    $query = "INSERT INTO tbl_subcategorias (nome_subcategoria, id_categoria) VALUES (?, ?)";
                    $stmt = $this->mysqlConnection->prepare($query);
                    if (!$stmt) {
                        throw new Exception('Erro na preparação da consulta MySQL: ' . $this->mysqlConnection->error);
                    }

                    if (isset($_POST['subcategorias']) && is_array($_POST['subcategorias'])) {
                        foreach ($_POST['subcategorias'] as $subcategoria) {
                            if (!empty(trim($subcategoria))) {
                                // Verifica se já existe uma subcategoria com o mesmo nome na categoria
                                $check_sub_query = "SELECT COUNT(*) as count FROM tbl_subcategorias WHERE nome_subcategoria = ? AND id_categoria = ?";
                                $check_sub_stmt = $this->mysqlConnection->prepare($check_sub_query);
                                
                                if (!$check_sub_stmt) {
                                    throw new Exception('Erro na preparação da consulta MySQL: ' . $this->mysqlConnection->error);
                                }

                                $check_sub_stmt->bind_param("si", $subcategoria, $id_categoria);
                                $check_sub_stmt->execute();
                                $sub_result = $check_sub_stmt->get_result();
                                $sub_row = $sub_result->fetch_assoc();
                                $check_sub_stmt->close();

                                if ($sub_row['count'] > 0) {
                                    throw new Exception('A subcategoria "' . $subcategoria . '" já existe nesta categoria.');
                                }

                                $stmt->bind_param("si", $subcategoria, $id_categoria);
                                if (!$stmt->execute()) {
                                    throw new Exception('Erro ao adicionar Subcategoria: ' . $stmt->error);
                                }
                            }
                        }
                    }
                    $stmt->close();

                    $this->mysqlConnection->commit();
                    $_SESSION['message'] = 'Categoria atualizada com sucesso!';

                } catch (Exception $e) {
                    $this->mysqlConnection->rollback();
                    $_SESSION['message'] = $e->getMessage();
                }

            } else {
                $_SESSION['message'] = 'Todos os campos são obrigatórios.';
            }

            // Redireciona para a mesma página
            header('Location: index.php');
            exit();
        }
    }

    public function deletaCategoria()
    {
        // Verifica se o ID foi enviado via GET
        if (isset($_GET['id'])) {
            $this->mysqlConnection->begin_transaction();

            try {
                $id_categoria = $this->mysqlConnection->real_escape_string($_GET['id']);

                // Primeiro remove as subcategorias
                $query = "DELETE FROM tbl_subcategorias WHERE id_categoria = ?";
                $stmt = $this->mysqlConnection->prepare($query);
                if (!$stmt) {
                    throw new Exception('Erro na preparação da consulta MySQL: ' . $this->mysqlConnection->error);
                }
                $stmt->bind_param("i", $id_categoria);
                if (!$stmt->execute()) {
                    throw new Exception('Erro ao remover subcategorias: ' . $stmt->error);
                }
                $stmt->close();

                // Depois remove a categoria
                $query = "DELETE FROM tbl_categorias WHERE id = ?";
                $stmt = $this->mysqlConnection->prepare($query);
                if (!$stmt) {
                    throw new Exception('Erro na preparação da consulta MySQL: ' . $this->mysqlConnection->error);
                }
                $stmt->bind_param("i", $id_categoria);
                if (!$stmt->execute()) {
                    throw new Exception('Erro ao remover categoria: ' . $stmt->error);
                }
                $stmt->close();

                $this->mysqlConnection->commit();
                echo "<script>alert('Categoria removida com sucesso!');</script>";
                echo "<script>window.location.href = window.location.pathname;</script>";

            } catch (Exception $e) {
                $this->mysqlConnection->rollback();
                echo "Erro: " . $e->getMessage();
            }
        }
    }
}
