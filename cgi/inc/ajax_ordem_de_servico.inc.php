<?php
include_once 'functions.inc.php';

extract($_POST);
extract($_GET);
$conn = TConnection::open(DB);

if(!isset($op)){
	$input = json_decode(file_get_contents('php://input'), true);
	extract($input);
}
	

if($op=='produto_id'){

	$criterio = new TCriteria();
	$criterio->add(new TFilter('id', '=', $id));
	$criterio->add(new TFilter('nome', '=', $produto));

	$sql = new TSqlSelect();
	$sql->setEntity('produto');
	$sql->addColumn('*');
	$sql->setCriteria($criterio);
	$result = $conn->query($sql->getInstruction());

	if($result->rowCount()){

		$criterio = new TCriteria();
		$criterio->add(new TFilter('id', '=', $id));

		$sql = new TSqlSelect();
		$sql->setEntity('produto');
		$sql->addColumn('preco');
		$sql->addColumn('preco_promocional');
		$sql->addColumn('data_inicio_promocao');
		$sql->addColumn('data_termino_promocao');
		$sql->setCriteria($criterio);
		
		$result = $conn->query($sql->getInstruction());

		for($i=0; $i<$result->rowCount(); $i++){
			extract($result->fetch(PDO::FETCH_ASSOC));

			if($preco_promocional and 
				(subtrairDatas(date('Y-m-d'), $data_inicio_promocao)<=0) and
				(subtrairDatas(date('Y-m-d'), $data_termino_promocao)>=0)
			){
				$preco_und = $preco_promocional;
			}else{
				$preco_und = $preco;
			}
			
			echo 'A partir de - <b>R$ '.real($preco_und, 'real').'</b><br>';

		}


	}else{
		echo 'false';
	}

}elseif($op=='preco'){

	$criterio = new TCriteria();
	$criterio->add(new TFilter('id', '=', $id));

	$sql = new TSqlSelect();
	$sql->setEntity('produto');
	$sql->addColumn('preco');
	$sql->addColumn('preco_promocional');
	$sql->addColumn('data_inicio_promocao');
	$sql->addColumn('data_termino_promocao');
	$sql->setCriteria($criterio);

	$result = $conn->query($sql->getInstruction());

	if($result->rowCount()){
		extract($result->fetch(PDO::FETCH_ASSOC));

		if($preco_promocional and 
			(subtrairDatas(date('Y-m-d'), $data_inicio_promocao)<=0) and
			(subtrairDatas(date('Y-m-d'), $data_termino_promocao)>=0)
		){
			$preco_und = $preco_promocional;
		}else{
			$preco_und = $preco;
		}
		$preco_sub = $preco_und;
		echo real($preco_sub, 'real');
	}



}elseif($op=='deletarimg'){

	$criterio = new TCriteria();
	$criterio->add(new TFilter('id', '=', $id));

	$sql = new TSqlSelect();
	$sql->setEntity('ordem_de_servico_atributos');
	$sql->addColumn('valor');
	$sql->setCriteria($criterio);
	$result = $conn->query($sql->getInstruction());


	if($result->rowCount()){
		extract($result->fetch(PDO::FETCH_ASSOC));
		
		$sql = new TSqlDelete();
		$sql->setEntity('ordem_de_servico_atributos');
		$sql->setCriteria($criterio);

		$result = $conn->query($sql->getInstruction());
		
		echo unlink('../'.$valor);
		
		
	}

}elseif($op=='aprovacao'){

	$criterio = new TCriteria();
	$criterio->add(new TFilter('id', '=', $id));

	$sql = new TSqlUpdate();
	$sql->setEntity('ordem_de_servico_atributos');
	$sql->setRowData('aprovado', $aprovado);
	if($aprovado==2){
		$sql->setRowData('observacao', $observacao);
	}else{
		$sql->setRowData('observacao', '');
	}
	$sql->setCriteria($criterio);

	$result = $conn->query($sql->getInstruction());

	$tarefa = 'Cliente '.registro(registro($id, 'ordem_de_servico_atributos', 'ordem_de_servico_id'),'ordem_de_servico', 'nome').', ';	
	if($aprovado==1){
		$tarefa.= '<span class="btn btn-success btn-xs">aprovou</span> ';
	}else{
		$tarefa.= '<span class="btn btn-danger btn-xs">retificou</span> ';
	}
	$tarefa.= 'o layout '.$id.' referente a ordem de serviço <a target="_blank" class="btn btn-primary btn-xs" href="cadastrar-ordem-de-servico.php?op=visualizar&id='.base64_encode(registro($id, 'ordem_de_servico_atributos', 'ordem_de_servico_id')).'">'.registro($id, 'ordem_de_servico_atributos', 'ordem_de_servico_id').'</a>';

	$criterio = new TCriteria;
	$criterio->add(new TFilter('status', '=', 1));
	$criterio->add(new TFilter('id', '<>', 1));

	$sql_selecao = new TSqlSelect();
	$sql_selecao->setEntity('usuario');
	$sql_selecao->addColumn('id as id_usuarios');
	$sql_selecao->setCriteria($criterio);

	$result_selecao = $conn->query($sql_selecao->getInstruction());

	for($i=0; $i<$result_selecao->rowCount(); $i++){

		extract($result_selecao->fetch(PDO::FETCH_ASSOC));

		$sql = new TSqlInsert();
	    $sql->setEntity('tarefas');
	    $sql->setRowData('tarefa', $tarefa);
	    $sql->setRowData('data_criacao', date('Y-m-d'));
	    $sql->setRowData('data_fazer', date('Y-m-d', strtotime('+1 day')));
	    $sql->setRowData('status', 1);
	    $sql->setRowData('id_usuario_criacao', 1);
	    $sql->setRowData('id_usuario_fazer', $id_usuarios);
	    $result = $conn->query($sql->getInstruction());

	}


	


}elseif($op=='finalizacao'){

	$criterio = new TCriteria();
	$criterio->add(new TFilter('status', '=', 1));

	$sql = new TSqlSelect();
	$sql->setEntity('ordem_de_servico_status');
	$sql->addColumn('nome as statusNome');
	$sql->setCriteria($criterio);

	$result = $conn->query($sql->getInstruction());
	$cod = null;
	for($i=0; $i<$result->rowCount(); $i++){
	    extract($result->fetch(PDO::FETCH_ASSOC));
	    if($statusNome=='Concluída'){
	    	$cod .= '<option value="'.($i+1).'" selected="yes">'.$statusNome.'</option>';
	    }else{
	    	$cod .= '<option value="'.($i+1).'">'.$statusNome.'</option>';
	    }	    
	}
	echo $cod;

}elseif($op=='salvarAssinatura'){

	$id = base64_decode($id);
	
	$criterio = new TCriteria();
	$criterio->add(new TFilter('id', '=', $id));

	$sql = new TSqlUpdate();
	$sql->setEntity('ordem_de_servico');
	$sql->setRowData('assinatura', $img);
	$sql->setCriteria($criterio);

	$result = $conn->query($sql->getInstruction());
	
	if($result->rowCount()){
		echo 'Assinatura salva.';
	}else{
		echo 'Assinatura não salva. :(';
	}
	
}



?>