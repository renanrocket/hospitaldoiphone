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

	$tarefa = $data_criacao = $data_fazer = $id_usuario_fazer = null;
	$status = 1;
    $op = 'novo';
    $popup = null;
    $id = 0;

}elseif($op=='novo' or $op=='editar'){

	$cont = count($_POST);
	$array = array_keys($_POST);
    $valida = true;
    if(isset($id)){
        if(!is_numeric($id))
            $id = base64_decode($id);
    }

    $erro = 'Tarefa não cadastrada. <b>Motivo:</b><br>';

    if($tarefa==''){
        $valida = false;
        $erro.= 'Você precisa digitar o que deseja por nessa tarefa.<br>';   
        $error['tarefa'] = true;
    }

    if($prazo_desta_tarefa=='' or !checarDataVerdadeira($prazo_desta_tarefa)){
        $valida = false;
        $erro.= 'A data que você inseriu é inválida.<br>';   
        $error['data_fazer'] = true;
    }

    if($usuario_destinado_a_esta_tarefa==0 and $usuario_destinado_a_esta_tarefa!='todos'){
    	$valida = false;
        $erro.= 'Você precisa associar uma pessoa a este lembrete.<br>';   
        $error['id_usuario_fazer'] = true;
    }

   $id_usuario_fazer = $usuario_destinado_a_esta_tarefa;
   $data_fazer = $prazo_desta_tarefa;

	if($valida){

		if($op=='novo'){
			$usu = new usuario;
			$id_usuario_criacao = $usu->get_usuario('id');

            if($usuario_destinado_a_esta_tarefa!='todos'){
	            $sql = new TSqlInsert();
	            $sql->setEntity('tarefas');
	            $sql->setRowData('tarefa', $tarefa);
	            $sql->setRowData('data_criacao', date('Y-m-d'));
	            $sql->setRowData('data_fazer', formataDataInv($prazo_desta_tarefa));
	            $sql->setRowData('status', 1);
	            $sql->setRowData('id_usuario_criacao', $id_usuario_criacao);
	            $sql->setRowData('id_usuario_fazer', $usuario_destinado_a_esta_tarefa);
	            $result = $conn->query($sql->getInstruction());


            }else{
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
		            $sql->setRowData('data_fazer', formataDataInv($prazo_desta_tarefa));
		            $sql->setRowData('status', 1);
		            $sql->setRowData('id_usuario_criacao', $id_usuario_criacao);
		            $sql->setRowData('id_usuario_fazer', $id_usuarios);
		            $result = $conn->query($sql->getInstruction());

            	}


            }


            if($result->rowCount()){
                $id = $conn->lastInsertId();
    
                $pop = new popup('Tarefa cadastrada com sucesso', 'verde');
                $popup = $pop->get_pop();

                $op = 'editar';

            }else{

            	$pop = new popup('Tarefa não pode ser cadastrada', 'vermelho');
        		$popup = $pop->get_pop();
        		$id = 0;
            }


            
           
			

		}elseif($op=='editar'){


            $criterio = new TCriteria;
            $criterio->add(new TFilter('id', '=', $id));

            $sql = new TSqlUpdate();
            $sql->setEntity('tarefas');
            $sql->setRowData('tarefa', $tarefa);
            $sql->setRowData('data_fazer', formataDataInv($prazo_desta_tarefa));
            $sql->setRowData('status', $status);
            $sql->setRowData('id_usuario_fazer', $usuario_destinado_a_esta_tarefa);
            $sql->setCriteria($criterio);
            $result = $conn->query($sql->getInstruction());

            if($result->rowCount()){
                $id = $conn->lastInsertId();
    
                $pop = new popup('Tarefa editada com sucesso', 'verde');
                $popup = $pop->get_pop();

            }else{
            	
            	$pop = new popup('Tarefa não pode ser editada', 'vermelho');
        		$popup = $pop->get_pop();
           		$id = 0;
            }
            

        }

    }else{
        $pop = new popup($erro, 'vermelho');
        $popup = $pop->get_pop();
        if($op=='novo')
           $id = 0;
    }

}elseif($op=='deletar'){
    

    if(!is_numeric($id))
        $id = base64_decode($id);
    
    $criterio = new TCriteria;
    $criterio->add(new TFilter('id', '=', $id));
    
    $sql = new TSqlDelete;
    $sql->setEntity('tarefa');
    $sql->setRowData('status', '0');
    $sql->setCriteria($criterio);
    $result = $conn->query($sql->getInstruction());

    if($result->rowCount()){
        $pop = new popup("Tarefa deletada com sucesso.<br>");
        $popup = $pop->get_pop();
    }else{
        $pop = new popup('Empresa não pode ser deletada', 'vermelho');
        $popup = $pop->get_pop();
    }

    $tarefa = $data_criacao = $data_fazer = $id_usuario_fazer = null;
	$status = 1;
	$usu = new usuario;
    $id_usuario_criacao = $usu->get_usuario('id');
    $op = 'novo';
    $popup = null;
    $id = 0;

}elseif($op=='visualizar'){
    
    if(!is_numeric($id))
        $id = base64_decode($id);

    $criterio = new TCriteria;
    $criterio->add(new TFilter('id', '=', $id));

    $sql = new TSqlSelect;
    $sql->setEntity('tarefas');
    $sql->addColumn('*');
    $sql->setCriteria($criterio);
    $result = $conn->query($sql->getInstruction());

    if($result->rowCount()){
        extract($result->fetch(PDO::FETCH_ASSOC));
        $data_fazer = formataData($data_fazer);
    }

    

    $popup = null;
    $op = 'editar';
}



$form = new form('Tarefas', 'tarefa.php', $op);
$form->id = $id;
$form->tabela = 'tarefas';


$criterio = new TCriteria;
$criterio->add(new TFilter('id', '>', 1));
$criterio->add(new TFilter('status', '=', 1));
$criterio->setProperty('order', 'nome');

$sql = new TSqlSelect();
$sql->setEntity('usuario');
$sql->addColumn('id');
$sql->addColumn('nome');
$sql->addColumn('id_funcao');
$sql->setCriteria($criterio);
$result = $conn->query($sql->getInstruction());
$usuarios = null;
$usuarios[0][] = '0';
$usuarios[0][] = '--';
for($i=1; $i<=$result->rowCount(); $i++){
    extract($result->fetch(PDO::FETCH_ASSOC));
    $usuarios[$i][] = $id;
    $usuarios[$i][] = $nome." - ".registro($id_funcao, 'usuario_funcao', 'nome');
}
$usuarios[$i][] = 'todos';
$usuarios[$i][] = 'Todos';
$form->colspan = 12;
if(isset($error['id_usuario_fazer']))
    $form->error = true;
$form->set_arg('select', 'Usuario destinado a esta tarefa', $usuarios, $id_usuario_fazer);

if($op=='novo'){
	$form->colspan = 12;
	if(isset($error['data_fazer']))
	    $form->error = true;
	$form->set_arg('text', 'Prazo desta tarefa', 'dd/mm/AAAA', $data_fazer, 'data');
	$form->set_arg('hidden', 'status', null, 1);
}else{
	$form->colspan = 6;
	if(isset($error['data_fazer']))
	    $form->error = true;
	$form->set_arg('text', 'Prazo desta tarefa', 'dd/mm/AAAA', $data_fazer, 'data');


	$criterio = new TCriteria;
	$criterio->add(new TFilter('status', '=', 1));

	$sql = new TSqlSelect();
	$sql->setEntity('tarefas_status');
	$sql->addColumn('id');
	$sql->addColumn('nome');
	$sql->setCriteria($criterio);
	$result = $conn->query($sql->getInstruction());
	$status_lista = null;
	for($i=0; $i<$result->rowCount(); $i++){
	    extract($result->fetch(PDO::FETCH_ASSOC));
	    $status_lista[$i][] = $id;
	    $status_lista[$i][] = $nome;
	}

	$form->colspan = 6;
	$form->set_arg('select', 'status', $status_lista, $status);
}

$form->colspan = 12;
if(isset($error['tarefa']))
	    $form->error = true;
$form->set_arg('text', 'Tarefa', 'Digite aqui o que deseja lembrar', $tarefa);
$form->button_ativar_deletar = false;

$html = new template;
$cod = $html->set_box('Formulario de cadastramento de tarefas', $form->get_form().$popup);
$html->set_html($cod);
$html->get_html('Tarefas', 'Agende suas tarefas aqui');


echo $form->mascara;


?>