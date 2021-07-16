<?php

include 'inc/functions.inc.php';

is_logged_usuario();

extract($_POST);
extract($_GET);

if(isset($id))
    if(!is_numeric($id))
        $id = base64_decode($id);


$conn = TConnection::open(DB);



if(!isset($nome_do_cliente)){


	$op = 'pesquisar';
	
	$form = new form('Pesquisar conta', 'pesquisar-conta.php', $op, 'get');
	
	$form->colspan = 12;
	
	$form->set_js(null, 'onchange="show(this.value)"');
	$form->set_arg('select', 'Buscar por', array(array('status', 'Status'), array('imei', 'IMEI ou Nº de Série'), array('periodo', 'Período')), null);


	$form->max = 50;
	$form->colspan = 12;
	$form->div_id = 'nome_do_cliente';
	$form->set_arg('text', 'Nome do Cliente', 'Digite o nome do cliente', null);
	
	$form->colspan = 12;
	$form->div_id = 'status';
	$form->set_arg('select', 'Status', array(array('quitado', 'Quitado'), array('nao_quitado', 'Não quitado'), array('todos', 'Todos')), null);
	
	$form->colspan = 12;
	$form->div_id = 'imei';
	$form->set_arg('text', 'IMEI', 'Digite o imei do iphone para ser encontrado', null);
	
	$form->colspan = 6;
	$form->div_id = 'data_1';
	$form->max = 10;
	$form->set_arg('text', 'Data 1', 'Periodo inicial', null, 'data');
	
	$form->colspan = 6;
	$form->div_id = 'data_2';
	$form->max = 10;
	$form->set_arg('text', 'Data 2', 'Periodo final', null, 'data');

	$cod_pag = $form->get_form();
	
}else{

	$hoje = date('Y-m-d');
	
	$criterio = new TCriteria;
	if($buscar_por=='status'){
		$focus = $nome_do_cliente;
		$criterio->add(new TFilter('recebemos_pagamos', 'like', '%'.$nome_do_cliente.'%'));
		if($status=='quitado'){
			$criterio->add(new TFilter('conta.valor', '=', '(conta.ja_pago)'));
		}elseif($status=='nao_quitado'){
			$criterio->add(new TFilter('conta.valor', '<>', '(conta.ja_pago)'));
		}
	}elseif($buscar_por=='imei'){
		$focus = $imei;
		$criterio->add(new TFilter('imei_ou_n_de_serie', '=', $imei));
	}elseif($buscar_por=='periodo'){
		$focus = null;
		$data_1 = formataDataInv($data_1);
		$data_2 = formataDataInv($data_2);
		$criterio->add(new TFilter('recebemos_pagamos', 'like', '%'.$nome_do_cliente.'%'));
		$criterio->add(new TFilter('data', '>=', $data_1.' 00:00:00'));
		$criterio->add(new TFilter('data', '<=', $data_2.' 23:59:59'));
	}

	if($buscar_por=='imei'){

		$sql = new TSqlSelect();
		$sql->setEntity('ordem_de_servico');
		$sql->addColumn('id');
		$sql->addColumn('nome as recebemos_pagamos');
		$sql->addColumn('servico as referido');
		$sql->addColumn('venda_id');
		$sql->setCriteria($criterio);

	}else{

		$sql = new TSqlSelect();
		$sql->setEntity('conta');
		$sql->addColumn('id');
		$sql->addColumn('recebemos_pagamos');
		$sql->addColumn('tabela_referente');
		$sql->addColumn('referido');
		$sql->addColumn('valor');
		$sql->addColumn('ja_pago');
		$sql->addColumn('data');
		$sql->setCriteria($criterio);

	}
	
	$result = $conn->query($sql->getInstruction());
	


	$entries = array();
	for($i=0; $i<$result->rowCount(); $i++){
		extract($result->fetch(PDO::FETCH_ASSOC));

		if($buscar_por=='imei'){

			if($venda_id){

				$criterio_referido = new TCriteria();
				$criterio_referido->add(new TFilter('tabela_referente', '=', 'venda'));
				$criterio_referido->add(new TFilter('referido', '=', $venda_id));

			}else{

				$criterio_referido = new TCriteria();
				$criterio_referido->add(new TFilter('tabela_referente', '=', 'ordem_de_servico'));
				$criterio_referido->add(new TFilter('referido', '=', $id));

			}

			$sql_referido = new TSqlSelect();
			$sql_referido->setEntity('conta');
			$sql_referido->addColumn('id');
			$sql_referido->addColumn('valor');
			$sql_referido->addColumn('ja_pago');
			$sql_referido->addColumn('data');
			$sql_referido->setCriteria($criterio_referido);
			
			$result_referido = $conn->query($sql_referido->getInstruction());


			if($result_referido->rowCount()){
				extract($result_referido->fetch(PDO::FETCH_ASSOC));
			}else{
				$valor = $ja_pago = $id = $data = null;
			}

		}else{
			$imei = null;

		}
		$nome = $recebemos_pagamos;

		if(is_numeric($referido)){
			if($tabela_referente=='venda'){
				$criterio_referido = new TCriteria();
				$criterio_referido->add(new TFilter('venda_id', '=', $referido));

				$sql_referido = new TSqlSelect();
				$sql_referido->setEntity('venda_itens');
				$sql_referido->addColumn('item');
				$sql_referido->addColumn('quantidade');
				$sql_referido->setCriteria($criterio_referido);

				$result_referido = $conn->query($sql_referido->getInstruction());

				$referido = null;
				for($j=0; $j<$result_referido->rowCount(); $j++){
					extract($result_referido->fetch(PDO::FETCH_ASSOC));
					$referido .= $item.' ('.$quantidade.'x)<br>';
				}
			}elseif($tabela_referente=='ordem_de_servico'){
				$referido = registro($referido, $tabela_referente, 'servico');
				$imei  = registro($referido, $tabela_referente, 'imei_ou_n_de_serie');
			}
		}

		$entries[$i][0] = '<a class="btn btn-primary" href="cadastrar-conta.php?op=visualizar&id='.base64_encode($id).'">'.$id.'</a>';		
		$entries[$i][1] = $nome;
		$entries[$i][2] = $referido;
		$entries[$i][3] = $imei;
		$entries[$i][4] = 'R$ '. real($valor,'real');
		$entries[$i][5] = 'R$ '. real($ja_pago,'real');
		$entries[$i][6] = 'R$ '. real($valor - $ja_pago,'real');
		$entries[$i][7] = formataData($data);

	}
	


	$column = array('ID','Cliente', 'Referido', 'Imei', 'Valor Total', 'Já Quitado', 'Resta', 'Data');

	$table = new table('Resultado da pesquisa', $column, $entries);
	$table->focus = $focus;
	$script = $table->get_script();
	$cod_pag = $table->set_table();

	
}



$html = new template;
if(!isset($buscar_por)){
	$cod = $html->set_box('Formulário de pesquisa conta', $cod_pag);
}else{
	$cod = $cod_pag;
}
$html->set_html($cod);
$html->get_html('Pesquisar conta', 'Pesquise por conta ao preencher este formulário');


?>

<script type="text/javascript">
	
	$(function(){
		var buscar_por = $('select[name="buscar_por"]').val();

		show(buscar_por);
	});

	function show(buscar_por){
		if(buscar_por=='status'){
			$('#status').show();
			$('#imei').hide();
			$('#data_1').hide();
			$('#data_2').hide();
			$('#nome_do_cliente').show();
		}else if(buscar_por=='imei'){
			$('#status').hide();
			$('#imei').show();
			$('#data_1').hide();
			$('#data_2').hide();
			$('#nome_do_cliente').hide();
		}else{
			$('#status').hide();
			$('#imei').hide();
			$('#data_1').show();
			$('#data_2').show();
			$('#nome_do_cliente').show();
		}
	}

	$("input[name='data_1']").attr("id", "datemask"); 
	$("input[name='data_1']").datepicker({
	    format: "dd/mm/yyyy",
	    autoclose: true,
	    todayHighlight: true
	});

	$("input[name='data_2']").attr("id", "datemask"); 
	$("input[name='data_2']").datepicker({
	    format: "dd/mm/yyyy",
	    autoclose: true,
	    todayHighlight: true
	});
</script>

<?php 
	if(isset($script))
		echo $script; 
?>

