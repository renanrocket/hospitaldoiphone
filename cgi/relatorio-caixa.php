<?php

include 'inc/functions.inc.php';

is_logged_usuario();

extract($_POST);
extract($_GET);

if(isset($id))
    if(!is_numeric($id))
        $id = base64_decode($id);


$conn = TConnection::open(DB);



if(!isset($op)){


	$op = 'pesquisar';
	
	$form = new form('Pesquisar Fluxo de Conta', 'relatorio-caixa.php', $op, 'get');

	$form->colspan = 12;
    $form->set_arg('h3', 'Filtrar por');
    $criterio = new TCriteria();
    $criterio->add(new TFilter('status', '=', 1));

    $sql = new TSqlSelect();
    $sql->setEntity('conta_tipo_pagamento');
    $sql->addColumn('id');
    $sql->addColumn('nome');
    $sql->setCriteria($criterio);
    $result = $conn->query($sql->getInstruction());
    $filtrar_por = array();
    for($i=0; $i<$result->rowCount(); $i++){
        extract($result->fetch(PDO::FETCH_ASSOC));
        $filtrar_por[] = $id;
        $form->colspan = 2;	
        $form->set_arg('checkbox_array', 'filtrar_por[]', $id, true, $nome);
    }
    
	$hoje = date('d/m/Y');

	$form->colspan = 2;
	$form->div_id = 'data_1';
	$form->max = 10;
	$form->set_arg('text', 'Data 1', 'Periodo inicial', $hoje, 'data');
	
	$form->colspan = 2;
	$form->div_id = 'data_2';
	$form->max = 10;
	$form->set_arg('text', 'Data 2', 'Periodo final', $hoje, 'data');

	
	$ontem = date('d/m/Y', strtotime('-1 day'));
	$ultimos7 = date('d/m/Y', strtotime('-7 days'));
	$ultimos30 = date('d/m/Y', strtotime('-30 days'));

	$form->colspan = 4;
	$cod = '
		function ultimos30(){
			$("input[name=\'data_1\']").val("'.$ultimos30.'");
			$("input[name=\'data_2\']").val("'.$hoje.'");
		}
	';
	$form->set_js($cod, 'onclick="ultimos30();"');
	$form->set_arg('button', null, 'Últimos 30 dias', '#');

	$form->colspan = 4;
	$cod = '
		function ultimos7(){
			$("input[name=\'data_1\']").val("'.$ultimos7.'");
			$("input[name=\'data_2\']").val("'.$hoje.'");
		}
	';
	$form->set_js($cod, 'onclick="ultimos7();"');
	$form->set_arg('button', null, 'Últimos 7 dias', '#');

	$form->colspan = 4;
	$cod = '
		function ontem(){
			$("input[name=\'data_1\']").val("'.$ontem.'");
			$("input[name=\'data_2\']").val("'.$hoje.'");
		}
	';
	$form->set_js($cod, 'onclick="ontem();"');
	$form->set_arg('button', null, 'Ontem', '#');

	$cod_pag = $form->get_form();

	$criterio1 = new TCriteria();
	$criterio1->add(new TFilter('filial_id', '=', $_COOKIE['filial'])); //depois por a filial correta aqui
	
	$sql_conta = new TSqlSelect();
	$sql_conta->setEntity('conta');
	$sql_conta->addColumn('id');
	$sql_conta->setCriteria($criterio1);

	$criterio2 = new TCriteria();
	$criterio2->add(new TFilter('data_pagamento', '>=', formataDataInv($hoje)));
	$criterio2->add(new TFilter('data_pagamento', '<=', formataDataInv($hoje)));
	$criterio2->add(new TFilter('conta_id', '= any', '('.$sql_conta->getInstruction().')'));

	if(isset($filtrar_por)){
		$criterio3 = new TCriteria();
		for($i=0; $i<count($filtrar_por); $i++){
			$criterio3->add(new TFilter('tipo_pagamento', '=', $filtrar_por[$i]), TExpression::OR_OPERATOR);
		}
		$criterio = new TCriteria();
		$criterio->add($criterio2);
		$criterio->add($criterio3);
	}else{
		$criterio = $criterio2;
	}



	$sql_conta_itens = new TSqlSelect();
	$sql_conta_itens->setEntity('conta_itens');
	$sql_conta_itens->addColumn('*');
	$sql_conta_itens->setCriteria($criterio);

	$result = $conn->query($sql_conta_itens->getInstruction());
	$entries = array();
	for($i=0; $i<$result->rowCount(); $i++){
		extract($result->fetch(PDO::FETCH_ASSOC));
		$tabela_referente = registro($id, 'conta', 'tabela_referente');
		$referido = registro($conta_id, 'conta', 'referido');
		
		
		if($tabela_referente=='venda'){
			$prefixo = 'Venda ';
			$link = 'cadastrar-venda.php?op=visualizar&id='.base64_encode($referido);
		}else{
			$prefixo = 'Conta ';
			$link = 'cadastrar-conta.php?op=visualizar&idVenda='.base64_encode($referido);
		}

		$entries[$i][] = formataData($data_pagamento);
		if($entrada_saida==1){
			$entries[$i][] = '<a class="btn btn-primary" href="'.$link.'">'.$prefixo.$id.'</a>';
		}else{
			$entries[$i][] = '<a class="btn btn-danger" href="'.$link.'">'.$prefixo.$id.'</a>';
		}

		$criterio_referido = new TCriteria();
        $criterio_referido->add(new TFilter('venda_id', '=', $referido));

        $sql_referido = new TSqlSelect();
        $sql_referido->setEntity('venda_itens');
        $sql_referido->addColumn('item');
        $sql_referido->addColumn('quantidade');
        $sql_referido->setCriteria($criterio_referido);
        $result_referido = $conn->query($sql_referido->getInstruction());

        if($result_referido->rowCount()){
            $ref = null;
            for($j=0; $j<$result_referido->rowCount(); $j++){
                extract($result_referido->fetch(PDO::FETCH_ASSOC));
                if($j>0)
                    $ref .= ', ';
                $ref .= $item.' ('.$quantidade.')';
            }
        }else{
        	$ref = $referido;
        }
		$entries[$i][] = $ref;
		$entries[$i][] = registro($tipo_pagamento, 'conta_tipo_pagamento', 'nome');
		if($entrada_saida==1){
			$entries[$i][] = '';
			$entries[$i][] = 'R$ '.real($valor, 'real');
		}else{
			$entries[$i][] = 'R$ '.real($valor, 'real');
			$entries[$i][] = '';
		}
		$entries[$i][] = '';//colocar saldo do caixa
		$entries[$i][] = '';//colocar saldo do caixa
	}
	
	/*
	$entries[0][] = '17/08/2020 09:32:25';
	$entries[0][] = '<a class="btn btn-primary" href="#">V 1</a>';
	$entries[0][] = 'Venda de produto 1';
	$entries[0][] = 'Dinheiro';
	$entries[0][] = '';
	$entries[0][] = 'R$ 100,00';
	$entries[0][] = 'R$ 38,25';
	$entries[0][] = 'R$ 138,00';
	*/

	
	
}else{

	$hoje = date('Y-m-d');
	
	$criterio = new TCriteria();
	if($buscar_por=='nome_do_cliente'){
		$pesquisa = 'Nome do cliente';
		$focus = $nome_do_cliente;
		$criterio->add(new TFilter('nome', 'like', '%'.$nome_do_cliente.'%'));
	}elseif($buscar_por=='servico'){
		$pesquisa = 'Serviço';
		$focus = $servico;
		$criterio->add(new TFilter('produto', 'like', '%'.$servico.'%'));
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
	$sql->addColumn('produto');
	$sql->addColumn('quantidade');
	$sql->addColumn('total');
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
		$entries[$i][2] = $produto.' ('.$quantidade.')';
		$entries[$i][3] = 'R$ '. real($total,'real');
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
	$cod = $html->set_box('Formulário de pesquisa de Fluxo de Caixa', $cod_pag);
}else{
	$cod = $cod_pag;
}




$column = array('Data / Hora', 'ID', 'Operação', 'Forma de pagamento' , 'Despesas', 'Receitas', 'Saldo antes', 'Acumulado');

$table = new table('Livro caixa', $column, $entries);
$table->focus = null;
$script = $table->get_script();
$cod.= $table->set_table();


$form->colspan = 4;
$form->set_js($cod, 'onclick="ultimos30();"');
$form->set_arg('button', null, 'Últimos 30 dias', '#');


$form = new form('Pesquisar Fluxo de Conta', 'relatorio-caixa.php', '', '');
$form->colspan = 5;
$form->set_arg('button', null, 'Fechar caixa', 'cadastrar-fechar-caixa.php');
$form->colspan = 1;
$form->set_arg('h3', '');
$form->colspan = 4;
$form->set_arg('h3', 'Saldo do dia em dinheiro');
$form->colspan = 2;
$form->set_arg('h3', 'R$ 359,25');

$cod_pag = $html->set_box('Fechar caixa',$form->get_form());




$html->set_html($cod.$cod_pag);
$html->get_html('Pesquisar Fluxo de caixa', 'Pesquise por Fluxo de caixa ao preencher este formulário');



echo $form->mascara;

if(isset($script))
	echo $script; 


?>

<script type="text/javascript">
    $(function(){
        $("#id_data_1").datepicker();
    });
    $(function(){
        $("#id_data_2").datepicker();
    });
</script>




