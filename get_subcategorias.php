<?php
require '../../conexao/conexao.php';
require '../../conexao/acesso.php';
require '../../configuacoes.php';
require '../querys_financeiro/categorias.php';

header('Content-Type: application/json'); // Define o retorno como JSON

if (isset($_GET['id_categoria']) or $_POST['id_categoria'] != '') {
    if(isset($_GET['id_categoria'])){
    $id_categoria = intval($_GET['id_categoria']); // Garante que é um número inteiro
    }else{
        $id_categoria = intval($_POST['id_categoria']);
    }
    $categorias = new Categorias($connectionmysqlfinanceiro);
    $subcategorias = $categorias->listarSubcategorias($id_categoria);

    if (!empty($subcategorias)) {
        echo json_encode($subcategorias);
    } else {
        echo json_encode([]); // Retorna um array vazio se não houver subcategorias
    }
} else {
    echo json_encode(['erro' => 'ID de categoria não enviado']);
}
