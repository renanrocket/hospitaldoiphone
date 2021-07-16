<?php

include 'inc/functions.inc.php';

is_logged_usuario();

extract($_POST);
extract($_GET);

if(isset($id))
    if(!is_numeric($id))
        $id = base64_decode($id);


$conn = TConnection::open(DB);



if(isset($op)){


	$op = 'pesquisar';
	
	$form = new form('Pesquisar Fluxo de Conta', 'relatorio-caixa.php', $op, 'get');

	$form->colspan = 12;
    $form->set_arg('h3', 'Filtrar por');
	$form->colspan = 2;	
	$form->set_arg('checkbox_array', 'filtrar_por[]', 'cartao', null, 'Cartão');
	$form->colspan = 2;
	$form->set_arg('checkbox_array', 'filtrar_por[]', 'dinheiro', true, 'Dinheiro');
	$form->colspan = 2;
	$form->set_arg('checkbox_array', 'filtrar_por[]', 'transferencia', null, 'Transferência');
	$form->colspan = 2;
	$form->set_arg('checkbox_array', 'filtrar_por[]', 'boleto', null, 'Boleto');
	
	$form->colspan = 2;
	$form->div_id = 'data_1';
	$form->max = 10;
	$form->set_arg('text', 'Data 1', 'Periodo inicial', null, 'data');
	
	$form->colspan = 2;
	$form->div_id = 'data_2';
	$form->max = 10;
	$form->set_arg('text', 'Data 2', 'Periodo final', null, 'data');

	$hoje = date('d/m/Y');
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
	
}


$form = new form('Fechar caixa', 'cadastrar-fechar-caixa.php', 'novo', 'post');

$form->colspan = 6;
$form->set_arg('h3', 'Valor em especie');
$form->colspan = 6;
$form->set_arg('h3', 'R$ 359,25');

$form->colspan = 6;	
$form->set_arg('select', 'Efetuar uma', array(array('sangria', 'Sangria'), array('deposito', 'Depósito')), null);
$form->max = 10;
$form->set_arg('dinheiro', 'Total', '00,00', '179,62', 'Valor');


$cod_pag = $form->get_form();


$html = new template;
$cod_pag = $html->set_box('Formulário de pesquisa de Fluxo de Caixa', $cod_pag);
$html->set_html($cod_pag);
$html->get_html('Pesquisar Fluxo de caixa', 'Pesquise por Fluxo de caixa ao preencher este formulário');

?>

<?php 
	if(isset($script))
		echo $script; 
?>

