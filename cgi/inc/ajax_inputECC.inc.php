<?php

include_once 'functions.inc.php';

extract($_POST);
extract($_GET);

$conn = TConnection::open(DB);

if($op=='cidade'){
	
	$criterio = new TCriteria;
	$criterio->add(new TFilter('estados_cod_estados', '=', $id_estado));
	$sql = new TSqlSelect();
	$sql->setEntity('localidade_cidades');
	$sql->addColumn('nome');
	$sql->addColumn('cod_cidades');
	$sql->setCriteria($criterio);
	$result = $conn->query($sql->getInstruction());
	$cidades = null;
	
	if($result->rowCount()){
		
		$cod = "<label>Cidade</label>";
		$cod.= "<select class='form-control' name='cidade' style='width: 100%;' onchange='showCep(this.value)'>";
		
		for($i=0; $i<$result->rowCount(); $i++){
			extract($result->fetch(PDO::FETCH_ASSOC));
			$cod.= "<option value='$cod_cidades'>$nome</option>";
		}
		
		$cod.= "</select>";
	}

}elseif($op=='cep'){

	$criterio = new TCriteria;
	$criterio->add(new TFilter('cod_cidades', '=', $id_cidade));
	$sql = new TSqlSelect();
	$sql->setEntity('localidade_cidades');
	$sql->addColumn('cep');
	$sql->setCriteria($criterio);
	$result = $conn->query($sql->getInstruction());
	$cidades = null;
	
	if($result->rowCount()){
		extract($result->fetch(PDO::FETCH_ASSOC));
		$cep = $cep{0}.$cep{1}.$cep{2}.$cep{3}.$cep{4}."-".$cep{5}.$cep{6}.$cep{7};
		$cod = "<label>Cep</label>";
		$cod .= "<input type='text' class='form-control' name='cep' placeholder='00000-000' value='$cep' data-inputmask=\"'mask': ['99999-999', '99999-999']\" data-mask>";
		
	}

}

echo $cod;

?>