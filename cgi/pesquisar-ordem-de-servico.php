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

	$criterio = new TCriteria();
	$criterio->add(new TFilter('status', '=', 1));

	$sql = new TSqlSelect();
	$sql->setEntity('ordem_de_servico_status');
	$sql->addColumn('id');
	$sql->addColumn('nome');
	$sql->setCriteria($criterio);

	$result = $conn->query($sql->getInstruction());

	$status_os = array();
	for($i=0; $i<$result->rowCount(); $i++){
		extract($result->fetch(PDO::FETCH_ASSOC));

		$status_os[$i][] = $id;
		$status_os[$i][] = $nome;
	}

	$op = 'pesquisar';
	
	$form = new form('Pesquisar venda', 'pesquisar-ordem-de-servico.php', $op, 'get');
	$form->max = 50;
	$form->colspan = 12;
	$form->set_js(null, 'onchange="busca(this.value);"');
	$form->set_arg('select', 'Buscar por', array(array('nome_do_cliente', 'Nome do Cliente'), array('data', 'Data'), array('status', 'Status da ordem de serviço'), array('imei', 'IMEI')), null);
	$form->colspan = 12;
	$form->div_id = 'nome_do_cliente';
	$form->set_arg('text', 'Nome do Cliente', 'Digite o nome do cliente', null);
	$form->colspan = 12;
	$form->div_id = 'servico';
	$form->set_arg('text', 'Serviço', 'Digite qual item no pedido deseja encontrar', null);
	$form->colspan = 12;
	$form->div_id = 'status';
	$form->set_arg('select', 'Status', $status_os, null);
	$form->colspan = 12;
	$form->div_id = 'imei';
	$form->set_arg('text', 'IMEI', 'Digite o imei do iphone para ser encontrado', null);
	$form->colspan = 6;
	$form->div_id = 'data_1';
	$form->max = 10;
	$form->set_arg('text', 'Data de abertura 1', 'Periodo inicial', null, 'data');
	$form->colspan = 6;
	$form->div_id = 'data_2';
	$form->max = 10;
	$form->set_arg('text', 'Data de abertura 2', 'Periodo final', null, 'data');

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
		$criterio->add(new TFilter('servico', 'like', '%'.$servico.'%'));
	}elseif($buscar_por=='imei'){
		$pesquisa = 'IMEI';
		$focus = $imei;
		$criterio->add(new TFilter('imei', '=', $imei));
	}elseif($buscar_por=='data'){
		$pesquisa = 'Data';
		$focus = null;
		$data_1 = formataDataInv($data_de_abertura_1);
		$data_2 = formataDataInv($data_de_abertura_2);
		$criterio->add(new TFilter('data_de_abertura', '>=', $data_1.' 00:00:00'));
		$criterio->add(new TFilter('data_de_abertura', '<=', $data_2.' 23:59:59'));
	}else{
		$pesquisa = 'Status';
		$focus = null;
		$criterio->add(new TFilter('status', '=', $status));
	}


	$sql = new TSqlSelect();
	$sql->setEntity('ordem_de_servico');
	$sql->addColumn('id');
	$sql->addColumn('nome');
	$sql->addColumn('wpp');
	$sql->addColumn('imei_ou_n_de_serie');
	$sql->addColumn('data_de_abertura');
	$sql->addColumn('status');
	$sql->setCriteria($criterio);
	
	$result = $conn->query($sql->getInstruction());
	


	$entries = array();
	for($i=0; $i<$result->rowCount(); $i++){
		extract($result->fetch(PDO::FETCH_ASSOC));

		$entries[$i][0] = $id;

		if($status!=1){
			$entries[$i][0] = '<a class="btn btn-primary" href="cadastrar-ordem-de-servico.php?op=visualizar&id='.base64_encode($entries[$i][0]).'">'.$entries[$i][0].'</a>';
		}else{
			$entries[$i][0] = '<a class="btn btn-danger" href="cadastrar-ordem-de-servico.php?op=visualizar&id='.base64_encode($entries[$i][0]).'">'.$entries[$i][0].'</a>';
		}

		$entries[$i][1] = $nome;
		$entries[$i][2] = $wpp;
		$entries[$i][3] = $imei_ou_n_de_serie;
		$entries[$i][4] = formataData($data_de_abertura);
		$entries[$i][5] = registro($status, 'ordem_de_servico_status', 'nome');


	}
	


	$column = array('ID','Cliente', 'Serviços', 'Total', 'Data de venda', 'Status');

	$table = new table('Resultado da pesquisa de '.$pesquisa, $column, $entries);
	$table->focus = $focus;
	$script = $table->get_script();
	$cod_pag = $table->set_table();

	
	
}



$html = new template;
if(!isset($buscar_por)){
	$cod = $html->set_box('Formulário de pesquisa de Ordem de Serviço', $cod_pag);
}else{
	$cod = $cod_pag;
}
$html->set_html($cod);
$html->get_html('Pesquisar Ordem de Serviço', 'Pesquise por Ordem de Serviço ao preencher este formulário');


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
		$('#status').hide();
		$('#imei').hide();

		if(valor == 'data'){
			$('#data_1').show();
			$('#data_2').show();
		}else{
			$('#'+valor).show();
		}
	}

	$("input[name='data_de_abertura_1']").attr("id", "datemask"); 
	$("input[name='data_de_abertura_1']").datepicker({
	    format: "dd/mm/yyyy",
	    autoclose: true,
	    todayHighlight: true
	});

	$("input[name='data_de_abertura_2']").attr("id", "datemask"); 
	$("input[name='data_de_abertura_2']").datepicker({
	    format: "dd/mm/yyyy",
	    autoclose: true,
	    todayHighlight: true
	});

</script>
