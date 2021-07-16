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
	$nome = $cpf = $buscar_por = $script = null;
	
	$form = new form('Pesquisar Cliente', 'pesquisar-cliente.php', $op, 'get');
	$form->max = 50;
	$form->colspan = 12;
	$cod = '
		function show(valor){
			if(valor==\'nome\'){
				$(\'input[name="nome"]\').parent().show();
				$(\'input[name="cpf"]\').parent().hide();
			}else{
				$(\'input[name="nome"]\').parent().hide();
				$(\'input[name="cpf"]\').parent().show();
			}
		}	';
	$form->set_js($cod, 'onchange="show(this.value)"');
	$form->set_arg('select', 'Buscar por', array(array('nome', 'Nome'), array('cpf', 'CPF')), $buscar_por);
	$form->colspan = 12;
	$form->set_arg('text', 'Nome', 'Preencha apenas um nome do cliente', $nome);
	$form->colspan = 12;
	$form->set_arg('text', 'CPF', '000.000.000-00', $cpf, 'cpf');

	$cod_pag = $form->get_form();
	
}elseif($buscar_por=='nome' or $buscar_por=='cpf'){

	
	$criterio = new TCriteria;
	if($buscar_por=='nome'){
		$criterio->add(new TFilter('nome', 'like', '%'.$nome.'%'));
	}else{
		$criterio->add(new TFilter('cpf', '=', $cpf));
	}
	


	$sql = new TSqlSelect;
	$sql->setEntity('cliente');
	$sql->addColumn('id');
	$sql->addColumn('nome');
	$sql->addColumn('cpf');
	$sql->addColumn('email');
	$sql->addColumn('wpp');
	$sql->addColumn('instagram');

	$sql->setCriteria($criterio);

	$result = $conn->query($sql->getInstruction());
	if($result->rowCount()){
		for($i=0; $i<$result->rowCount(); $i++){
			$entries[$i] = $result->fetch(PDO::FETCH_NUM);
			$entries[$i][0] = '<a class="btn btn-primary" href="cadastrar-cliente.php?op=visualizar&id='.base64_encode($entries[$i][0]).'">'.$entries[$i][0].'</a>';
			
			if($entries[$i][5]!=''){
				$perfil = str_replace('@', '', $entries[$i][5]);
				$entries[$i][5] = '<a class="btn btn-primary" href="http://instagram.com/'.$perfil.'" target="_blank"><i class="fa fa-instagram"></i> '.$entries[$i][5].'</a>';
			}
			
		}
		
	}else{
		$entries = null;
	}
	$column = array('ID','Nome', 'CPF', 'E-mail', 'Whats app', 'Instagram');

	$table = new table('Resultado da pesquisa de '.$nome, $column, $entries);
	$table->focus = $nome;
	$script = $table->get_script();
	$cod_pag = $table->set_table();

	
	
}







$html = new template;
if(!isset($buscar_por)){
	$cod = $html->set_box('Formulário de pesquisa de cliente', $cod_pag);
}else{
	$cod = $cod_pag;
}
$html->set_html($cod);
$html->get_html('Pesquisar Cliente', 'Pesquise por clientes ao preencher este formulário');


?>

<?php echo $script; ?>
<script type="text/javascript">
	$(function(){
		var valor = $('select[name="buscar_por"]').val();
		show(valor);
		
	});

</script>
