<?php

include 'inc/functions.inc.php';

is_logged_usuario();

extract($_POST);
extract($_GET);

if(isset($id))
    if(!is_numeric($id))
        $id = base64_decode($id);


$conn = TConnection::open(DB);



if(!isset($produto)){

    $op = 'pesquisar';
    
    //select estoques
    $criterio = new TCriteria();
    $criterio->add(new TFilter('contabilizar', '=', 1));
    $criterio->setProperty('order', 'nome');

    $sql = new TSqlSelect();
    $sql->setEntity('filiais');
    $sql->addColumn('id');
    $sql->addColumn('nome');
    $sql->setCriteria($criterio);

    $result = $conn->query($sql->getInstruction());
    $estoques[0][] = 0;
    $estoques[0][] = 'Todos os estoques';
    for($i=1; $i<=$result->rowCount(); $i++){
        extract($result->fetch(PDO::FETCH_ASSOC));

        $estoques[$i][] = $id;
        $estoques[$i][] = $nome;
    }

    //select produto
    $criterio = new TCriteria();
    $criterio->add(new TFilter('status', '=', 1));
    $criterio->setProperty('order', 'nome');

    $sql = new TSqlSelect();
    $sql->setEntity('produto');
    $sql->addColumn('id');
    $sql->addColumn('nome');
    $sql->setCriteria($criterio);

    $result = $conn->query($sql->getInstruction());
    $produtos[0][] = 0;
    $produtos[0][] = 'Todos os produtos';
    for($i=1; $i<=$result->rowCount(); $i++){
        extract($result->fetch(PDO::FETCH_ASSOC));

        $produtos[$i][] = $id;
        $produtos[$i][] = $nome;
    }

    $form = new form('Relatório Estoque', 'relatorio-estoque.php', $op, 'get');
    
    $form->colspan = 6;
    $form->set_arg('select', 'Produto', $produtos);

    $form->colspan = 6;
    $form->set_arg('select', 'Estoque', $estoques);

    $form->max = 10;
    $form->colspan = 6;
    $form->set_arg('text', 'Data inicio', 'dd/mm/aaaa', null, 'data');

    $form->max = 10;
    $form->colspan = 6;
    $form->set_arg('text', 'Data termino', 'dd/mm/aaaa', null, 'data');
    
    $cod_pag = $form->get_form();
    
}else{

    //selecionando estoque
    $criterio = new TCriteria;
    if($produto!=0)
        $criterio->add(new TFilter('produto_id', '=', $produto));
    if($estoque!=0)
        $criterio->add(new TFilter('estoque_id', '=', $estoque));
    
    $sql = new TSqlSelect;
    $sql->setEntity('produto_estoque_registro');
    $sql->addColumn('estoque_id');
    $sql->addColumn('produto_id');
    $sql->addColumn('quantidade');
    $sql->setCriteria($criterio);
    

    $result = $conn->query($sql->getInstruction());
    if($result->rowCount()){
        for($i=0; $i<$result->rowCount(); $i++){
            $entries[$i] = $result->fetch(PDO::FETCH_NUM);
            
            $entries[$i][0] = registro($entries[$i][0], 'filiais', 'nome');
            $entries[$i][1] = registro($entries[$i][1], 'produto', 'nome');
        }
        
    }else{
        $entries = null;
    }
    $column = array('Estoque','Produto', 'Quantidade');




    //selecionando log (se houver)
    $criterio = new TCriteria;
    if($data_inicio!='' and $data_termino!=''){    
        if($produto!=0){
            $criterio->add(new TFilter('produto_id', '=', $produto));
        }
        if($estoque!=0){
            $criterio->add(new TFilter('origem', '=', $estoque), TExpression::OR_OPERATOR);
            $criterio->add(new TFilter('destino', '=', $estoque), TExpression::OR_OPERATOR);
        }
        $criterio->add(new TFilter('data', '>=', formataDataInv($data_inicio).' 00:00:00'));
        $criterio->add(new TFilter('data', '<=', formataDataInv($data_termino).' 23:59:59'));
        
        $sql = new TSqlSelect;
        $sql->setEntity('produto_estoque');
        $sql->addColumn('*');
        $sql->setCriteria($criterio);

        $result = $conn->query($sql->getInstruction());
        if($result->rowCount()){
            for($i=0; $i<$result->rowCount(); $i++){
                $entries_2[$i] = $result->fetch(PDO::FETCH_NUM);
                $entries_2[$i][0] = '<a class="btn btn-primary" href="estoque-cadastrar.php?op=visualizar&id='.base64_encode($entries_2[$i][0]).'">'.$entries_2[$i][0].'</a>';

                $entries_2[$i][1] = registro($entries_2[$i][1], 'produto', 'nome');
                $entries_2[$i][3] = 'R$ '.real($entries_2[$i][3], 'real');
                $entries_2[$i][5] = registro($entries_2[$i][5], 'filiais', 'nome');
                $entries_2[$i][6] = registro($entries_2[$i][6], 'filiais', 'nome');
                $entries_2[$i][8] = formataData($entries_2[$i][8]);
                $entries_2[$i][9] = registro($entries_2[$i][9], 'usuario', 'nome');
                
            }
            
        }else{
            $entries_2 = null;
        }
        $column_2 = array('ID','Produto', 'Fornecedor', 'Preco Fornecedor', 'Cod Fornecedor', 'Origem', 'Destino', 'Total', 'Data', 'Usuário');

    }


    



    $table = new table('Estoques', $column, $entries);
    $script = $table->get_script();

    $cod_pag = $table->set_table();

    if($data_inicio!='' and $data_termino!=''){    
        $table2 = new table('Registros', $column_2, $entries_2);
        $script_2 = $table2->get_script();
        $cod_pag .= $table2->set_table();
    }

    
    
}







$html = new template;
if(!isset($produto)){
    $cod = $html->set_box('Relatório Estoque', $cod_pag);
}else{
    $cod = $cod_pag;
}
$html->set_html($cod);
$html->get_html('Relatório estoque', 'Relatório de estoque por estoque, data ou produto');


?>
<script type="text/javascript">
    $(function(){
        $("#id_data_inicio").datepicker();
    });
    $(function(){
        $("#id_data_termino").datepicker();
    });
</script>