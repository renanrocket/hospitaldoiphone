<?php
include_once 'functions.inc.php';

extract($_POST);
extract($_GET);
$conn = TConnection::open(DB);

$criterio = new TCriteria();
$criterio->add(new TFilter('id_conta_tipo_pagamento', '=', $id_tipo_pagamento));

$sql = new TSqlSelect();
$sql->setEntity('conta_tipo_pagamento_sub');
$sql->addColumn('id');
$sql->addColumn('nome');
$sql->setCriteria($criterio);
$result = $conn->query($sql->getInstruction());

if($result->rowCount()){

	$cod = '

	<label for="id_tipo_de_pagamento_sub_'.$i.'">
		Via '.$i.'
	</label>
	<select class="form-control " name="via_'.$i.'" style="width: 100%;">
	';

	for($i=0; $i<$result->rowCount(); $i++){
		extract($result->fetch(PDO::FETCH_ASSOC));
		if($selecionado == $id){
			$cod .= '<option value="'.$id.'" selected="yes">'.$nome.'</option>';
		}else{
			$cod .= '<option value="'.$id.'">'.$nome.'</option>';
		}
	}


	$cod .= '</select>';

	echo $cod;

}else{
	echo null;
}



?>