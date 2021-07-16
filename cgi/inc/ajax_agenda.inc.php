<?php
include_once 'functions.inc.php';

extract($_POST);
extract($_GET);
$conn = TConnection::open(DB);

if($op=='usu'){
	echo nome_sobrenome($usuario);
}elseif($op=='insert'){

	$sql = new TSqlInsert();
    $sql->setEntity('agenda');
    $sql->setRowData('id_usuario_send', $id_usuario_send);
    $sql->setRowData('id_usuario_receive', $id_usuario_receive);
    $sql->setRowData('data_inicio', $data_inicio);
    $sql->setRowData('texto', $texto);
    $sql->setRowData('cor', $cor);
    $sql->setRowData('aviso', 1);
    $result = $conn->query($sql->getInstruction());

}elseif($op=="delete"){

	$criterio = new TCriteria();
	$criterio->add(new TFilter('id', '=', $id));

	$sql = new TSqlDelete();
	$sql->setEntity('agenda');
	$sql->setCriteria($criterio);

	$result = $conn->query($sql->getInstruction());

}elseif($op=="update"){

    $criterio = new TCriteria();
    $criterio->add(new TFilter('id', '=', $id));

    $sql = new TSqlUpdate();
    $sql->setEntity('agenda');
    if(isset($data_inicio)){
        $data_inicio = explode('T', $data_inicio);
        $sql->setRowData('data_inicio', $data_inicio[0].' '.$data_inicio[1]);
    }
    if(isset($data_termino)){
        $data_termino = explode('T', $data_termino);
        $sql->setRowData('data_termino', $data_termino[0].' '.$data_termino[1]);
    }
    $sql->setCriteria($criterio);
    $result = $conn->query($sql->getInstruction());

}
    echo $op.' '.$sql->getInstruction();
?>