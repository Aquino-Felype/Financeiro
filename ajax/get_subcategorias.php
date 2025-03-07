<?php
require '../conexao/conexao.php';
require '../querys/categorias.php';

header('Content-Type: application/json');

$id_categoria = isset($_GET['id_categoria']) ? $_GET['id_categoria'] : '';

if (empty($id_categoria)) {
    echo json_encode([]);
    exit;
}

$categorias = new Categorias($connectionmysql);
$subcategorias = $categorias->listarSubcategorias($id_categoria);

echo json_encode($subcategorias);
