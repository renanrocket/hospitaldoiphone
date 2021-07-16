<?php

include 'inc/functions.inc.php';

is_logged_usuario();

extract($_POST);
extract($_GET);

if(isset($id))
    if(!is_numeric($id))
        $id = base64_decode($id);


$conn = TConnection::open(DB);



if(!isset($buscar_por)){

	$op = 'pesquisar';
	
	$form = new form('Pesquisar venda', 'pesquisar-venda.php', $op, 'get');
	$form->max = 50;
	$form->colspan = 12;
	$form->set_js(null, 'onchange="busca(this.value);"');
	$form->set_arg('select', 'Buscar por', array(array('nome_do_cliente', 'Nome do Cliente'), array('servico', 'Serviços'), array('data', 'Data')), null);
	$form->colspan = 12;
	$form->div_id = 'nome_do_cliente';
	$form->set_arg('text', 'Nome do Cliente', 'Digite o nome do cliente', null);
	$form->colspan = 12;
	$form->div_id = 'servico';
	$form->set_arg('text', 'Serviço', 'Digite qual item no pedido deseja encontrar', null);
	$form->colspan = 6;
	$form->div_id = 'data_1';
	$form->set_arg('text', 'Data 1', 'Periodo inicial', null);
	$form->colspan = 6;
	$form->div_id = 'data_2';
	$form->set_arg('text', 'Data 2', 'Periodo final', null);

	$cod_pag = $form->get_form();
	
}else{

	$hoje = date('Y-m-d');
	
	$criterio = new TCriteria;
	if($buscar_por=='nome_do_cliente'){
		$pesquisa = 'Nome do cliente';
		$focus = $nome_do_cliente;
		$criterio->add(new TFilter('nome', 'like', '%'.$nome_do_cliente.'%'));
	}elseif($buscar_por=='servico'){
		$pesquisa = 'Serviço';
		$focus = $servico;
		$criterio->add(new TFilter('item', 'like', '%'.$servico.'%'));
	}else{
		$pesquisa = 'Data';
		$focus = null;
		$data_1 = formataDataInv($data_1);
		$data_2 = formataDataInv($data_2);
		$criterio->add(new TFilter('data', '>=', $data_1.' 00:00:00'));
		$criterio->add(new TFilter('data', '<=', $data_2.' 23:59:59'));
	}


	$sql = new TSqlSelect();
	if($buscar_por=='servico'){
		$sql->setEntity('venda_itens');
		$sql->addColumn('venda_id');
	}else{
		$sql->setEntity('venda');
		$sql->addColumn('id');
		$sql->addColumn('nome');
		$sql->addColumn('total as total_venda');
		$sql->addColumn('data');
	}

	$sql->setCriteria($criterio);
	$entries = array();
	$result = $conn->query($sql->getInstruction());
	$loop = $result->rowCount();
	if($buscar_por=='servico'){

		$ids = array();
		for($i=0; $i<$result->rowCount(); $i++){
			extract($result->fetch(PDO::FETCH_ASSOC));
			$ids[] = $venda_id;
		}

		$loop = count($ids);

	}


	for($i=0; $i<$loop; $i++){

		if($buscar_por!='servico'){
			extract($result->fetch(PDO::FETCH_ASSOC));
			$total_venda = 'R$ '.real($total_venda, 'real');
			$data = formataData($data);
		}else{
			$id = $ids[$i];
			$nome = registro($id, 'venda', 'nome');
			$total_venda = registro($id, 'venda', 'total');
			$total_venda = 'R$ '.real($total_venda, 'real');
			$data = registro($id, 'venda', 'data');
			$data = formataData($data);
		}

		$criterio = new TCriteria();
		$criterio->add(new TFilter('venda_id', '=', $id));

		$sql = new TSqlSelect();
		$sql->setEntity('venda_itens');
		$sql->addColumn('item');
		$sql->addColumn('quantidade');
		$sql->addColumn('total as total_item');
		$sql->setCriteria($criterio);

		$resultItems = $conn->query($sql->getInstruction());

		for($j=0, $descricao = null; $j<$resultItems->rowCount(); $j++){
			extract($resultItems->fetch(PDO::FETCH_ASSOC));
			if($j!=0)
				$descricao.= '<br>';
			$descricao .= $item.' ('.$quantidade.') - R$ '.real($total_item, 'real');
		}



		$entries[$i][0] = $id;

		if(registro($entries[$i][0], 'venda', 'status')==1){
			$entries[$i][0] = '<a class="btn btn-primary" href="cadastrar-venda.php?op=visualizar&id='.base64_encode($entries[$i][0]).'">'.$entries[$i][0].'</a>';
		}else{
			$entries[$i][0] = '<a class="btn btn-danger" href="cadastrar-venda.php?op=visualizar&id='.base64_encode($entries[$i][0]).'">'.$entries[$i][0].'</a>';
		}

		$entries[$i][1] = $nome;
		$entries[$i][2] = $descricao;
		$entries[$i][3] = $total_venda;
		$entries[$i][4] = $data;


	}
	


	$column = array('ID','Cliente', 'Serviços', 'Total', 'Data de venda');

	$table = new table('Resultado da pesquisa de '.$pesquisa, $column, $entries);
	$table->focus = $focus;
	$script = $table->get_script();
	$cod_pag = $table->set_table();

	
	
}



$html = new template;
if(!isset($buscar_por)){
	$cod = $html->set_box('Formulário de pesquisa de Pedido ou Orçamento', $cod_pag);
}else{
	$cod = $cod_pag;
}
$html->set_html($cod);
$html->get_html('Pesquisar Pedido ou Orçamento', 'Pesquise por Pedido ou Orçamento ao preencher este formulário');


?>

<?php 
	if(isset($script))
		echo $script; 
?>
<script type="text/javascript">
	$(function(){
		var valor = $('select[name="buscar_por"]').val();
		busca(valor);
		
	});

	function busca(valor){
		$('#nome_do_cliente').hide();
		$('#servico').hide();
		$('#data_1').hide();
		$('#data_2').hide();

		if(valor == 'data'){
			$('#data_1').show();
			$('#data_2').show();
		}else{
			$('#'+valor).show();
		}
	}

</script>
