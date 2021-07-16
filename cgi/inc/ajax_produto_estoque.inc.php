<?php
include_once 'functions.inc.php';

extract($_POST);
extract($_GET);
$conn = TConnection::open(DB);

$criterio = new TCriteria();
$criterio->add(new TFilter('produto_id', '=', $produto_id));
$criterio->setProperty('order', 'id desc');

$sql = new TSqlSelect();
$sql->setEntity('produto_estoque');
$sql->addColumn('fornecedor');
$sql->addColumn('preco_fornecedor');
$sql->addColumn('codigo_fornecedor');
$sql->setCriteria($criterio);
$result = $conn->query($sql->getInstruction());

if($result->rowCount()){
	extract($result->fetch(PDO::FETCH_ASSOC));
}else{
	$fornecedor = $preco_fornecedor = $codigo_fornecedor = '';
}

$retorno = array('fornecedor' => $fornecedor, 'preco_fornecedor' => $preco_fornecedor, 'codigo_fornecedor' => $codigo_fornecedor);

echo json_encode($retorno);




?>