<?php

include 'inc/functions.inc.php';

is_logged_usuario();

extract($_POST);
extract($_GET);

if(isset($id))
    if(!is_numeric($id))
        $id = base64_decode($id);


$conn = TConnection::open(DB);

define('MAX_ARQUIVO_ENVIADO_CLIENTE', 6);
define('MAX_ARQUIVO_APROVACAO', 6);


if(!isset($op)){

    $cliente_id = $nome = $wpp = $telefone = $email = $instagram = $cpf = $total = null;

    $itens_qtd = 1;
   
    $op = 'novo';
    $id = $idVenda = 0;


}elseif($op=='novo' or $op=='editar'){


    $permitidos = array('cliente_id', 'nome', 'wpp', 'telefone', 'email', 'instagram', 'cpf', 'total');

    for($i=1 ; $i<$itens_qtd ; $i++){
        $var_preco = 'preco_'.$i;
        $var_desconto = 'desconto_'.$i;
        $var_total = 'total_'.$i;
        $$var_preco = real($$var_preco, 'float');
        $$var_desconto = real($$var_desconto, 'float');
        $$var_total = real($$var_total, 'float');    
    }
    $total = real($total, 'float');

    $args[] = array('email', '=', $_COOKIE['email']);
    $usuario = usuario_get($args);

    
    $conn->beginTransaction();

    if($op=='novo'){

        //inserindo venda
        $sql = new TSqlInsert();
        $sql->setEntity('venda');
        foreach ($permitidos as $key => $value) {
            $sql->setRowData($value, $$value);
        }
        $sql->setRowData('data', date('Y-m-d'));
        $sql->setRowData('usuario_id', $usuario[0]['id']);
        $sql->setRowData('status', $tipo);
        $sql->setRowData('filial_id', $_COOKIE['filial']);
        $result = $conn->query($sql->getInstruction());

        if($result->rowCount()){
            
            $id = $idVenda = $conn->lastInsertId();

            //inserindo conta
            $sql = new TSqlInsert();
            $sql->setEntity('conta');
            $sql->setRowData('recebemos_pagamos', $nome);
            $sql->setRowData('tabela_referente', 'venda');
            $sql->setRowData('referido', $idVenda);
            $sql->setRowData('status', $tipo);//se tipo = 1 então vai ser uma conta a receber do contrario tipo 3 sera uma conta aguardando venda.
            $sql->setRowData('valor', $total);
            $sql->setRowData('forma_pagamento', 1);
            $sql->setRowData('parcelas', 1);
            $sql->setRowData('data', date('Y-m-d'));
            $sql->setRowData('usuario_id', $usuario[0]['id']);
            $sql->setRowData('filial_id', $_COOKIE['filial']);
            $result = $conn->query($sql->getInstruction());


            //inserindo conta_itens
            if($result->rowCount()){
                $idConta = $conn->lastInsertId();
                $sql = new TSqlInsert();
                $sql->setEntity('conta_itens');
                $sql->setRowData('conta_id', $idConta);
                $sql->setRowData('tipo_pagamento', 1);
                $sql->setRowData('valor', $total);
                $sql->setRowData('data_vencimento', date('Y-m-d'));
                $sql->setRowData('entrada_saida', 1);
                $sql->setRowData('usuario_id', $usuario[0]['id']);
                $result = $conn->query($sql->getInstruction());

                $alterar_conta = false;

            }
            
        }


    }elseif($op=='editar'){


        if(!is_numeric($id))
            $idVenda = base64_decode($id);
        $idVenda = $id;

        $criterio = new TCriteria();
        $criterio->add(new TFilter('id', '=', $idVenda));

        //autalizando venda
        $sql = new TSqlUpdate();
        $sql->setEntity('venda');
        foreach ($permitidos as $key => $value) {
            $sql->setRowData($value, $$value);
        }
        $result = $conn->query($sql->getInstruction());



        if(!$result->rowCount()){//se os valores de totais da venda não for alterado então não altera a conta
            $alterar_conta = false;
        }else{// se os valores totais da venda for alterado então altera a conta

            $criterio = new TCriteria();
            $criterio->add(new TFilter('tabela_referente', '=', 'venda'));
            $criterio->add(new TFilter('referido', '=', $idVenda));

            $sql = new TSqlSelect();
            $sql->setEntity('conta');
            $sql->addColumn('id as idConta');
            $sql->setCriteria($criterio);
            
            $result = $conn->query($sql->getInstruction());

            //inserindo a conta caso não exista
            if(!$result->rowCount()){
                //inserindo conta
                $sql = new TSqlInsert();
                $sql->setEntity('conta');
                $sql->setRowData('recebemos_pagamos', $nome);
                $sql->setRowData('tabela_referente', 'venda');
                $sql->setRowData('referido', $idVenda);
                $sql->setRowData('status', $tipo);//se tipo = 1 então vai ser uma conta a receber do contrario tipo 3 sera uma conta aguardando venda.
                $sql->setRowData('valor', $total);
                $sql->setRowData('forma_pagamento', 1);
                $sql->setRowData('parcelas', 1);
                $sql->setRowData('data', date('Y-m-d'));
                $sql->setRowData('usuario_id', $usuario[0]['id']);
                $sql->setRowData('filial_id', $_COOKIE['filial']);
                $result = $conn->query($sql->getInstruction());


                //inserindo conta_itens
                if($result->rowCount()){
                    $idConta = $conn->lastInsertId();
                    $sql = new TSqlInsert();
                    $sql->setEntity('conta_itens');
                    $sql->setRowData('conta_id', $idConta);
                    $sql->setRowData('tipo_pagamento', 1);
                    $sql->setRowData('valor', $total);
                    $sql->setRowData('data_vencimento', date('Y-m-d'));
                    $sql->setRowData('entrada_saida', 1);
                    $sql->setRowData('usuario_id', $usuario[0]['id']);
                    $result = $conn->query($sql->getInstruction());

                    $alterar_conta = false;

                }
            }else{
                extract($result->fetch(PDO::FETCH_ASSOC));
                $alterar_conta = true;    
            }
            
        }

        if($alterar_conta){

            
            //editando a conta
            $criterio = new TCriteria();
            $criterio->add(new TFilter('tabela_referente', '=', 'venda'));
            $criterio->add(new TFilter('referido', '=', $idVenda));

            $sql = new TSqlUpdate();
            $sql->setEntity('conta');
            $sql->setRowData('recebemos_pagamos', $nome);
            $sql->setRowData('valor', $total);
            $sql->setRowData('data', date('Y-m-d'));
            $sql->setRowData('usuario_id', $usuario[0]['id']);
            $sql->setCriteria($criterio);
            
            $result = $conn->query($sql->getInstruction());


            //deletando conta itens
            $criterio = new TCriteria();
            $criterio->add(new TFilter('conta_id', '=', $idConta));

            $sql = new TSqlDelete();
            $sql->setEntity('conta_itens');
            $sql->setCriteria($criterio);
            $result = $conn->query($sql->getInstruction());
        
            
        }

    }

    //alterando cliente (se houver dados para alteração)
    if($cliente_id){
        $criterio = new TCriteria();
        $criterio->add(new TFilter('id', '=', $cliente_id));

        $sql = new TSqlUpdate();
        $sql->setEntity('cliente');
        $sql->setRowData('nome', $nome);
        $sql->setRowData('wpp', $wpp);
        $sql->setRowData('telefone', $telefone);
        $sql->setRowData('email', $email);
        $sql->setRowData('instagram', $instagram);
        $sql->setRowData('cpf', $cpf);
        $sql->setCriteria($criterio);

        $result = $conn->query($sql->getInstruction());
    }

    if($alterar_conta){
        //inserindo conta itens
        $sql = new TSqlInsert();
        $sql->setEntity('conta_itens');
        $sql->setRowData('conta_id', $idConta);
        $sql->setRowData('tipo_pagamento', 1);
        $sql->setRowData('valor', $total);
        $sql->setRowData('data_vencimento', date('Y-m-d'));
        $sql->setRowData('entrada_saida', 1);
        $sql->setRowData('usuario_id', $usuario[0]['id']);
        $result = $conn->query($sql->getInstruction());
    }


    //alterando dados da venda_itens
    $criterio = new TCriteria();
    $criterio->add(new TFilter('venda_id', '=', $idVenda));
    $sql = new TSqlDelete();
    $sql->setEntity('venda_itens');
    $sql->setCriteria($criterio);
    $result = $conn->query($sql->getInstruction());

    $permitidos = array('cliente_id', 'nome', 'wpp', 'telefone', 'email', 'instagram', 'cpf');
    $idItens = array();
    for($i=1; $i<=$itens_qtd; $i++){

        $var_item_id = 'item_id_'.$i;
        $var_tipo_item = 'tipo_item_'.$i;
        $var_item = 'item_'.$i;
        $var_observacao = 'observacao_'.$i;
        $var_quantidade = 'quantidade_'.$i;
        $var_garantia = 'garantia_'.$i;
        $var_preco = 'preco_'.$i;
        $var_desconto = 'desconto_'.$i;
        $var_total = 'total_'.$i;

        /*
        $$var_preco = real($$var_preco, 'float');
        $$var_desconto = real($$var_desconto, 'float');
        $$var_total = real($$var_total, 'float');
        */
        $$var_desconto = floatval($$var_desconto);
        $$var_preco = floatval($$var_preco);
        $$var_total = floatval($$var_total);
    
        //se não for uma venda então so inserir os dados na venda itens sem ordem de servico

        $sql = new TSqlInsert();
        $sql->setEntity('venda_itens');
        $sql->setRowData('venda_id', $idVenda);
        $sql->setRowData('item_id', $$var_item_id);
        $sql->setRowData('servico_produto', $$var_tipo_item);
        $sql->setRowData('item', $$var_item);
        $sql->setRowData('observacao', $$var_observacao);
        $sql->setRowData('quantidade', $$var_quantidade);
        $sql->setRowData('garantia', $$var_garantia);
        $sql->setRowData('preco', $$var_preco);
        $sql->setRowData('desconto', $$var_desconto);
        $sql->setRowData('total', $$var_total);
        $result = $conn->query($sql->getInstruction());


        


    }
    

    //se o tipo == 1 e a op = editar então essa variavel pode contar registros de ordens de serviço
    //que precisao ser cencelados e retirado a chave estrangeira da venda
    if(count($idItens)){

        $criterio = new TCriteria();
        $criterio->add(new TFilter('venda_id', '=', $idVenda));
        for($i=0; $i<count($idItens); $i++){
            $criterio->add(new TFilter('id', '!=', $idItens[$i]));
        }

        $sql = new TSqlSelect();
        $sql->setEntity('venda_itens');
        $sql->addColumn('ordem_de_servico_id');
        $sql->setCriteria($criterio);

        $result = $conn->query($sql->getInstruction());

        //cancelando as ordens de serviço e retirando a chave estrangeira da venda
        for($i=0; $i<$result->rowCount(); $i++){
            extract($result->fetch(PDO::FETCH_ASSOC));
            $criterio2 = new TCriteria();
            $criterio2->add(new TFilter('id', '=', $ordem_de_servico_id));

            $sql = new TSqlUpdate();
            $sql->setEntity('ordem_de_servico');
            $sql->setRowData('status', 1);//status = 1 significa ordem de serviço cancelada
            $sql->setRowData('venda_id', 0);//retirando a chave estrangeira, esta OS não pertence mais a este pedido
            $sql->setCriteria($criterio2);

            $result2 = $conn->query($sql->getInstruction());

        }

        //deletando os sub itens da venda (venda_itens)
        $sql = new TSqlDelete();
        $sql->setEntity('venda_itens');
        $sql->setCriteria($criterio);//usa o criterio setado no começo desse IF
        $result = $conn->query($sql->getInstruction());


    }

    $op = 'editar';

    for($i=1 ; $i<=$itens_qtd ; $i++){
        $var_preco = 'preco_'.$i;
        $var_desconto = 'desconto_'.$i;
        $var_total = 'total_'.$i;
        $$var_preco = real($$var_preco, 'real');
        $$var_desconto = real($$var_desconto, 'real');
        $$var_total = real($$var_total, 'real');
    }
    
    $total = real($total, 'real');



    if($conn->inTransaction()){
        $conn->commit();
    }


}elseif($op=="converter" or $op=="visualizar"){

    $cliente_id = $nome = $wpp = $telefone = $email = $instagram = $cpf = $fornecedor = $ordem_de_servico_prioritaria = $aprovacao_da_arte_pelo = $servico_deve_vir = $cor_do_material = $cor_da_impressao = $informacoes_sobre_o_servico = $produto_id = $produto = $quantidade = $sub_total = $desconto_em_porcentagem = $desconto_em_reais = $total = $data_de_finalizacao = $data_do_evento = $hora_do_evento = null;

    $status = 1;
    
    if(!isset($id)){
        $idOS = base64_decode($idOS);

        $criterio = new TCriteria();
        $criterio->add(new TFilter('ordem_de_servico_id', '=', $idOS));
    }else{
        if(!is_numeric($id))
            $idVenda = base64_decode($id);
        $idVenda = $id;

        $criterio = new TCriteria();
        $criterio->add(new TFilter('id', '=', $idVenda));
    }

    

    $sql = new TSqlSelect();
    $sql->setEntity('venda');
    $sql->addColumn('*');
    $sql->setCriteria($criterio);
    $result = $conn->query($sql->getInstruction());
    if($result->rowCount()){
        extract($result->fetch(PDO::FETCH_ASSOC));
        !isset($idVenda) ? $idVenda = $id : false;

        $criterio = new TCriteria();
        $criterio->add(new TFilter('venda_id', '=', $idVenda));
        $sql = new TSqlSelect();
        $sql->setEntity('venda_itens');
        $sql->addColumn('*');
        $sql->setCriteria($criterio);
        $result = $conn->query($sql->getInstruction());

        $itens_qtd = $result->rowCount();
        $tipo = $status;
        $total = real($total, 'real');


        if($op == "converter"){
            
            $conn->beginTransaction();

            if($tipo == 1){
                //transofrmar pedido em orçamento
                //cancelar todas as ordem deserviço
                //converter conta de a receber pra aguardando venda

                //transformando em orçamento
                $criterio = new TCriteria();
                $criterio->add(new TFilter('id', '=', $idVenda));

                $sql = new TSqlUpdate();
                $sql->setEntity('venda');
                $sql->setRowData('status', 3);
                $sql->setCriteria($criterio);

                $result = $conn->query($sql->getInstruction());

                //convertendo contas para aguardando venda
                $criterio = new TCriteria();
                $criterio->add(new TFilter('tabela_referente', '=', 'venda'));
                $criterio->add(new TFilter('referido', '=', $idVenda));

                $sql = new TSqlUpdate();
                $sql->setEntity('conta');
                $sql->setRowData('status', 3);
                $sql->setCriteria($criterio);

                $result = $conn->query($sql->getInstruction());

                $tipo = 3;




            }elseif($tipo == 3){
                //transformar orçamento em pedido
                //criar ordem de serviço
                //converter conta de aguardando venda para a receber

                //transformando em pedido
                $criterio = new TCriteria();
                $criterio->add(new TFilter('id', '=', $idVenda));

                $sql = new TSqlUpdate();
                $sql->setEntity('venda');
                $sql->setRowData('status', 1);
                $sql->setCriteria($criterio);

                $result = $conn->query($sql->getInstruction());

                //criando as ordens de serviço
                //selecionando todos os itens do pedido e criando uma OS de cada item
                /*
                $criterio = new TCriteria();
                $criterio->add(new TFilter('venda_id', '=', $idVenda));

                $sql = new TSqlSelect();
                $sql->setEntity('venda_itens');
                $sql->addColumn('id as venda_itens_id');
                $sql->addColumn('item_id');
                $sql->addColumn('item');
                $sql->addColumn('observacao');
                $sql->addColumn('quantidade');
                $sql->addColumn('garantia');
                $sql->addColumn('preco');
                $sql->addColumn('desconto');
                $sql->addColumn('total as item_total');
                $sql->setCriteria($criterio);

                $result_itens = $conn->query($sql->getInstruction());
                
                $args[] = array('email', '=', $_COOKIE['email']);
                $usuario = usuario_get($args);

                $permitidos = array('cliente_id', 'nome', 'wpp', 'telefone', 'email', 'instagram', 'cpf');
                for($i=1; $i<=$result_itens->rowCount(); $i++){

                    extract($result_itens->fetch(PDO::FETCH_ASSOC));

                    //verifica se a ordem de serviço já existe para não duplica-la
                    $criterio = new TCriteria();
                    $criterio->add(new TFilter('venda_id', '=', $idVenda));
                    $criterio->add(new TFilter('produto', '=', $item));
                    $criterio->add(new TFilter('produto_id', '=', $item_id));

                    $sql = new TSqlSelect();
                    $sql->setEntity('ordem_de_servico');
                    $sql->addColumn('*');
                    $sql->setCriteria($criterio);

                    $result = $conn->query($sql->getInstruction());

                    if(!$result->rowCount()){//se a ordem de serviço não existe então cria ela aqui
                        $sql = new TSqlInsert();
                        $sql->setEntity('ordem_de_servico');
                        foreach ($permitidos as $key => $value) {
                            $sql->setRowData($value, $$value);
                        }
                        $sql->setRowData('data_de_abertura', date('Y-m-d'));
                        $sql->setRowData('produto_id', $item_id);
                        $sql->setRowData('produto', $item);
                        $sql->setRowData('quantidade', $quantidade);
                        $sql->setRowData('venda_id', $idVenda);
                        $sql->setRowData('sub_total', $preco);
                        $sql->setRowData('desconto_em_porcentagem', $desconto);
                        $sql->setRowData('desconto_em_reais', ($preco-$item_total));
                        $sql->setRowData('total', $item_total);
                        $sql->setRowData('informacoes_sobre_o_servico', $observacao);
                        $sql->setRowData('status', 2); //status 2 aguardando revisao
                        $sql->setRowData('usuario_id', $usuario[0]['id']);

                        $result = $conn->query($sql->getInstruction());

                        //se foi criado a ordem de serviço então atualiza os itens do pedido com as respectivas chaves estrangeiras
                        $os_id = $conn->lastInsertId();

                        $criterio = new TCriteria();
                        $criterio->add(new TFilter('id', '=', $venda_itens_id));

                        $sql = new TSqlUpdate();
                        $sql->setEntity('venda_itens');
                        $sql->setRowData('ordem_de_servico_id', $os_id);
                        $sql->setCriteria($criterio);

                        $result = $conn->query($sql->getInstruction());

                    }else{//se a ordem de servico existe então mudar o estatus dela para revisão

                        $criterio = new TCriteria();
                        $criterio->add(new TFilter('venda_id', '=', $idVenda));

                        $sql = new TSqlUpdate();
                        $sql->setEntity('ordem_de_servico');
                        $sql->setRowData('status', 2);
                        $sql->setCriteria($criterio);

                        $result = $conn->query($sql->getInstruction());
                    }

                }
                */

                //convertendo contas para a receber
                $criterio = new TCriteria();
                $criterio->add(new TFilter('tabela_referente', '=', 'venda'));
                $criterio->add(new TFilter('referido', '=', $idVenda));

                $sql = new TSqlUpdate();
                $sql->setEntity('conta');
                $sql->setRowData('status', 1);
                $sql->setCriteria($criterio);

                $result = $conn->query($sql->getInstruction());

                $tipo = 1;


            }

            if($conn->inTransaction()){
                $conn->commit();
            }
        }


    }

    if(isset($idOS)){
        $criterio = new TCriteria();
        $criterio->add(new TFilter('id', '=', $idOS));
        $sql = new TSqlSelect();
        $sql->setEntity('ordem_de_servico');
        $sql->addColumn('nome');
        $sql->addColumn('wpp');
        $sql->addColumn('telefone');
        $sql->addColumn('email');
        $sql->addColumn('instagram');
        $sql->addColumn('cpf');
        $sql->setCriteria($criterio);
        $result = $conn->query($sql->getInstruction());
        if($result->rowCount()){
            extract($result->fetch(PDO::FETCH_ASSOC));
        }
        $itens_qtd == 0 ? $itens_qtd = 1 : false;

    }

    
    $op = 'editar';

}


if(!isset($tipo)){

    $cod = '
        <style>
            /* FROM HTTP://WWW.GETBOOTSTRAP.COM
             * Glyphicons
             *
             * Special styles for displaying the icons and their classes in the docs.
             */

            .bs-glyphicons {
                padding-left: 0;
                padding-bottom: 1px;
                margin-bottom: 20px;
                list-style: none;
                overflow: hidden;
            }

            .bs-glyphicons li {
                cursor: pointer;
                display: inline-block;
                width: 25%;
                height: 115px;
                padding: 10px;
                margin: 0px -5px -1px 0;
                font-size: 12px;
                line-height: 1.4;
                text-align: center;
                border: 1px solid #ddd;
            }

            .bs-glyphicons .fa {
                margin-top: 5px;
                margin-bottom: 10px;
                font-size: 60px;
            }

            .bs-glyphicons .glyphicon-class {
                display: block;
                text-align: center;
                word-wrap: break-word; /* Help out IE10+ with class names */
            }

            .bs-glyphicons li:hover {
                background-color: rgba(86, 61, 124, .1);
            }

            @media (min-width: 768px) {
                .bs-glyphicons li {
                width: 12.5%;
                }
            }
        </style>
        <center>
            <div id="glyphicons">
                <ul class="bs-glyphicons">
                  <li onclick="location.href=\'cadastrar-venda.php?tipo=3\'">
                    <span class="fa fa-navicon"></span>
                    <span class="glyphicon-class">Orçamento</span>
                  </li>
                  <li onclick="location.href=\'cadastrar-venda.php?tipo=1\'">
                    <span class="fa fa-file-text-o"></span>
                    <span class="glyphicon-class">Pedido</span>
                  </li>
                </ul>
            </div>
        </center>';



    $html = new template;
    $cod = $html->set_box('Escolha o que deseja cadastrar', $cod);
    $html->set_html($cod);
    $html->get_html('Cadastrar Orçamento ou Pedido', 'Selecione entre orçamento ou pedido.');

    $pop_filtro = new popup('<span id="filtro_alerta"></span>', 'vermelho');
    $pop_filtro->display = 'none';
    $popup = $pop_filtro->get_pop();


}else{

    //preparando todos os ajax da pagina num unico select para otimizar a velocidade

    $criterio = new TCriteria();
    $criterio->add(new TFilter('status', '=', 1));
    $criterio->setProperty('group', 'nome, email');


    $sql = new TSqlSelect();
    $sql->setEntity('cliente');
    $sql->addColumn('id as clienteId');
    $sql->addColumn('nome as clienteNome');
    $sql->addColumn('email as clienteEmail');
    $sql->addColumn('wpp as clienteWpp');
    $sql->addColumn('telefone as clienteTelefone');
    $sql->addColumn('instagram as clienteInstagram');
    $sql->addColumn('cpf as clienteCpf');
    $sql->setCriteria($criterio);


    $result = $conn->query($sql->getInstruction());

    $autocomplete = array();
    $index = array('value', 'cliente_id', 'email', 'wpp', 'telefone', 'instagram', 'cpf');

    for($i=0; $i<$result->rowCount(); $i++){
        extract($result->fetch(PDO::FETCH_ASSOC));

        $autocomplete[] = '{ '.$index[0].': "'.$clienteNome.'", '.$index[1].': '.$clienteId.', '.$index[2].': "'.$clienteEmail.'", '.$index[3].': "'.$clienteWpp.'", '.$index[4].': "'.$clienteTelefone.'", '.$index[5].': "'.$clienteInstagram.'", '.$index[6].': "'.$clienteCpf.'" }';

    }

    function select_array($array){
        $array2 = array();
        foreach ($array as $key => $value) {
            $array2[] = array($value, $value);
        }
        return $array2;
    }

    $hidden = $tipo;
    if($tipo==3){
        $tipo = 'Orçamento';
    }else{
        $tipo = 'Pedido';
    }


    $form = new form('Cadastrar '.$tipo, 'cadastrar-venda.php', $op);
    $form->id = $idVenda;
    $form->tabela = 'venda';
    $form->set_arg('hidden', 'tipo', null, $hidden);

    if($op=='editar'){
        $wpplink = str_replace('(', '', $wpp);
        $wpplink = str_replace(')', '', $wpplink);
        $wpplink = str_replace(' ', '', $wpplink);
        $wpplink = str_replace('.', '', $wpplink);
        $wpplink = '55'.$wpplink;

        $texto = urlencode('Olá '.$nome.', estou enviando o link para visualização de '.$tipo.' '.$idVenda.' http://www.faiskabrindes.com.br/cgi/imp/venda.php?id='.base64_encode($idVenda));

        $form->colspan = 12;

        if($tipo=='Pedido'){
            
            $popup_converter = new popup(null, 'amarelo');
            $msg = "Deseja mesmo cancelar o pedido e deletar as ordens de serviço?<br><br>";
            $msg.= "<button type='button' onclick='window.location.href=\"cadastrar-venda.php?op=converter&id=".base64_encode($idVenda)."\"' class='btn btn-danger'>Sim</button>";
            $msg.= "<button type='button' onclick='".$popup_converter->get_action_hide()."' class='btn btn-primary pull-right'>Não</button>";
            $popup_converter->texto = $msg;
            $popup_converter->display = 'none';

            $pop_converter = $popup_converter->get_pop();

            $converter = '
            <a onclick="'.$popup_converter->get_action_show().'" href="#" style="padding-right: 20px;">
                <span class="fa fa-toggle-on" data-toggle="tooltip" title="Cancelar Venda '.$idVenda.'"></span>
            </a> ';  

        }else{
            $popup_converter = new popup(null, 'amarelo');
            $msg = "Deseja mesmo efetuar esta venda?<br><br>";
            $msg.= "<button type='button' onclick='window.location.href=\"cadastrar-venda.php?op=converter&id=".base64_encode($idVenda)."\"' class='btn btn-danger'>Sim</button>";
            $msg.= "<button type='button' onclick='".$popup_converter->get_action_hide()."' class='btn btn-primary pull-right'>Não</button>";
            $popup_converter->texto = $msg;
            $popup_converter->display = 'none';

            $pop_converter = $popup_converter->get_pop();

            $converter = '
            <a onclick="'.$popup_converter->get_action_show().'" href="#" style="padding-right: 20px;">
                <span class="fa fa-toggle-off" data-toggle="tooltip" title="Efetuar Venda '.$idVenda.'"></span>
            </a> ';
        }
        isset($ordem_de_servico_id) ? false : $ordem_de_servico_id = null;
        if($ordem_de_servico_id){
            $link_OS = '
            <a href="cadastrar-ordem-de-servico.php?op=visualizar&id='.base64_encode($ordem_de_servico_id).'" style="padding-right: 20px;">
                <span class="fa fa-file-text-o" data-toggle="tooltip" title="Ordem de Serviço"></span>
            </a> ';
        }else{
            $link_OS = null;
        }
        $link = '
        <span style="float:right;">
            '.$converter.'
            <a target="blank_" href="imp/venda.php?id='.base64_encode($idVenda).'" style="padding-right: 20px;">
                <span class="fa fa-print" data-toggle="tooltip" title="Imprimir"></span>
            </a> 
            '.$link_OS.'
            <a href="cadastrar-conta.php?op=visualizar&idVenda='.base64_encode($idVenda).'" style="padding-right: 20px;">
                <span class="fa fa-list-alt" data-toggle="tooltip" title="Conta"></span>
            </a> 
            <a href="javascript:void(0);" onclick=" window.open(\'https://api.whatsapp.com/send?phone='.$wpplink.'&text='.$texto.'\',\'page\');" style="padding-right: 20px;">
                <span class="fa fa-phone-square" data-toggle="tooltip" title="Enviar WhatsApp"></span>
            </a>
        </span>';
        $form->set_arg('h3', $link);
    }else{
        $pop_converter = null;
    }



    $form->set_arg('hidden', 'cliente_id', null, $cliente_id);

    $form->colspan = 4;
    if(isset($error['nome']))
        $form->error = true;
    $form->max = 250;
    $form->ajax_valores = array_values($autocomplete);
    $form->ajax_index = array_values($index);
    $form->set_arg('ajaxtext', 'Nome', 'Nome do cliente', $nome);

    $form->colspan = 4;
    if(isset($error['wpp']))
        $form->error = true;
    $form->max = 250;
    $form->set_arg('text', 'Wpp', '(88) 0000.00000', $wpp, 'tel');

    $form->colspan = 4;
    if(isset($error['telefone']))
        $form->error = true;
    $form->max = 250;
    $form->set_arg('text', 'Telefone', '(88) 0000.00000', $telefone, 'tel');


    $form->colspan = 4;
    if(isset($error['email']))
        $form->error = true;
    $form->max = 250;
    $form->set_arg('email', 'Email', 'email@dominio.com', $email);
    $form->colspan = 4;
    $form->set_arg('text', 'Instagram', '@perfil.do.cliente', $instagram, 'instagram');
    $form->colspan = 4;
    $form->set_arg('text', 'CPF', '000.000.000-00', $cpf, 'cpf');




    $criterio = new TCriteria();
    $criterio->add(new TFilter('status', '=', 1));


    $sql = new TSqlSelect();
    $sql->setEntity('produto');
    $sql->addColumn('id as produtoId');
    $sql->addColumn('nome as produtoNome');
    $sql->setCriteria($criterio);


    $result = $conn->query($sql->getInstruction());

    $autocomplete = array();
    $index = array('value', 'item_id');

    for($i=0; $i<$result->rowCount(); $i++){
        extract($result->fetch(PDO::FETCH_ASSOC));

        for($j=1; $j<100; $j++){
            $var = 'autocomplete_'.$j;
            $$var[] = '{ '.$index[0].': "'.$produtoNome.'", '.$index[1].'_'.$j.': '.$produtoId.'}';
        }


    }

    $form->colspan = 12;
    $form->set_arg('hidden', 'itens_qtd', null, $itens_qtd);
    $form->colspan = 12;
    $cod = "
    function show_hide_itens(op){

        $('#progressao_preco').html('');
        var itens_qtd = parseInt($('input[name=\"itens_qtd\"]').val());

        if(op == 'mais'){
            if(itens_qtd >= 99){
                alert('Quantidade de itens maximo atingida.');
            }else{
                itens_qtd += 1;
            }
        }else if(op == 'menos'){
            if(itens_qtd <= 1){
                alert('Quantidade de itens mimima atingida.');
            }else{
                itens_qtd -= 1;
            }
        }

        for(var i = 1; i < 100; i++ ){
            if(i <= itens_qtd){
                if($('#button_' + i).length){
                    $('#button_' + i).show();
                }
                $('#tipo_item_' + i).show();
                $('#item_' + i).show();
                $('#qtd_' + i).show();
                $('#preco_' + i).show();
                $('#desconto_' + i).show();
                $('#total_' + i).show();
                $('#obs_' + i).show();
                $('#garantia_' + i).show();
            }else if(i > itens_qtd){
                if($('#button_' + i).length){
                    $('#button_' + i).hide();
                }
                $('#tipo_item_' + i).hide();
                $('#item_' + i).hide();
                $('#qtd_' + i).hide();
                $('#preco_' + i).hide();
                $('#desconto_' + i).hide();
                $('#total_' + i).hide();
                $('#obs_' + i).hide();
                $('#garantia_' + i).hide();
            }
        }

        $('input[name=\"itens_qtd\"]').val(itens_qtd);
    }
    ";
    $form->set_js($cod, null);
    $botoes = '<span onclick="show_hide_itens(\'mais\');" class="fa fa-plus-circle" style="cursor:pointer;" data-toggle="tooltip" title="Adicionar itens"></span> Itens do '.$tipo.' <span onclick="show_hide_itens(\'menos\');" class="fa fa-minus-circle" style="cursor:pointer;" data-toggle="tooltip" title="Remover itens"></span>';
    $form->set_arg('h3', $botoes);


    if($id){
        $criterio = new TCriteria();
        $criterio->add(new TFilter('venda_id', '=', $id));

        $sql = new TSqlSelect();
        $sql->setEntity('venda_itens');
        $sql->addColumn('servico_produto');
        $sql->addColumn('item_id');
        $sql->addColumn('item');
        $sql->addColumn('observacao');
        $sql->addColumn('garantia');
        $sql->addColumn('quantidade');
        $sql->addColumn('preco');
        $sql->addColumn('desconto');
        $sql->addColumn('total as total_item');
        $sql->setCriteria($criterio);

        $result = $conn->query($sql->getInstruction());

    }

    for($i = 1; $i < 100; $i++){

        $var_tipo_item = 0;
        $var_item_id = 'item_id_'.$i;
        $var_item = 'item_'.$i;
        $var_qtd = 'qtd_'.$i;
        $var_preco = 'preco_'.$i;
        $var_desconto = 'desconto_'.$i;
        $var_total = 'total_'.$i;
        $var_obs = 'obs_'.$i;
        $var_garantia = 'garantia_'.$i;

        if($i % 2 == 0){
            $class = 'bg-gray disabled color-palette';
        }else{
            $class = '';
        }

        if($i<=$result->rowCount() and $id){
            extract($result->fetch(PDO::FETCH_ASSOC));

            $$var_tipo_item = $servico_produto;
            $$var_item_id = $item_id;
            $$var_item = $item;
            $$var_obs = $observacao;
            $$var_garantia = $garantia;
            $$var_qtd = $quantidade;
            $$var_preco = $preco;
            $$var_desconto = $desconto;
            $$var_total = $total_item;
            
        }else{

            if(isset($idOS)){
                $$var_tipo_item = 1;
            }else{
                $$var_tipo_item = 0;  
            }
            $$var_item_id = 0;
            $$var_item = '';
            $$var_qtd = 1;
            $$var_preco = '';
            $$var_desconto = '';
            $$var_total = '';
            $$var_obs = '';
            $$var_garantia = '';

        }

        $form->colspan = 0;
        $form->set_arg('hidden', 'item id '.$i, '', $$var_item_id);

        $form->colspan = 2;
        $form->div_id = 'tipo_item_'.$i;
        $form->div_class = $class;
        $tipos_itens[0][] = 0;
        $tipos_itens[0][] = 'Produto';
        $tipos_itens[1][] = 1;
        $tipos_itens[1][] = 'Serviço';
        $form->set_arg('select', 'Tipo Item '.$i, $tipos_itens, $$var_tipo_item);

        $form->colspan = 6;
        if(isset($error['item_'.$i]))
            $form->error = true;
        $form->div_id = 'item_'.$i;
        $var = 'autocomplete_'.$i;
        $form->ajax_valores = array_values($$var);
        $form->ajax_index = array_values(array('value', 'item_id_'.$i));
        $form->set_js(null, 'onchange="atualiza_preco(\'produto_id\', \''.$i.'\')"', false);
        $form->div_class = $class;
        $form->set_arg('ajaxtext', 'Item '.$i, 'Digite o nome do produto ou serviço', $$var_item);

        $form->colspan = 2;
        if(isset($error['qtd_'.$i]))
            $form->error = true;
        $form->div_id = 'qtd_'.$i;
        $form->set_js(null, 'onblur="atualiza_preco(\'preco\', \''.$i.'\'); calcular(\'total\', \''.$i.'\');" onchange="atualiza_preco(\'preco\', \''.$i.'\'); calcular(\'total\', \''.$i.'\');"', false);
        $form->div_class = $class;
        $form->set_arg('text', 'Quantidade '.$i, '1', $$var_qtd, 'Integer');

        $form->colspan = 2;
        if(isset($error['preco_'.$i]))
            $form->error = true;
        $form->div_id = 'preco_'.$i;
        $form->set_js(null, 'onchange="calcular(\'real\', \''.$i.'\')"', false);
        $form->div_class = $class;
        $form->set_arg('dinheiro', 'Preço '.$i, '0000,00', real($$var_preco, 'real'), 'Valor2');

        $form->colspan = 6;
        if(isset($error['obs_'.$i]))
            $form->error = true;
        $form->div_id = 'obs_'.$i;
        $form->div_class = $class;
        $form->set_arg('text', 'Observação '.$i, 'Observações sobre este produto ou serviço', $$var_obs);

        $form->colspan = 2;
        if(isset($error['garantia_'.$i]))
            $form->error = true;
        $form->div_id = 'garantia_'.$i;
        $form->div_class = $class;
        $garantias[0][] = 'sem garantia';
        $garantias[0][] = 'sem garantia';
        $garantias[1][] = 3;
        $garantias[1][] = '3 meses';
        $garantias[2][] = 6;
        $garantias[2][] = '6 meses';
        $garantias[3][] = 12;
        $garantias[3][] = '12 meses';
        $garantias[4][] = 24;
        $garantias[4][] = '24 meses';
        $garantias[5][] = 36;
        $garantias[5][] = '36 meses';
        $garantias[6][] = 48;
        $garantias[6][] = '48 meses';
        $garantias[7][] = 60;
        $garantias[7][] = '60 meses';
        $garantias[8][] = 'vitalicia';
        $garantias[8][] = 'vitalicia';
        $form->set_arg('select', 'Garantia '.$i, $garantias, $$var_garantia);

        $form->colspan = 2;
        if(isset($error['desconto_'.$i]))
            $form->error = true;
        $form->div_id = 'desconto_'.$i;
        $form->input_group_addon_back = '%';
        $form->set_js(null, 'onchange="calcular(\'por\', \''.$i.'\')"', false);
        $form->div_class = $class;
        $form->set_arg('text', 'Desconto '.$i, '00,00', real($$var_desconto, 'real'), 'Valor2');

        $form->colspan = 2;
        if(isset($error['total_'.$i]))
            $form->error = true;
        $form->div_id = 'total_'.$i;
        $form->set_js(null, 'onchange="calcular(\'total\', \''.$i.'\')"', false);
        $form->div_class = $class;
        $form->set_arg('dinheiro', 'Total '.$i, '0000,00', real($$var_total, 'real'), 'Valor2');

    }

    $form->colspan = 8;
    $form->div_id = 'progressao_preco';
    $form->set_arg(null, null);

    $form->colspan = 4;
    if(isset($error['total']))
        $form->error = true;
    if(strstr($total, '.')){
        $total = real($total, 'real');
    }
    $form->set_arg('dinheiro', 'Total', '0000,00', $total, 'Valor2');

    $form->button_ativar_deletar = false;




    $pop_filtro = new popup('<span id="filtro_alerta"></span>', 'vermelho');
    $pop_filtro->display = 'none';
    $popup = $pop_filtro->get_pop();



    $html = new template;
    $cod = $html->set_box($tipo.' '.$idVenda, $form->get_form().$popup.$pop_converter);
    $html->set_html($cod);
    $html->get_html('Cadastrar Orçamento ou Pedido', 'Selecione entre orçamento ou pedido.');

    echo $form->mascara;
}






?>


<script type="text/javascript">

    $(function(){

        var itens_qtd = parseInt($('input[name="itens_qtd"]').val());
        for(i = 1; i < 100; i++ ){
            if(i <= itens_qtd){
                if($('#button_' + i).length){
                    $('#button_' + i).show();
                }
                $('#tipo_item_' + i).show();
                $('#item_' + i).show();
                $('#qtd_' + i).show();
                $('#preco_' + i).show();
                $('#desconto_' + i).show();
                $('#total_' + i).show();
                $('#obs_' + i).show();
                $('#garantia_' + i).show();
            }else if(i > itens_qtd){
                if($('#button_' + i).length){
                    $('#button_' + i).hide();
                }
                $('#tipo_item_' + i).hide();
                $('#item_' + i).hide();
                $('#qtd_' + i).hide();
                $('#preco_' + i).hide();
                $('#desconto_' + i).hide();
                $('#total_' + i).hide();
                $('#obs_' + i).hide();
                $('#garantia_' + i).hide();
            }
        }

    });

    function atualiza_preco(op, numero){

        id = $('input[name="item_id_'+numero+'"]').val();
        nome = $('input[name="item_'+numero+'"]').val();
        qtd = $('input[name="quantidade_'+numero+'"]').val();

        $.post('inc/ajax_ordem_de_servico.inc.php', {
            op: op,
            id: id,
            produto: nome,
            quantidade: qtd
        }).done(function(data){
            if(data=='false'){
                $('input[name="item_id_'+numero+'"]').val('');
                $('#progressao_preco').html('');
            }else{
                if(op=='produto_id'){
                    $('#progressao_preco').html(data);
                }else{
                    $('input[name="preco_'+numero+'"]').val(data);
                    calcular('real', numero);
                }
            }
        });
        
    }




    //desconto pode ser por 'por' ou por 'real'
    //se for por 'por' será calculado pela porcentagem, se for por 'real', serar calculado por real
    function calcular(tipo, numero){
        var preco               = $('input[name="preco_'+numero+'"]').val();
        var qtd                 = $('input[name="quantidade_'+numero+'"]').val();
        var desconto            = $('input[name="desconto_'+numero+'"]').val();
        var total               = $('input[name="total_'+numero+'"]').val();
        var total_pedido        = 0.0;
        var qtd_itens           = $('input[name="itens_qtd"]').val();

        if(preco == ''){
            preco = total;
            preco = parseFloat(preco.replace(',', '.'));
            $('input[name="preco_'+numero+'"]').val(preco.toFixed(2).replace(".", ","));
        }else{
            preco = parseFloat(preco.replace(',', '.'));
        }
        if(qtd == ''){
            qtd = 1;
        }
        if(desconto == '' || desconto == 'NaN'){
            desconto = 0;
        }else{
            desconto = parseFloat(desconto.replace(',', '.'));
        }
        if(total == ''){
            total = 0;
        }else{
            total = preco * qtd;
            //total = parseFloat(total.replace(',', '.'));
        }
        

        if(tipo == 'por'){
            $('input[name="desconto_'+numero+'"]').val(desconto.toFixed(2).replace(".", ","));
            $('input[name="total_'+numero+'"]').val((total - (desconto * total / 100)).toFixed(2).replace(".", ","));
        }else if(tipo == 'real'){
            $('input[name="desconto_'+numero+'"]').val((desconto * 100 / preco).toFixed(2).replace(".", ","));
            $('input[name="total_'+numero+'"]').val(total.toFixed(2).replace(".", ","));
        }else if(tipo == 'total'){
            $('input[name="desconto_'+numero+'"]').val((100-(total * 100 / preco)).toFixed(2).replace(".", ","));
            $('input[name="total_'+numero+'"]').val(total.toFixed(2).replace(".", ","));
        }

        for(var i = 1; i <= qtd_itens; i++){
            if(isNaN($('input[name="total_'+i+'"]').val())){
                total_pedido += parseFloat($('input[name="total_'+i+'"]').val().replace(',', '.'));
            }

        }

        
        $('input[name="total"]').val(total_pedido.toFixed(2).replace(".", ","));

    }
    <?php
        if(!isset($tipo))
            $tipo = null;
        $tipo == 'Orçamento' ? $tipo = 'orcamento' : $tipo = 'pedido';
    ?>
    $('form[name="cadastrar_<?php echo $tipo; ?>"]').submit(function(){

        var motivo = '';
        var valida = true;
        var classe_erro = '';

        if($('input[name="nome"]').val()==''){
            motivo += 'Nome do cliente não informado<br>';
            valida = false;
            erro_input(true, 'nome');
        }else{
            erro_input(false, 'nome');
        }

        if($('input[name="wpp"]').val()=='' && $('input[name="telefone"]').val()==''){
            motivo += 'Nenhum telefone ou wpp informado<br>';
            valida = false;
            erro_input(true, 'wpp');
            erro_input(true, 'telefone');
        }else{
            erro_input(false, 'wpp');
            erro_input(false, 'telefone');
        }

        if($('input[name="email"]').val()!='' && $('input[name="email"]').val().indexOf('@')==-1){
            motivo += 'E-mail inválido<br>';
            valida = false;
            erro_input(true, 'email');
        }else{
            erro_input(false, 'email');
        }

        if($('input[name="instagram"]').val()!='' && $('input[name="instagram"]').val().indexOf(' ')>0){
            motivo += 'Instagram inválido<br>';
            valida = false;
            erro_input(true, 'instagram');
        }else{
            erro_input(false, 'instagram');
        }

        if($('input[name="cpf"]').val()!='' && $('input[name="cpf"]').val().indexOf(' ')>0){
            motivo += 'CPF inválido<br>';
            valida = false;
            erro_input(true, 'cpf');
        }else{
            erro_input(false, 'cpf');
        }

        for(var i = 1; i<=$('input[name="itens_qtd"]').val(); i++){
            if($('input[name="item_'+i+'"]').val()==''){
                motivo += 'Produto '+i+' não inserido<br>';
                valida = false;
                erro_input(true, 'item_'+i);
            }else{
                erro_input(false, 'item_'+i);
            }
            
            if($('input[name="quantidade_'+i+'"]').val()==''){
                motivo += 'Quantidade '+i+' do produto não inserido<br>';
                valida = false;
                erro_input(true, 'quantidade_'+i);
            }else{
                erro_input(false, 'quantidade_'+i);
            }

            if($('input[name="preco_'+i+'"]').val()==''){
                motivo += 'Preço '+i+' do produto não inserido<br>';
                valida = false;
                erro_input(true, 'preco_'+i);
            }else{
                erro_input(false, 'preco_'+i);
            }

            if($('input[name="total_'+i+'"]').val()==''){
                motivo += 'Total '+i+' do produto não inserido<br>';
                valida = false;
                erro_input(true, 'total_'+i);
            }else{
                erro_input(false, 'total_'+i);
            }
        }


        if($('input[name="total"]').val()=='' || $('input[name="total"]').val()=='0'){
            motivo += 'Total do pedido não inserido<br>';
            valida = false;
            erro_input(true, 'total');
        }else{
            erro_input(false, 'total');
        }
        
        if(!valida){
            $('#filtro_alerta').html(motivo);
            <?php echo $pop_filtro->get_action_show(); ?>
        }


        //acertar o valor total do orçamento // pedido
        var total_pedido        = 0.0;
        var qtd_itens           = $('input[name="itens_qtd"]').val();
        for(var i = 1; i <= qtd_itens; i++){
            if(isNaN($('input[name="total_'+i+'"]').val())){
                total_pedido += parseFloat($('input[name="total_'+i+'"]').val().replace(',', '.'));
            }

        }
        $('input[name="total"]').val(total_pedido.toFixed(2).replace(".", ","));

        
        return valida;
        
    });

    function erro_input(op, tag){
        var classe_erro = '';
        if(op == true){
            classe_erro = $('input[name="'+tag+'"]').closest('div').attr('class') + ' has-error';
            $('input[name="'+tag+'"]').closest('div').attr('class', classe_erro);
        }else{
            classe_erro = $('input[name="'+tag+'"]').closest('div').attr('class').replace(' has-error', '');
            $('input[name="'+tag+'"]').closest('div').attr('class', classe_erro);
        }
    }

    
    

</script>
