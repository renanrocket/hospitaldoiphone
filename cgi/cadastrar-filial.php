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
	
	$nome = $email = $telefone_1 = $telefone_2 = $email = 
    $endereco = $numero = $bairro = $cidade = $estado = $cep = $status = null;
    $estado = 6;
    $cidade = 1722;
    $popup = null;
    $op = 'novo';
    $id = 0;

}elseif($op=='novo' or $op=='editar'){
	
	$cont = count($_POST);
	$array = array_keys($_POST);
    $valida = true;
    

    $erro = 'Filial não cadastrado. <b>Motivo:</b><br>';
    //filtros
    if($nome==''){
        $valida = false;
        $erro.= 'Nome do cliente inválido.<br>';
        $error['nome'] = true;
    }

    if($valida){

        if($op=='novo'){

            $sql = new TSqlInsert;
            $sql->setEntity('filiais');
            for($i=$check=0; $i<$cont; $i++){
                if($array[$i]!='op'){
                    $sql->setRowData($array[$i], $_POST[$array[$i]]);
                }
            }
            $sql->setRowData('status', 1); //status ativo          
            $result = $conn->query($sql->getInstruction());
            if($result->rowCount()){

                $idFilial = $conn->lastInsertId();
                $pop = new popup("Filial $nome cadastrada com sucesso.<br>");
                $popup = $pop->get_pop();
                $op = 'editar';
                $id = $idFilial;
                
            }else{

                $pop = new popup(
                "Não foi possível cadastrar a filial $nome.<br>
                Motivo: " . $mail->ErrorInfo, 'vermelho');
                $popup = $pop->get_pop();

            }
     
       

        }elseif($op=='editar'){

            

            $criterio = new TCriteria;
            $criterio->add(new TFilter('id', '=', $id));
            $idUsuario = $id;
            
            $sql = new TSqlUpdate;
            $sql->setEntity('filiais');
            for($i=$check=0; $i<$cont; $i++){
                if($array[$i]!='op' and $array[$i]!='id'){
                    $sql->setRowData($array[$i], $_POST[$array[$i]]);
                }
            }
            $sql->setCriteria($criterio);
            $result = $conn->query($sql->getInstruction());
            
            if($result->rowCount()){
                 $pop = new popup("Filial $nome editado com sucesso.<br>");
                    $popup = $pop->get_pop();

            }else{
                 $pop = new popup(
                    "Filial $nome não pode ser editado.<br>
                    Entre em contato com o suporte para maiores informações.<br>", 'vermelho');
                $popup = $pop->get_pop();
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
    
    $sql = new TSqlUpdate();
    $sql->setEntity('filiais');
    $sql->setRowData('status', '0');
    $sql->setCriteria($criterio);
    $result = $conn->query($sql->getInstruction());

    if($result->rowCount()){
        $pop = new popup("Filial deletado com sucesso.<br>");
        $popup = $pop->get_pop();
    }else{
        $pop = new popup("Filial não pode ser deletado.<br>", 'vermelho');
        $popup = $pop->get_pop();
    }

    $nome = $email = $telefone_1 = $telefone_2 = $email = 
    $endereco = $numero = $bairro = $cidade = $estado = $cep = $status = null;
    $estado = 6;
    $cidade = 1722;
    $op = 'novo';
    $id = 0;


}elseif($op=='ativar'){

    if(!is_numeric($id))
        $id = base64_decode($id);
    
    $criterio = new TCriteria();
    $criterio->add(new TFilter('id', '=', $id));
    $sql = new TSqlUpdate;
    $sql->setEntity('filiais');
    $sql->setRowData('status', 1);
    $sql->setCriteria($criterio);
    $result = $conn->query($sql->getInstruction());

    if($result->rowCount()){
        $pop = new popup("Filial ativado com sucesso.<br>");
        $popup = $pop->get_pop();
    }else{
        $pop = new popup("Filial não pode ser ativado.<br>", 'vermelho');
        $popup = $pop->get_pop();
    }

    

    $sql = new TSqlSelect();
    $sql->setEntity('filiais');
    $sql->addColumn('*');
    $sql->setCriteria($criterio);

    $result = $conn->query($sql->getInstruction());

    if($result->rowCount()){
        extract($result->fetch(PDO::FETCH_ASSOC));
    }

    $op = 'editar';

}elseif($op=='visualizar'){

    if(!is_numeric($id))
        $id = base64_decode($id);

    $criterio = new TCriteria;
    $criterio->add(new TFilter('id', '=', $id));

    $sql = new TSqlSelect;
    $sql->setEntity('filiais');
    $sql->addColumn('*');
    $sql->setCriteria($criterio);
    $result = $conn->query($sql->getInstruction());

    if($result->rowCount()){
        extract($result->fetch(PDO::FETCH_ASSOC));
        $popup = null;
    }

    $op = 'editar';

}




$form = new form('Cadastrar Filial', 'cadastrar-filial.php', $op);
$form->id = $id;
$form->tabela = 'filiais';

$form->colspan = 12;
$form->max = 100;
if(isset($error['nome']))
    $form->error = true;
$form->set_arg('text', 'Nome', 'Preencha com um nome que possa ser identificado essa filial', $nome);


$form->colspan = 3;
$form->set_arg('text', 'Telefone 1', '(88) 0000.00000', $telefone_1, 'tel');
$form->colspan = 3;
$form->set_arg('text', 'Telefone 2', '(88) 0000.00000', $telefone_2, 'tel');
$form->colspan = 6;
$form->set_arg('email', 'E-mail', 'Email de contato desta filial', $email);

$form->colspan = 6;
$form->set_arg('text', 'Endereço', 'Rua, Av., Travessa, Logradouro...', $endereco);
$form->colspan = 4;
$form->set_arg('text', 'Bairro', 'Centro...', $bairro);
$form->colspan = 2;
$form->set_arg('text', 'Numero', 'Deixe em branco para sem numero', $numero, 'integer');


$sql = new TSqlSelect();
$sql->setEntity('localidade_estados');
$sql->addColumn('nome');
$sql->addColumn('sigla');
$sql->addColumn('cod_estados');
$result = $conn->query($sql->getInstruction());
$estados = null;
for($i=0; $i<$result->rowCount(); $i++){
    extract($result->fetch(PDO::FETCH_ASSOC));
    $estados[$i][] = $cod_estados;
    $estados[$i][] = $nome;
}

$criterio = new TCriteria();
$criterio->add(new TFilter('estados_cod_estados', '=', $estado));
$sql = new TSqlSelect();
$sql->setEntity('localidade_cidades');
$sql->addColumn('nome');
$sql->addColumn('cep');
$sql->addColumn('cod_cidades');
$sql->setCriteria($criterio);
$result = $conn->query($sql->getInstruction());
$cidades = null;
for($i=0; $i<$result->rowCount(); $i++){
    extract($result->fetch(PDO::FETCH_ASSOC));
    $cidades[$i][] = $cod_cidades;
    $cidades[$i][] = $nome;
}

$form->colspan = 4;
$cod = '
        function showCidade(valor){
            $.post("inc/ajax_inputECC.inc", {
                op : "cidade",
                id_estado : valor
            }, function(data) {
                if (data.length > 0) {
                    $("#cidade").html(data);
                    showCep($("select[name=\'cidade\']").val());
                }
            });
        }   ';
$form->set_js($cod, 'onchange="showCidade(this.value)"');
$form->div_id = 'estado';
$form->set_arg('select2', 'Estado', $estados, $estado);

$form->colspan = 4;
$cod = '
        function showCep(valor){
            $.post("inc/ajax_inputECC.inc", {
                op : "cep",
                id_cidade : valor
            }, function(data) {
                if (data.length > 0) {
                    $("#cep").html(data);
                }
            });
        }   ';
$form->set_js($cod, 'onchange="showCep(this.value)"');
$form->div_id = 'cidade';
$form->set_arg('select2', 'Cidade', $cidades, $cidade);

$criterio = new TCriteria();
$criterio->add(new TFilter('cod_cidades', '=', $cidade));
$sql = new TSqlSelect();
$sql->setEntity('localidade_cidades');
$sql->addColumn('cep');
$sql->setCriteria($criterio);
$result = $conn->query($sql->getInstruction());
if($result->rowCount()){
    extract($result->fetch(PDO::FETCH_ASSOC));
    $cep = $cep{0}.$cep{1}.$cep{2}.$cep{3}.$cep{4}."-".$cep{5}.$cep{6}.$cep{7};
}
$form->colspan = 4;
$form->div_id = 'cep';
$form->set_arg('text', 'CEP', '00000-000', $cep, 'cep');



$html = new template;
$cod = $html->set_box('Formulário de cadastramento de cliente', $form->get_form().$popup);
$html->set_html($cod);
$html->get_html('Cadastrar Cliente', 'Cadastre um novo cliente ao preencher esse formulário');

echo $form->mascara;

?>
