<?php

include 'inc/functions.inc.php';

is_logged_usuario();

extract($_POST);
extract($_GET);

if(isset($id))
    if(!is_numeric($id))
        $id = base64_decode($id);


$conn = TConnection::open(DB);



if(!isset($nome)){

    $op = 'pesquisar';
    $nome = null;
    
    $form = new form('Pesquisar Usuário', 'pesquisar-usuario.php', $op, 'get');
    $form->max = 50;
    $form->colspan = 12;
    $form->set_arg('text', 'Nome', 'Preencha apenas um nome do usuário', $nome);

    $cod_pag = $form->get_form();
    
}else{

    
    $criterio = new TCriteria;
    $criterio->add(new TFilter('nome', 'like', '%'.$nome.'%'));
    $criterio->add(new TFilter('id', '<>', 1));


    $sql = new TSqlSelect;
    $sql->setEntity('usuario');
    $sql->addColumn('id');
    $sql->addColumn('nome');
    $sql->addColumn('telefone');
    $sql->addColumn('email');
    $sql->addColumn('id_funcao');
    $sql->setCriteria($criterio);

    $result = $conn->query($sql->getInstruction());
    if($result->rowCount()){
        for($i=0; $i<$result->rowCount(); $i++){
            $entries[$i] = $result->fetch(PDO::FETCH_NUM);
            if(registro($entries[$i][0], 'usuario', 'status')==1){
                $entries[$i][0] = '<a class="btn btn-primary" href="cadastrar-usuario.php?op=visualizar&id='.base64_encode($entries[$i][0]).'">'.$entries[$i][0].'</a>';
            }else{
                $entries[$i][0] = '<a class="btn btn-danger" href="cadastrar-usuario.php?op=visualizar&id='.base64_encode($entries[$i][0]).'">'.$entries[$i][0].'</a>';
            }
            
            if($entries[$i][2]!=''){
                $tel = str_replace('(', '', $entries[$i][2]);
                $tel = str_replace(')', '', $tel);
                $tel = str_replace('-', '', $tel);
                $tel = str_replace(' ', '', $tel);
                $tel = str_replace('.', '', $tel);
                $entries[$i][2] = '<a class="btn btn-primary" href="https://api.whatsapp.com/send?phone=55'.$tel.'" target="_blank"><i class="fa fa-whatsapp"></i> '.$entries[$i][2].'</a>';
            }
            if($entries[$i][4]!=''){
                $entries[$i][4] = registro($entries[$i][4], 'usuario_funcao', 'nome');
            }
        }
        
    }else{
        $entries = null;
    }
    $column = array('ID','Nome', 'Telefone', 'E-mail', 'Função');

    $table = new table('Resultado da pesquisa de '.$nome, $column, $entries);
    $table->focus = $nome;
    $script = $table->get_script();
    $cod_pag = $table->set_table();

    
    
}







$html = new template;
if(!isset($buscar_por)){
    $cod = $html->set_box('Formulário de pesquisa de usuário', $cod_pag);
}else{
    $cod = $cod_pag;
}
$html->set_html($cod);
$html->get_html('Pesquisar Usuário', 'Pesquise por usuário ao preencher este formulário');


?>