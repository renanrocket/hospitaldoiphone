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
	
	$produto_id = $fornecedor = $preco_fornecedor = $codigo_fornecedor = $origem = $destino = $alerta_quantidade = $total = $balanco_total = $data = $usuario_id = null;
    
    $popup = null;
    $op = 'novo';
    $id = $idProdutoEstoque = 0;


}elseif($op=='novo' or $op=='editar'){
	
	$cont = count($_POST);
	$array = array_keys($_POST);
    $valida = true;
    

    $erro = 'Estoque do produto não cadastrado. <b>Motivo:</b><br>';
    //filtros
    if($produto_id==''){
        $valida = false;
        $erro.= 'Produto não selecionado.<br>';
        $error['produto_id'] = true;
    }
    if($origem==''){
        $valida = false;
        $erro.= 'Origem não selecionada.<br>';
        $error['origem'] = true;
    }
    if($destino==''){
        $valida = false;
        $erro.= 'Destino não selecionado.<br>';
        $error['destino'] = true;
    }
    if($origem == $destino){
        $valida = false;
        $erro.= 'Origem e destino não podem ser iguais.<br>';
        $error['origem'] = true;
        $error['destino'] = true;
    }
    if($total=='' or $total==0){
        $valida = false;
        $erro.= 'Total não informado.<br>';
        $error['total'] = true;
    }
    

    if($valida){
        
        $array_permitido = array('produto_id', 'fornecedor', 'preco_fornecedor', 'origem', 'destino', 'total');

        if($op=='novo'){

            $sql = new TSqlInsert();
            $sql->setEntity('produto_estoque');
            for($i=$check=0; $i<$cont; $i++){
                if(in_array($array[$i], $array_permitido)){
                    if($array[$i]=='preco_fornecedor'){
                        $sql->setRowData($array[$i], real($_POST[$array[$i]], 'float'));
                    }else{
                        $sql->setRowData($array[$i], $_POST[$array[$i]]);
                    }
                }
            }
            $sql->setRowData('data', date('Y-m-d h:i:s'));
            $user = new usuario;
            $sql->setRowData('usuario_id', $user->get_usuario('id') );  
            
            $result = $conn->query($sql->getInstruction());

            if($result->rowCount()){

                $idProdutoEstoque = $conn->lastInsertId();

                $pop = new popup(
                "Resgistro de estoque cadastrado com sucesso.");
                $popup = $pop->get_pop();

                $op = 'editar';
                $id = $idProdutoEstoque;

                //subtrair estoque
                $contabilizar_origem = registro($_POST['origem'], 'filiais', 'contabilizar');
                if($contabilizar_origem){
                    //pegando o ultimo registro do balanco total
                    $criterio = new TCriteria();
                    $criterio->add(new TFilter('produto_id', '=', $_POST['produto_id']));
                    $criterio->add(new TFilter('estoque_id', '=', $_POST['origem']));

                    $sql = new TSqlSelect();
                    $sql->setEntity('produto_estoque_registro');
                    $sql->addColumn('id as id_produto_estoque_registro');
                    $sql->addColumn('quantidade');
                    $sql->setCriteria($criterio);

                    $result = $conn->query($sql->getInstruction());

                    if($result->rowCount()){
                        extract($result->fetch(PDO::FETCH_ASSOC));

                        $criterio = new TCriteria();
                        $criterio->add(new TFilter('id', '=', $id_produto_estoque_registro));

                        $sql = new TSqlUpdate();
                        $sql->setEntity('produto_estoque_registro');
                        $sql->setRowData('quantidade', ($quantidade - $_POST['total']));
                        $result = $conn->query($sql->getInstruction());
                    }else{
                        $sql = new TSqlInsert();
                        $sql->setEntity('produto_estoque_registro');
                        $sql->setRowData('produto_id', $_POST['produto_id']);
                        $sql->setRowData('estoque_id', $_POST['origem']);
                        $sql->setRowData('quantidade', (-1 * $_POST['total']));
                        $result = $conn->query($sql->getInstruction());
                    }

                }

                //somar estoque
                $contabilizar_destino = registro($_POST['destino'], 'filiais', 'contabilizar');
                if($contabilizar_destino){
                    //pegando o ultimo registro do balanco total
                    $criterio = new TCriteria();
                    $criterio->add(new TFilter('produto_id', '=', $_POST['produto_id']));
                    $criterio->add(new TFilter('estoque_id', '=', $_POST['destino']));

                    $sql = new TSqlSelect();
                    $sql->setEntity('produto_estoque_registro');
                    $sql->addColumn('id as id_produto_estoque_registro');
                    $sql->addColumn('quantidade');
                    $sql->setCriteria($criterio);

                    $result = $conn->query($sql->getInstruction());

                    if($result->rowCount()){
                        extract($result->fetch(PDO::FETCH_ASSOC));

                        $criterio = new TCriteria();
                        $criterio->add(new TFilter('id', '=', $id_produto_estoque_registro));

                        $sql = new TSqlUpdate();
                        $sql->setEntity('produto_estoque_registro');
                        $sql->setRowData('quantidade', ($quantidade + $_POST['total']));
                        $result = $conn->query($sql->getInstruction());
                    }else{
                        $sql = new TSqlInsert();
                        $sql->setEntity('produto_estoque_registro');
                        $sql->setRowData('produto_id', $_POST['produto_id']);
                        $sql->setRowData('estoque_id', $_POST['destino']);
                        $sql->setRowData('quantidade', $_POST['total']);
                        $result = $conn->query($sql->getInstruction());
                    }

                }
                                              

            }else{

                $pop = new popup(
                "Não foi possível cadastrar o Registro de Estoque.<br>
                Motivo: " . var_dump($conn->errorInfo()), 'vermelho');
                $popup = $pop->get_pop();

            }
     
       

        }elseif($op=='editar'){


            
            if(!is_numeric($id))
                $id = base64_decode($id);

            $criterio = new TCriteria;
            $criterio->add(new TFilter('id', '=', $id));
            $idProduto = $id;

            $sql = new TSqlUpdate;
            $sql->setEntity('produto_estoque');
            for($i=$check=0; $i<$cont; $i++){
                if(in_array($array[$i], $array_permitido)){
                    if($array[$i]=='preco_fornecedor'){
                        $sql->setRowData($array[$i], real($_POST[$array[$i]], 'float'));
                    }else{
                        $sql->setRowData($array[$i], $_POST[$array[$i]]);
                    }
                }
            }
            $sql->setCriteria($criterio);
            
            $result = $conn->query($sql->getInstruction());
            
            if($result->rowCount()){
                
                $pop = new popup(
                "Registro de Estoque editado com sucesso.");
                $popup = $pop->get_pop();

                $op = 'editar';
                $id = $idProdutoEstoque;


            }else{
              
                $pop = new popup(
                "Não foi possível editar o Resgistro de Estoque.<br>
                Motivo: " . implode(' | ', $conn->errorInfo()), 'amarelo');
                $popup = $pop->get_pop();
                
            }

        }



    }else{
        $pop = new popup($erro, 'vermelho');
        $popup = $pop->get_pop();
        if($op=='novo'){
            $id = $idProdutoEstoque = 0;
        }else{
            $idProdutoEstoque = $id;
        }
    }
        




}elseif($op=='deletar' or $op=="visualizar"){

    $produto_id = $fornecedor = $preco_fornecedor = $codigo_fornecedor = $origem = $destino = $alerta_quantidade = $total = $balanco_total = $data = $usuario_id = null;


    if(!is_numeric($id))
        $id = base64_decode($id);

    if($op=='deletar'){
        
        $criterio = new TCriteria;
        $criterio->add(new TFilter('id', '=', $id));
        
        $sql = new TSqlUpdate();
        $sql->setEntity('produto');
        if($op=="deletar"){
            $sql->setRowData('status', '0');
        }elseif($op=="ativar"){
            $sql->setRowData('status', '1');
        }
        $sql->setCriteria($criterio);
        $result = $conn->query($sql->getInstruction());

        if($result->rowCount()){
            $pop = new popup("Produto $operacao com sucesso.<br>");
            $popup = $pop->get_pop();
        }else{
            $pop = new popup("Produto não pode ser $operacao.<br>", 'vermelho');
            $popup = $pop->get_pop();
        }
    }

    $criterio = new TCriteria();
    $criterio->add(new TFilter('id', '=', $id));

    $sql = new TSqlSelect();
    $sql->setEntity('produto');
    $sql->addColumn('*');
    $sql->setCriteria($criterio);
    $result = $conn->query($sql->getInstruction());

    if($result->rowCount()){
        extract($result->fetch(PDO::FETCH_ASSOC));

        $idProdutoEstoque = $id;

        $preco_fornecedor = real($preco_fornecedor, 'real');
    }

    if($op=="visualizar")
        $popup = null;
    if($op!="deletar")
        $op = 'novo';

}
$op = 'novo';

//preparando select produtos
$criterio = new TCriteria();
$criterio->add(new TFilter('status', '=', 1));
$criterio->setProperty('order', 'nome');

$sql = new TSqlSelect();
$sql->setEntity('produto');
$sql->addColumn('id');
$sql->addColumn('nome');
$sql->setCriteria($criterio);
$result = $conn->query($sql->getInstruction());

$produtos[0][] = '';
$produtos[0][] = '--';

for($i = 1; $i <= $result->rowCount(); $i++){

    extract($result->fetch(PDO::FETCH_ASSOC));
    $produtos[$i][] = $id;
    $produtos[$i][] = $nome;

}

//preparando select origem destino
$criterio = new TCriteria();
$criterio->setProperty('order', 'nome');

$sql = new TSqlSelect();
$sql->setEntity('filiais');
$sql->addColumn('id');
$sql->addColumn('nome as od');
$sql->setCriteria($criterio);
$result = $conn->query($sql->getInstruction());

$ods[0][] = '';
$ods[0][] = '--';

for($i = 1; $i <= $result->rowCount(); $i++){

    extract($result->fetch(PDO::FETCH_ASSOC));
    $ods[$i][] = $id;
    $ods[$i][] = $od;

}

//preparando ajax fornecedores
$sql = new TSqlSelect();
$sql->setEntity('produto_estoque');
$sql->addColumn('fornecedor as fornecedores');
$sql->addColumn('codigo_fornecedor as fornecedores_codigo');

$result = $conn->query($sql->getInstruction());

$fornecedoresNomes = $fornecedoresCodigo = array();

for($i=0; $i<$result->rowCount(); $i++){
    extract($result->fetch(PDO::FETCH_ASSOC));
    $fornecedoresNomes[] = $fornecedores;
    $fornecedoresCodigo[] = $fornecedores_codigo;
}

if($result->rowCount()){
    //todos os arrays unicos dos ajax da pagina
    $fornecedoresNomes = array_unique($fornecedoresNomes);
    $fornecedoresCodigo = array_unique($fornecedoresCodigo);

}


$form = new form('Registro de Estoque', 'cadastrar-estoque.php', $op);
$form->id = $idProdutoEstoque;
$form->tabela = 'produto_estoque';

$form->set_js(null, 'onchange="produto(this.value);"', false);
$form->colspan = 3;
if(isset($error['produto_id']))
    $form->error = true;
$form->set_arg('select', 'Produto id', $produtos, $produto_id);

$form->colspan = 3;
$form->max = 250;
$form->ajax_valores = array_values($fornecedoresNomes);
$form->set_arg('ajaxtext', 'Fornecedor', 'Fornecedor deste produto', $fornecedor);

$form->colspan = 3;
$form->max = 6;
$form->set_arg('dinheiro', 'Preco Fornecedor', '00,00', real($preco_fornecedor, 'real'), 'Valor2');

$form->colspan = 3;
$form->max = 250;
$form->ajax_valores = array_values($fornecedoresCodigo);
$form->set_arg('ajaxtext', 'Codigo fornecedor', 'Codigo deste produto no fornecedor', $codigo_fornecedor);

$form->colspan = 5;
if(isset($error['origem']))
    $form->error = true;
$form->tooltip = 'De onde está saindo este produto.';
$form->set_arg('select', 'Origem', $ods, $origem);

$form->colspan = 2;
$form->set_arg_generic('<center><span class="fa fa-arrow-right" style="font-size: 50px; display: inline-block;"></span></center>');

$form->colspan = 5;
if(isset($error['destino']))
    $form->error = true;
$form->tooltip = 'Para onde vai este produto.';
$form->set_arg('select', 'Destino', $ods, $destino);

$form->colspan = 12;
if(isset($error['total']))
    $form->error = true;
$form->max = 6;
$form->input_group_addon_back = 'Und';
$form->set_arg('text', 'Total', 'Total a ser transferido da origem para o destino', $total, 'Integer');


$form->button_ativar_deletar = false;




$html = new template;
$cod = $html->set_box('Formulário registro de estoque', $form->get_form().$popup);
$html->set_html($cod);
$html->get_html('Registro de estoque', 'Registre a entrada ou saida de um produto no estoques');

echo $form->mascara;

?>


<script type="text/javascript">
    
    function produto(valor){

        console.log(valor);

        $.post('inc/ajax_produto_estoque.inc.php', {
            produto_id: valor
        }).done(function(data){
            console.log(data);
            var arr = $.parseJSON(data);

            if(arr['fornecedor'] != ''){
                $('input[name="fornecedor"]').val(arr['fornecedor']);
            }
            if(arr['preco_fornecedor'] != ''){
                $('input[name="preco_fornecedor"]').val(arr['preco_fornecedor']);
            }
            if(arr['codigo_fornecedor'] != ''){
                $('input[name="codigo_fornecedor"]').val(arr['codigo_fornecedor']);
            }
            
        });

    }

    
    
</script>