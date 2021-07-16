<?php

include_once 'functions.inc.php';

extract($_POST);
extract($_GET);

$conn = TConnection::open(DB);

$criterio = new TCriteria;
$criterio->add(new TFilter('id', '=', $id_tarefa));

if($op=='riscado'){


	$sql = new TSqlUpdate;
	$sql->setEntity('tarefas');
	if($checked == "true"){
		$sql->setRowData('riscado', 1);
		$sql->setRowData('status', registro('Concluída', 'tarefas_status', 'id', 'nome'));
	}else{
		$sql->setRowData('riscado', '0');
		$sql->setRowData('status', registro('Em produção', 'tarefas_status', 'id', 'nome'));
	}
	$sql->setCriteria($criterio);
	$result = $conn->query($sql->getInstruction());

	if($result->rowCount()){
		echo 'OK';
	}

}elseif($op=="deletar"){

	$sql = new TSqlDelete;
	$sql->setEntity('tarefas');
	$sql->setCriteria($criterio);

	$result = $conn->query($sql->getInstruction());
	
	if($result->rowCount()){
		echo 'OK';
	}

}








?>