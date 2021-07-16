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
	$pesquisa = $buscar_por = null;
	
	$form = new form('Pesquisar Produto', 'pesquisar-produto.php', $op, 'get');
	$form->max = 50;
	$form->colspan = 12;
	$form->set_arg('select', 'Buscar por', array(array('nome', 'Nome'), array('categoria', 'Categoria'), array('sub_categoria', 'Sub Categoria')), $buscar_por);
	$form->colspan = 12;
	$form->set_arg('text', 'Pesquisa', 'Preencha com o nome, categoria ou sub categoria que deseja pesquisar', $pesquisa);

	$cod_pag = $form->get_form();
	
}else{

	$hoje = date('Y-m-d');
	
	$criterio = new TCriteria;
	$criterio->add(new TFilter($buscar_por, 'like', '%'.$pesquisa.'%'));
	


	$sql = new TSqlSelect;
	$sql->setEntity('produto');
	$sql->addColumn('id');
	$sql->addColumn('nome');
	$sql->addColumn('categoria');
	$sql->addColumn('sub_categoria');
	$sql->addColumn('preco');
	$sql->addColumn('preco_promocional');
	$sql->addColumn('data_inicio_promocao');
	$sql->addColumn('data_termino_promocao');
	$sql->setCriteria($criterio);

	$result = $conn->query($sql->getInstruction());
	
	$hoje = date('Y-m-d');
	
	if($result->rowCount()){
		for($i=0; $i<$result->rowCount(); $i++){
			
			$entries[$i] = $result->fetch(PDO::FETCH_NUM);

			$id = $entries[$i][0];

			if(registro($entries[$i][0], 'produto', 'status')==1){
				$entries[$i][0] = '<a class="btn btn-primary" href="cadastrar-produto.php?op=visualizar&id='.base64_encode($entries[$i][0]).'">'.$entries[$i][0].'</a>';
			}else{
				$entries[$i][0] = '<a class="btn btn-danger" href="cadastrar-produto.php?op=visualizar&id='.base64_encode($entries[$i][0]).'">'.$entries[$i][0].'</a>';
			}

			$varTroca = $entries[$i][1];
			$entries[$i][1] = produto_imagem($id, 'tumb', '80');
			$varTroca2 = $entries[$i][2];
			$entries[$i][2] = $varTroca;
			$varTroca = $entries[$i][3];
			$entries[$i][3] = $varTroca2;
			$preco = $entries[$i][4];
			$entries[$i][4] = $varTroca;
			if(strtotime($hoje)>=strtotime($entries[$i][6]) and 
				strtotime($hoje)<=strtotime($entries[$i][7])){
				$entries[$i][5] = '<span style="text-decoration: line-through;">R$ '.real($preco, 'real').'</span><br>R$ '.real($entries[$i][5], 'real');
			}else{
				$entries[$i][5] = 'R$ '.real($preco, 'real');
			}
			unset($entries[$i][6]);
			unset($entries[$i][7]);
			

		}
		
	}else{
		$entries = null;
	}
	$column = array('ID','Imagem', 'Nome', 'Categoria', 'Sub Categoria', 'Preços');

	$table = new table('Resultado da pesquisa de '.$pesquisa, $column, $entries);
	$table->focus = $pesquisa;
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



	if(isset($script))
		echo $script; 
?>

