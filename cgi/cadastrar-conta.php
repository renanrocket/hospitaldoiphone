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

    $referido = $tabela_referente = $recebemos___pagamos = $tipo_de_conta = $referido = $forma_de_pagamento = $parcelas = $tipo_de_pagamento = $tipo_de_pagamento_sub = $total = $data_de_vencimento = $data_do_pagamento = $plano_de_contas = null;

    $popup = null;
   
    $op = 'novo';
    $idConta = 0;


}elseif($op=='novo' or $op=='editar'){

    $total = real($total,'float');
    $args[] = array('email', '=', $_COOKIE['email']);
    $usuario = usuario_get($args);      


    $valida = true;
    $erro = 'Usuário não cadastrado. <b>Motivo:</b><br>';



    if($recebemos___pagamos==''){
        $valida = false;
        $erro.= 'Campo Recebemos / Pagamos vazio.<br>';   
        $error['recebemos___pagamos'] = true;
    }

    if($total=='' or $total==0){
        $valida = false;
        $erro.= 'Campo total vazio.<br>';   
        $error['total'] = true;
    }

    if($referido==''){
        $valida = false;
        $erro.= 'Campo referido vazio.<br>';   
        $error['referido'] = true;
    }
    
    $conn->beginTransaction();

    if($valida){


        if($op=='novo'){

            $sql = new TSqlInsert();
            $sql->setEntity('conta');
            $sql->setRowData('recebemos_pagamos', $recebemos___pagamos);
            $sql->setRowData('valor', $total);
            $sql->setRowData('status', $tipo_de_conta);
            $sql->setRowData('referido', $referido);
            $sql->setRowData('forma_pagamento', $forma_de_pagamento);
            $sql->setRowData('parcelas', $parcelas);
            $sql->setRowData('data', date('Y-m-d H:i:s'));
            $sql->setRowData('usuario_id', $usuario[0]['id']);
            $result = $conn->query($sql->getInstruction());

            $idConta = $conn->lastInsertId();

            $valorParcela = round($total/$parcelas, 0);
            $valorUltimaParcela = $total - ($valorParcela * ($parcelas - 1));

            for($i=0; $i<$parcelas; $i++){

                $dias = 30 * $i;
                $data_vencimento = date('Y-m-d', strtotime('+'.$dias.' days'));
                
                $sql = new TSqlInsert();
                $sql->setEntity('conta_itens');
                $sql->setRowData('conta_id', $idConta);
                if(($i+1) == $parcelas){
                    $sql->setRowData('valor', $valorUltimaParcela);
                }else{
                    $sql->setRowData('valor', $valorParcela);
                }
                $sql->setRowData('data_vencimento', $data_vencimento);
                $sql->setRowData('entrada_saida', $tipo_de_conta);
                $result = $conn->query($sql->getInstruction());                
            }

            


        }elseif($op=='editar'){

            if(!is_numeric($id))
                $id = base64_decode($id);
            $idConta = $id;

            //pegando o ultimo registro de parcelas
            $parcelas_anteriores = registro($idConta, 'conta', 'parcelas');

            $criterio = new TCriteria();
            $criterio->add(new TFilter('id', '=', $idConta));

            $sql = new TSqlUpdate();
            $sql->setEntity('conta');
            $sql->setRowData('forma_pagamento', $forma_de_pagamento);
            $sql->setRowData('parcelas', $parcelas);
            //se a conta não for associada a um orcamento ou pedido liberar essa edição
            if($tabela_referente!='venda' and $tabela_referente!='ordem_de_servico'){
                $sql->setRowData('referido', $referido);
                $sql->setRowData('recebemos_pagamos', $recebemos___pagamos);
                $sql->setRowData('valor', $total);
                $sql->setRowData('status', $tipo_de_conta);                
            }
            $sql->setCriteria($criterio);
            $result = $conn->query($sql->getInstruction());


            //se parcelas anteriores é menor do q a parcela atual então inserir uma nova parcela
            if($parcelas_anteriores<$parcelas){

                //calculando quantas parcelas não pagas
                $criterio1 = new TCriteria;
                $criterio1->add(new TFilter('conta_id', '=', $idConta));
                $criterio2 = new TCriteria;
                $criterio2->add(new TFilter('data_pagamento', 'is', null));
                $criterio2->add(new TFilter('data_pagamento', '=', '0000-00-00'), TExpression::OR_OPERATOR);

                $criterio = new TCriteria;
                $criterio->add($criterio1);
                $criterio->add($criterio2);

                $sql = new TSqlSelect();
                $sql->setEntity('conta_itens');
                $sql->addColumn('valor as faltaPagar');
                $sql->setCriteria($criterio);

                $result = $conn->query($sql->getInstruction());
                $parcelas_nao_pagas = $result->rowCount();

                //calculando o total de falta a pagar pegando o total ja pago e subitraindo com o total da conta
                $criterio1 = new TCriteria;
                $criterio1->add(new TFilter('conta_id', '=', $idConta));
                $criterio2 = new TCriteria;
                $criterio2->add(new TFilter('data_pagamento', 'is not', null));
                $criterio2->add(new TFilter('data_pagamento', '<>', '0000-00-00'), TExpression::OR_OPERATOR);

                $criterio = new TCriteria;
                $criterio->add($criterio1);
                $criterio->add($criterio2);

                $sql = new TSqlSelect();
                $sql->setEntity('conta_itens');
                $sql->addColumn('valor as jaPago');
                $sql->setCriteria($criterio);

                $result = $conn->query($sql->getInstruction());
                for($i=$ja_pago=0; $i<$result->rowCount();$i++){
                    extract($result->fetch(PDO::FETCH_ASSOC));
                    $ja_pago+=$jaPago;
                }
                $falta_pagar = registro($idConta, 'conta', 'valor');
                $falta_pagar-=$ja_pago;

                //deletando as parcelas_nao_pagas para inseri-las novamente com os valores ajustados
                $criterio1 = new TCriteria;
                $criterio1->add(new TFilter('conta_id', '=', $idConta));
                $criterio2 = new TCriteria;
                $criterio2->add(new TFilter('data_pagamento', 'is', null));
                $criterio2->add(new TFilter('data_pagamento', '=', '0000-00-00'), TExpression::OR_OPERATOR);

                $criterio = new TCriteria;
                $criterio->add($criterio1);
                $criterio->add($criterio2);

                $sql = new TSqlDelete();
                $sql->setEntity('conta_itens');
                $sql->setCriteria($criterio);
                $result = $conn->query($sql->getInstruction());


                //calculando as novas parcelas e inserindo novamente
                $parcelas_a_mais = $parcelas - $parcelas_anteriores;
                $parcelas_a_mais += $parcelas_nao_pagas;

                $valorParcela = round($falta_pagar/$parcelas_a_mais, 0);
                $valorUltimaParcela = $falta_pagar - ($valorParcela * ($parcelas_a_mais - 1));

                for($i=0; $i<$parcelas_a_mais; $i++){

                    $dias = 30 * $i;
                    $data_vencimento = date('Y-m-d', strtotime('+'.$dias.' days'));
                    
                    $sql = new TSqlInsert();
                    $sql->setEntity('conta_itens');
                    $sql->setRowData('conta_id', $idConta);
                    if(($i+1) == $parcelas_a_mais){
                        $sql->setRowData('valor', $valorUltimaParcela);
                    }else{
                        $sql->setRowData('valor', $valorParcela);
                    }
                    $sql->setRowData('data_vencimento', $data_vencimento);
                    $sql->setRowData('entrada_saida', $tipo_de_conta);
                    $result = $conn->query($sql->getInstruction());

                }


            }else{
                //se parcelas anteriores menores do q a parcela atual
                //então deleta a ultima parcela e recalcula o quanto falta a pagar


                //seleciona todas as parcelas por ordem decrescente para depois apagar de tras pra frente
                $criterio = new TCriteria();
                $criterio->add(new TFilter('conta_id', '=', $idConta));
                $criterio->setProperty('order', 'id desc');

                $sql = new TSqlSelect();
                $sql->setEntity('conta_itens');
                $sql->addColumn('id as conta_itens_id');
                $sql->setCriteria($criterio);

                $result = $conn->query($sql->getInstruction());

                //pegando a diferença das parcelas a menos
                $parcelas_a_menos = $parcelas_anteriores - $parcelas;
                //somando mais uma para ajustar a ultima parcela com o valor total faltante
                $parcelas_a_menos++;
                
                //apagando de um por um as ultimas parcelas
                for($i=0; $i<$parcelas_a_menos; $i++){
                    extract($result->fetch(PDO::FETCH_ASSOC));

                    $criterio = new TCriteria();
                    $criterio->add(new TFilter('id', '=', $conta_itens_id));

                    $sql= new TSqlDelete();
                    $sql->setEntity('conta_itens');
                    $sql->setCriteria($criterio);

                    $resultDelete = $conn->query($sql->getInstruction());

                }

                //calculando o total de falta a pagar pegando o total ja pago e atualizando a conta ja_pago
                $criterio1 = new TCriteria;
                $criterio1->add(new TFilter('conta_id', '=', $idConta));
                $criterio2 = new TCriteria;
                $criterio2->add(new TFilter('data_pagamento', 'is not', null));
                $criterio2->add(new TFilter('data_pagamento', '<>', '0000-00-00'), TExpression::OR_OPERATOR);

                $criterio = new TCriteria;
                $criterio->add($criterio1);
                $criterio->add($criterio2);

                $sql = new TSqlSelect();
                $sql->setEntity('conta_itens');
                $sql->addColumn('valor as jaPago');
                $sql->setCriteria($criterio);

                $result = $conn->query($sql->getInstruction());
                for($i=$ja_pago=0; $i<$result->rowCount();$i++){
                    extract($result->fetch(PDO::FETCH_ASSOC));
                    $ja_pago+=$jaPago;
                }

                //atualizando a conta o valor já pago
                $criterio = new TCriteria();
                $criterio->add(new TFilter('id', '=', $idConta));

                $sql = new TSqlUpdate();
                $sql->setEntity('conta');
                $sql->setRowData('ja_pago', $ja_pago);
                $sql->setCriteria($criterio);

                $result = $conn->query($sql->getInstruction());

                //calculando a ultima parcela, capturando informação de entrada e saida e inserindo na conta_itens
                $valor = registro($idConta, 'conta', 'valor');
                $entrada_saida = registro($idConta, 'conta', 'status');
                $valorUltimaParcela = $valor - $ja_pago;


                //inserindo a nova parcela
                $sql = new TSqlInsert();
                $sql->setEntity('conta_itens');
                $sql->setRowData('conta_id', $idConta);
                $sql->setRowData('valor', $valorUltimaParcela);
                $sql->setRowData('data_vencimento', date('Y-m-d'));
                $sql->setRowData('entrada_saida', $entrada_saida);
                $result = $conn->query($sql->getInstruction());


            }


        }

        $op = 'editar';

    }else{
        $pop = new popup($erro, 'vermelho');
        $popup = $pop->get_pop();
        if($op=='novo'){
           $idConta = 0;
           $referido = $recebemos___pagamos = $tipo_de_conta = $referido = $forma_de_pagamento = $parcelas = $tipo_de_pagamento = $tipo_de_pagamento_sub = $total = $data_de_vencimento = $data_do_pagamento = null;
        }


    }

    if(!isset($tabela_referente))
        $tabela_referente = null;
    if(!isset($popup))
        $popup = null;

    if($conn->inTransaction()){
        $conn->commit();
    }


}elseif($op=="lancarParcela" or $op=="cancelarParcela" or $op=="visualizar"){

    $referido = $recebemos___pagamos = $tipo_de_conta = $referido = $forma_de_pagamento = $parcelas = $tipo_de_pagamento = $tipo_de_pagamento_sub = $total = $data_de_vencimento = $data_do_pagamento = $plano_de_contas = null;

    $popup = null;

    if(isset($id))
        $idConta = $id;
    
    $conn->beginTransaction();

    if(isset($idContaSub)){
        $idContaSub = base64_decode($idContaSub);
    }

    if(isset($idVenda)){
        $idVenda = base64_decode($idVenda);

        $criterio = new TCriteria();
        $criterio->add(new TFilter('referido', '=', $idVenda));
        $criterio->add(new TFilter('tabela_referente', '=', 'venda'));

        $sql = new TSqlSelect();
        $sql->setEntity('conta');
        $sql->addColumn('id as idConta');
        $sql->setCriteria($criterio);

        $result = $conn->query($sql->getInstruction());

        extract($result->fetch(PDO::FETCH_ASSOC));

    }

    if($op=="lancarParcela"){

        $cod = base64_decode($cod);
        $cod = explode('&', $cod);
        for($i=0; $i<count($cod); $i++){
            $cod[$i] = explode('=', $cod[$i]);
        }

        $tipo_pagamento = $cod[0][1];
        $tipo_pagamento_sub = $cod[1][1];
        $data_vencimento = formataDataInv($cod[2][1]);
        $valor = real($cod[3][1], 'float');
        $data_pagamento = formataDataInv($cod[4][1]);


        if($data_pagamento==''){
            $data_pagamento = date('Y-m-d');
        }


        $criterio = new TCriteria();
        $criterio->add(new TFilter('id', '=', $idContaSub));

        $args[] = array('email', '=', $_COOKIE['email']);
        $usuario = usuario_get($args);

        $sql = new TSqlUpdate();
        $sql->setEntity('conta_itens');
        $sql->setRowData('tipo_pagamento', $tipo_pagamento);
        $sql->setRowData('tipo_pagamento_sub', $tipo_pagamento_sub);
        $sql->setRowData('data_vencimento', $data_vencimento);
        $sql->setRowData('valor', $valor);
        $sql->setRowData('data_pagamento', $data_pagamento);
        $sql->setRowData('usuario_id', $usuario[0]['id']);
        $sql->setCriteria($criterio);

        $result = $conn->query($sql->getInstruction());

   }elseif($op=='cancelarParcela'){

        $criterio = new TCriteria();
        $criterio->add(new TFilter('id', '=', $idContaSub));

        $sql = new TSqlUpdate();
        $sql->setEntity('conta_itens');
        $sql->setRowData('data_pagamento', '(NULL)');
        $sql->setCriteria($criterio);

        $result = $conn->query($sql->getInstruction());

   }

   if($op!='visualizar'){
        //pegando todo valor já pago e somando com o atual valor
        $idConta = registro($idContaSub, 'conta_itens', 'conta_id', 'id');

        $criterio1 = new TCriteria;
        $criterio1->add(new TFilter('conta_id', '=', $idConta));
        $criterio2 = new TCriteria;
        $criterio2->add(new TFilter('data_pagamento', 'is not', null));
        $criterio2->add(new TFilter('data_pagamento', '<>', '0000-00-00'), TExpression::OR_OPERATOR);

        $criterio = new TCriteria;
        $criterio->add($criterio1);
        $criterio->add($criterio2);

        $sql = new TSqlSelect();
        $sql->setEntity('conta_itens');
        $sql->addColumn('sum(valor) as ja_pago');
        $sql->setCriteria($criterio);

        $result = $conn->query($sql->getInstruction());
        if($result->rowCount()){
            extract($result->fetch(PDO::FETCH_ASSOC));

        }else{
            $ja_pago = $valor;
        }

        $criterio = new TCriteria();
        $criterio->add(new TFilter('id', '=', $idConta));

        $sql = new TSqlUpdate();
        $sql->setEntity('conta');
        $sql->setRowData('ja_pago', $ja_pago);
        $sql->setCriteria($criterio);

        $result = $conn->query($sql->getInstruction());


   }

    //verificando se o usuario já lançou todas as parcelas mas mesmo assim ainda falta dinheiro a receber
    if($op=='lancarParcela'){

        $criterio1 = new TCriteria;
        $criterio1->add(new TFilter('conta_id', '=', $idConta));
        $criterio2 = new TCriteria;
        $criterio2->add(new TFilter('data_pagamento', 'is', null));
        $criterio2->add(new TFilter('data_pagamento', '=', '0000-00-00'), TExpression::OR_OPERATOR);

        $criterio = new TCriteria;
        $criterio->add($criterio1);
        $criterio->add($criterio2);

        $sql = new TSqlSelect();
        $sql->setEntity('conta_itens');
        $sql->addColumn('*');
        $sql->setCriteria($criterio);

        $result = $conn->query($sql->getInstruction());

        $valor_total = registro($idConta, 'conta', 'valor');

        //se todas as parcelas estiverem preenchidas entra aqui e verifica se o total pago é igual o total da conta
        if(!$result->rowCount()){

           if($ja_pago!=$valor_total){


                //pegando data_vencimento e etrada_saida pra inserir na nova parcela
                $criterio = new TCriteria();
                $criterio->add(new TFilter('conta_id', '=', $idConta));
                $criterio->setProperty('order', 'id desc');

                $sql = new TSqlSelect();
                $sql->setEntity('conta_itens');
                $sql->addColumn('data_vencimento');
                $sql->addColumn('entrada_saida');
                $sql->setCriteria($criterio);

                $result = $conn->query($sql->getInstruction());

                extract($result->fetch(PDO::FETCH_ASSOC));

                $data_vencimento = date('Y-m-d',strtotime('+30 days',strtotime($data_vencimento)));

                //inserindo a nova parcela
                $sql = new TSqlInsert();
                $sql->setEntity('conta_itens');
                $sql->setRowData('conta_id', $idConta);
                $sql->setRowData('valor', ($valor_total-$ja_pago));
                $sql->setRowData('data_vencimento', $data_vencimento);
                $sql->setRowData('entrada_saida', $entrada_saida);
                $result = $conn->query($sql->getInstruction());
                

                //pegando a quantidade de parcelas
                $criterio = new TCriteria();
                $criterio->add(new TFilter('conta_id', '=', $idConta));

                $sql = new TSqlSelect();
                $sql->setEntity('conta_itens');
                $sql->addColumn('*');
                $sql->setCriteria($criterio);

                $result = $conn->query($sql->getInstruction());

                //atualizando a nova parcela na conta
                $criterio = new TCriteria();
                $criterio->add(new TFilter('id', '=', $idConta));

                $sql = new TSqlUpdate();
                $sql->setEntity('conta');
                $sql->setRowData('parcelas', $result->rowCount());
                $sql->setCriteria($criterio);

                $result = $conn->query($sql->getInstruction());


            }

        }else{
            //verificando se as parcelas estão com valores certos
            //primeiro verificando quantas parcelas falta pagar
            $criterio1 = new TCriteria;
            $criterio1->add(new TFilter('conta_id', '=', $idConta));
            $criterio2 = new TCriteria;
            $criterio2->add(new TFilter('data_pagamento', 'is', null));
            $criterio2->add(new TFilter('data_pagamento', '=', '0000-00-00'), TExpression::OR_OPERATOR);

            $criterio = new TCriteria;
            $criterio->add($criterio1);
            $criterio->add($criterio2);

            $sql = new TSqlSelect();
            $sql->setEntity('conta_itens');
            $sql->addColumn('id as id_conta_itens');
            $sql->addColumn('valor as faltaPagar');
            $sql->setCriteria($criterio);

            $result = $conn->query($sql->getInstruction());
            $arrayIds = array();
            $falta_pagar = 0;
            $parcelas_falta_pagar = $result->rowCount();
            for($i=0; $i<$result->rowCount(); $i++){
                extract($result->fetch(PDO::FETCH_ASSOC));
                $arrayIds[] = $id_conta_itens;
                $falta_pagar += $faltaPagar;
            }

            //se o valor de falta pagar for diferente do valor ja pago - o valor total
            //então editar as parcelas
            $valor = registro($idConta, 'conta', 'valor');
            if($falta_pagar != ($valor - $ja_pago)){

                $falta_pagar = $valor - $ja_pago;

                $valorParcela = round($falta_pagar/$parcelas_falta_pagar, 0);
                $valorUltimaParcela = $falta_pagar - ($valorParcela * ($parcelas_falta_pagar - 1));

                for($i=0; $i<$parcelas_falta_pagar; $i++){

                    $criterio = new TCriteria();
                    $criterio->add(new TFilter('id', '=', $arrayIds[$i]));
                    
                    $sql = new TSqlUpdate();
                    $sql->setEntity('conta_itens');
                    if(($i+1) == $parcelas_falta_pagar){
                        $sql->setRowData('valor', $valorUltimaParcela);
                    }else{
                        $sql->setRowData('valor', $valorParcela);
                    }
                    $sql->setCriteria($criterio);
                    $result = $conn->query($sql->getInstruction());


                }
            }
        }

    }


   
   if($conn->inTransaction()){
      $conn->commit();
   }
   

    if(!is_numeric($idConta))
        $idConta = base64_decode($idConta);

    $criterio = new TCriteria();
    $criterio->add(new TFilter('id', '=', $idConta));

    $sql = new TSqlSelect();
    $sql->setEntity('conta');
    $sql->addColumn('*');
    $sql->setCriteria($criterio);
    $result = $conn->query($sql->getInstruction());
    if($result->rowCount()){
        extract($result->fetch(PDO::FETCH_ASSOC));
        $recebemos___pagamos = $recebemos_pagamos;
        $total = $valor;
        $tipo_de_conta = $status;

    }

    
    $op = 'editar';

}


$form = new form('Cadastrar Conta', 'cadastrar-conta.php', $op);
$form->id = $idConta;
$form->tabela = 'conta';

$form->set_arg('hidden', 'tabela_referente', null, $tabela_referente);

if($op=='editar'){
    

    $form->colspan = 12;

    if($tabela_referente=='venda'){
        
        $link = '
        <span style="float:right;">        
            <a href="cadastrar-venda.php?op=visualizar&id='.base64_encode($referido).'" style="padding-right: 20px;">
                <span class="fa fa-file-text-o" data-toggle="tooltip" title="Pedido / Orçamento '.$referido.'"></span>
            </a> 
        </span>';

        

    }elseif($tabela_referente=='ordem_de_servico'){
        $link = '
        <span style="float:right;">        
            <a href="cadastrar-ordem-de-servico.php?op=visualizar&id='.base64_encode($referido).'" style="padding-right: 20px;">
                <span class="fa fa-file-text-o" data-toggle="tooltip" title="Ordem de serviço '.$referido.'"></span>
            </a> 
        </span>';
    }else{
        $link = null;
    }

    $form->set_arg('h3', $link);
}else{
    $pop_converter = null;
}


$form->colspan = 2;
//recuperar select dos tipo de parcelas
$criterio = new TCriteria();
$criterio->add(new TFilter('status', '=', 1));

$sql = new TSqlSelect();
$sql->setEntity('conta_status');
$sql->addColumn('id');
$sql->addColumn('nome');
$sql->setCriteria($criterio);
$result = $conn->query($sql->getInstruction());
$tipos = null;
for($i=0; $i<$result->rowCount(); $i++){
    extract($result->fetch(PDO::FETCH_ASSOC));
    $tipos[$i][] = $id;
    $tipos[$i][] = $nome;
}
$form->set_arg('select', 'Tipo de conta', $tipos, $tipo_de_conta);


//preparando todos os ajax da pagina num unico select para otimizar a velocidade
$criterio = new TCriteria();
$criterio->add(new TFilter('status', '=', 1));
$criterio->setProperty('group', 'nome, email');

$sql = new TSqlSelect();
$sql->setEntity('cliente');
$sql->addColumn('nome as clienteNome');
$sql->setCriteria($criterio);


$result = $conn->query($sql->getInstruction());

$clientesNomes = array();

for($i=0; $i<$result->rowCount(); $i++){
    extract($result->fetch(PDO::FETCH_ASSOC));
    $clientesNomes[] = $clienteNome;
}

$criterio = new TCriteria();
$criterio->setProperty('desc', 'id');
$criterio->setProperty('limit', LIMIT);

$sql = new TSqlSelect();
$sql->setEntity('venda');
$sql->addColumn('nome as clienteNome');
$sql->setCriteria($criterio);


$result = $conn->query($sql->getInstruction());

for($i=0; $i<$result->rowCount(); $i++){
    extract($result->fetch(PDO::FETCH_ASSOC));
    $clientesNomes[] = $clienteNome;
}


$clientesNomes = array_unique($clientesNomes);


$criterio = new TCriteria();
$criterio->setProperty('desc', 'id');
$criterio->setProperty('limit', '2000');

$sql = new TSqlSelect();
$sql->setEntity('conta');
$sql->addColumn('plano_de_contas as planoConta');
$sql->setCriteria($criterio);


$result = $conn->query($sql->getInstruction());

$planoContas = array();

for($i=0; $i<$result->rowCount(); $i++){
    extract($result->fetch(PDO::FETCH_ASSOC));
    $planoContas[] = $planoConta;
}



$form->colspan = 6;
if(isset($error['nome']))
    $form->error = true;
$form->max = 250;
$form->ajax_valores = array_values($clientesNomes);
if(isset($error['recebemos___pagamos']))
$form->error = true;
$form->set_arg('ajaxtext', 'Recebemos / Pagamos', 'Nome do cliente ou fornecedor', $recebemos___pagamos);




$form->colspan = 4;
if(isset($error['total']))
    $form->error = true;
$form->set_arg('dinheiro', 'Total', 'R$ 00,00', real($total, 'real'), 'Valor2');




//se essa conta for nova ou não for associada a uma ordem de serviço ou pedido/orçamento
//então mostrar um ajax com os nomes dos cliente
//do contrário não disponibilizar edição do referente
if($op!='novo'){
    $tabela_referente = registro($idConta, 'conta', 'tabela_referente');
    if($tabela_referente=='ordem_de_servico'){

        $criterio = new TCriteria();
        $criterio->add(new TFilter('id', '=', $referido));

        $sql = new TSqlSelect();
        $sql->setEntity($tabela_referente);
        $sql->addColumn('servico');
        $sql->setCriteria($criterio);
        $result = $conn->query($sql->getInstruction());

        if($result->rowCount()){
            extract($result->fetch(PDO::FETCH_ASSOC));
            $referido = $servico;
        }

        }elseif($tabela_referente=='venda'){

        $criterio = new TCriteria();
        $criterio->add(new TFilter('venda_id', '=', $referido));

        $sql = new TSqlSelect();
        $sql->setEntity('venda_itens');
        $sql->addColumn('item');
        $sql->addColumn('quantidade');
        $sql->setCriteria($criterio);
        $result = $conn->query($sql->getInstruction());

        if($result->rowCount()){
            $referido = null;
            for($i=0; $i<$result->rowCount(); $i++){
                extract($result->fetch(PDO::FETCH_ASSOC));
                if($i>0)
                    $referido .= ', ';
                $referido .= $item.' ('.$quantidade.')';
            }
        }
    }
}

if($tabela_referente){
    $form->set_arg('hidden', 'Referido', 'Recibo referente...', $referido);
    $form->colspan = 6;
    if($tabela_referente=='venda'){
        $form->set_arg_generic('<label>Referido</label><a href="cadastrar-venda.php?op=visualizar&id='.base64_encode($referido).'" class="btn btn-primary btn-block btn-flat" >Venda '.$referido.'</a>');
    }
}else{
    $form->colspan = 6;
    $form->max = 100;
    if(isset($error['referido']))
        $form->error = true;
    $form->set_arg('text', 'Referido', 'Recibo referente...', $referido);
}


$form->colspan = 2;
$formas[0][] = 1;
$formas[0][] = 'A vista';
$formas[1][] = 2;
$formas[1][] = 'A prazo';
$form->set_js(null, 'onchange="formaPagamento(\'forma\');"');
$form->set_arg('select', 'Forma de pagamento', $formas, $forma_de_pagamento);

$form->colspan = 2;
$sql = new TSqlSelect();
$sql->setEntity('conta_max_parcelas');
$sql->addColumn('max_parcela');
$result = $conn->query($sql->getInstruction());

if($result->rowCount()){
    extract($result->fetch(PDO::FETCH_ASSOC));
}else{
    $max_parcela = 6;
}
$parcelasM = null;
for($i=0; $i<$max_parcela; $i++){
    $parcelasM[$i][] = ($i+1);
    $parcelasM[$i][] = ($i+1);
}
$form->set_js(null, 'onchange="formaPagamento(\'parcela\');"');
$form->set_arg('select', 'Parcelas', $parcelasM, $parcelas);


$form->colspan = 2;
$form->ajax_valores = array_values($planoContas);
$form->set_arg('ajaxtext', 'Plano de Contas', 'Conta de luz, Venda...', $plano_de_contas);




if($op!='novo'){


    //recuperando wpp para envio de recibos por wpp
    $criterio = new TCriteria();
    $criterio->add(new TFilter('id','=', $idConta));

    $sql = new TSqlSelect();
    $sql->setEntity('conta');
    $sql->addColumn('tabela_referente');
    $sql->addColumn('referido');
    $sql->setCriteria($criterio);

    $result = $conn->query($sql->getInstruction());
    $wpp = null;
    if($result->rowCount()){
        extract($result->fetch(PDO::FETCH_ASSOC));
        if($tabela_referente and $referido){
            $criterio = new TCriteria();
            $criterio->add(new TFilter('id','=', $referido));

            $sql = new TSqlSelect();
            $sql->setEntity($tabela_referente);
            $sql->addColumn('wpp');
            $sql->setCriteria($criterio);

            $result = $conn->query($sql->getInstruction());
            if($result->rowCount()){
                extract($result->fetch(PDO::FETCH_ASSOC));
            }
        }
    }else{
        $wpp = null;
    }

    //separação de conta das parcelas
    $form->colspan = 12;
    $form->set_arg('h3', 'Parcelas');

    //recuperar select dos tipo de parcelas
    $criterio = new TCriteria();
    $criterio->add(new TFilter('status', '=', 1));

    $sql = new TSqlSelect();
    $sql->setEntity('conta_tipo_pagamento');
    $sql->addColumn('id');
    $sql->addColumn('nome');
    $sql->setCriteria($criterio);
    $result = $conn->query($sql->getInstruction());
    $tiposPagamentos = null;
    for($i=0; $i<$result->rowCount(); $i++){
        extract($result->fetch(PDO::FETCH_ASSOC));
        $tiposPagamentos[$i][] = $id;
        $tiposPagamentos[$i][] = $nome;
    }

    //recuperação das parelas para exibição
    $criterio = new TCriteria();
    $criterio->add(new TFilter('conta_id', '=', $idConta));

    $sql = new TSqlSelect();
    $sql->setEntity('conta_itens');
    $sql->addColumn('id as idContaSub');
    $sql->addColumn('tipo_pagamento');
    $sql->addColumn('tipo_pagamento_sub');
    $sql->addColumn('valor as valorSub');
    $sql->addColumn('data_pagamento');
    $sql->addColumn('data_vencimento');
    $sql->addColumn('usuario_id');
    $sql->setCriteria($criterio);

    $resultSub = $conn->query($sql->getInstruction());

    for($j=1; $j<=$resultSub->rowCount(); $j++){

        extract($resultSub->fetch(PDO::FETCH_ASSOC));

        $data_pagamento = formataData($data_pagamento);
        $data_vencimento = formataData($data_vencimento);

        $form->colspan = 2;
        $form->set_js(null, 'onchange="showTipoPagamentoSub(this.value, \''.$j.'\')"');
        $form->div_id = 'tipo_de_pagamento_'.$j;
        $form->set_arg('select', 'Tipo de Pagamento '.$j, $tiposPagamentos, $tipo_pagamento);

        $form->colspan = 2;
        $form->div_id = 'tipo_de_pagamento_sub_'.$j;
        $tiposSubs[0][]= $tipo_pagamento_sub;
        $tiposSubs[0][]= 'Carregando...';
        $form->set_arg('select', 'Via '.$j, $tiposSubs, $tipo_pagamento_sub);

        $form->colspan = 2;
        $form->max = 10;
        if(subtrairDatas(date('Y-m-d'), formataDataInv($data_vencimento))<=0){
            $form->div_class = "alert-danger";
        }

        $form->set_arg('text', 'Data de vencimento '.$j, 'dd/mm/AAAA', $data_vencimento, 'data');

        $form->colspan = 2;
        $form->set_arg('dinheiro', 'Valor '.$j, 'R$ 00,00', real($valorSub, 'real'), 'Valor2');

        $form->colspan = 2;
        $form->max = 10;
        $form->div_class = "alert-warning";
        $form->set_arg('text', 'Data do pagamento '.$j, 'dd/mm/AAAA', $data_pagamento, 'data');

        $form->colspan = 2;
        if($data_pagamento){
            $title = 'Editar';
        }else{
            $title = 'Lançar';
        }
        $botao = '<a href="javascript:void(0);" onclick="lancarParcela(\''.$idContaSub.'\', \''.$j.'\');" class="btn-conta fa fa-save" data-toggle="tooltip" title="'.$title.' parcela"></a>';
        if($data_pagamento){
            $botao .= '<a href="javascript:void(0);" onclick="cancelarParcela(\''.$idContaSub.'\');" class="btn-conta fa fa-rotate-left" data-toggle="tooltip" title="Cancelar parcela"></a>';
            $botao .= '<a href="javascript:void(0);" onclick="window.open(\'imp/recibo.php?id='.base64_encode($idContaSub).'\');" class="btn-conta fa fa-print" data-toggle="tooltip" title="Imprimir"></a>';
            if($wpp){
                $wpplink = str_replace('(', '', $wpp);
                $wpplink = str_replace(')', '', $wpplink);
                $wpplink = str_replace(' ', '', $wpplink);
                $wpplink = str_replace('.', '', $wpplink);
                $wpplink = '55'.$wpplink;

                $texto = urlencode('Olá '.$nome.', estou enviando o link para visualização do recibo '.$idContaSub.' http://www.faiskabrindes.com.br/cgi/imp/recibo.php?id='.base64_encode($idContaSub));
                $botao .= '<a href="javascript:void(0);" onclick="window.open(\'https://api.whatsapp.com/send?phone='.$wpplink.'&text='.$texto.'\',\'page\');" class="btn-conta fa fa-phone-square" data-toggle="tooltip" title="Enviar por Whats app"></a>';
            }
        }
        $form->set_arg_generic($botao);

        $form->colspan = 12;
        $form->set_arg_generic('');

    }


}







$html = new template;
$cod = $html->set_box('Formulário de cadastramento de conta', $form->get_form().$popup);
$html->set_html($cod);
$html->get_html('Cadastrar Conta', 'Cadastre recibo a pagar ou a receber para registro de caixa.');

echo $form->mascara;


?>


<script type="text/javascript">

    $(function(){
        if($('select[name="parcelas"]').val()!='1'){
            $('select[name="forma_de_pagamento"]').val(2);
        }
    });

    function showTipoPagamentoSub(valor, i){

        var tipo_de_pagamento_sub = $('select[name="via_'+i+'"]').val();


        $.post("inc/ajax_tipo_pagamento_sub.inc.php", {
            id_tipo_pagamento : valor,
            selecionado : tipo_de_pagamento_sub,
            i : i
        }, function(data) {
            if (data.length > 0) {
                $("#tipo_de_pagamento_"+i).attr("class","form-group  col-xs-2");
                $("#tipo_de_pagamento_sub_"+i).show();
                $("#tipo_de_pagamento_sub_"+i).html(data);
            }else{
                $("#tipo_de_pagamento_"+i).attr("class","form-group  col-xs-4");
                $("#tipo_de_pagamento_sub_"+i).hide();
            }
        });
    }

    function formaPagamento(op){
        var forma = $('select[name="forma_de_pagamento"]').val();
        var parcela = $('select[name="parcelas"]').val();
        
        if(op=='forma' && forma=='1'){
            $('select[name="parcelas"]').val(1);
        }else if(op=='forma' && forma=='2' && parcela=='1'){
            $('select[name="parcelas"]').val(2);
        }else if(op=='parcela' && parcela=='1'){
            $('select[name="forma_de_pagamento"]').val(1);
        }else if(op=='parcela' && parcela!='1'){
            $('select[name="forma_de_pagamento"]').val(2);
        }
    }

    function lancarParcela(idContaSub, i){

        tipo_pagamento = $('select[name="tipo_de_pagamento_'+i+'"]').val();
        tipo_pagamento_sub = $('select[name="via_'+i+'"]').val();
        data_vencimento = $('input[name="data_de_vencimento_'+i+'"]').val();
        valor = $('input[name="valor_'+i+'"]').val();
        data_pagamento = $('input[name="data_do_pagamento_'+i+'"]').val();

        var cod = $.base64.encode('tipo_pagamento='+tipo_pagamento + '&tipo_pagamento_sub=' + tipo_pagamento_sub + '&data_vencimento=' + data_vencimento + '&valor=' + valor + '&data_pagamento=' + data_pagamento);

        idContaSub = $.base64.encode(idContaSub);
        window.open('cadastrar-conta.php?op=lancarParcela&idContaSub='+idContaSub+'&cod='+cod, '_self');
        

    }

    function cancelarParcela(idContaSub){

        idContaSub = $.base64.encode(idContaSub);
        window.open('cadastrar-conta.php?op=cancelarParcela&idContaSub='+idContaSub, '_self');
        

    }

    $(function(){
        var parcelas = $('select[name="parcelas"]').val();
        for(var i = 1; i <= parcelas; i++){
            tipo_pagamento = $('select[name="tipo_de_pagamento_'+i+'"]').val();
            showTipoPagamentoSub(tipo_pagamento, i);
        }
    });


    //função para encriptar em base64 no javascript
    jQuery.base64 = ( function( $ ) {

      var _PADCHAR = "=",
        _ALPHA = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",
        _VERSION = "1.0";


      function _getbyte64( s, i ) {
        // This is oddly fast, except on Chrome/V8.
        // Minimal or no improvement in performance by using a
        // object with properties mapping chars to value (eg. 'A': 0)

        var idx = _ALPHA.indexOf( s.charAt( i ) );

        if ( idx === -1 ) {
          throw "Cannot decode base64";
        }

        return idx;
      }
      
      
      function _decode( s ) {
        var pads = 0,
          i,
          b10,
          imax = s.length,
          x = [];

        s = String( s );
        
        if ( imax === 0 ) {
          return s;
        }

        if ( imax % 4 !== 0 ) {
          throw "Cannot decode base64";
        }

        if ( s.charAt( imax - 1 ) === _PADCHAR ) {
          pads = 1;

          if ( s.charAt( imax - 2 ) === _PADCHAR ) {
            pads = 2;
          }

          // either way, we want to ignore this last block
          imax -= 4;
        }

        for ( i = 0; i < imax; i += 4 ) {
          b10 = ( _getbyte64( s, i ) << 18 ) | ( _getbyte64( s, i + 1 ) << 12 ) | ( _getbyte64( s, i + 2 ) << 6 ) | _getbyte64( s, i + 3 );
          x.push( String.fromCharCode( b10 >> 16, ( b10 >> 8 ) & 0xff, b10 & 0xff ) );
        }

        switch ( pads ) {
          case 1:
            b10 = ( _getbyte64( s, i ) << 18 ) | ( _getbyte64( s, i + 1 ) << 12 ) | ( _getbyte64( s, i + 2 ) << 6 );
            x.push( String.fromCharCode( b10 >> 16, ( b10 >> 8 ) & 0xff ) );
            break;

          case 2:
            b10 = ( _getbyte64( s, i ) << 18) | ( _getbyte64( s, i + 1 ) << 12 );
            x.push( String.fromCharCode( b10 >> 16 ) );
            break;
        }

        return x.join( "" );
      }
      
      
      function _getbyte( s, i ) {
        var x = s.charCodeAt( i );

        if ( x > 255 ) {
          throw "INVALID_CHARACTER_ERR: DOM Exception 5";
        }
        
        return x;
      }


      function _encode( s ) {
        if ( arguments.length !== 1 ) {
          throw "SyntaxError: exactly one argument required";
        }

        s = String( s );

        var i,
          b10,
          x = [],
          imax = s.length - s.length % 3;

        if ( s.length === 0 ) {
          return s;
        }

        for ( i = 0; i < imax; i += 3 ) {
          b10 = ( _getbyte( s, i ) << 16 ) | ( _getbyte( s, i + 1 ) << 8 ) | _getbyte( s, i + 2 );
          x.push( _ALPHA.charAt( b10 >> 18 ) );
          x.push( _ALPHA.charAt( ( b10 >> 12 ) & 0x3F ) );
          x.push( _ALPHA.charAt( ( b10 >> 6 ) & 0x3f ) );
          x.push( _ALPHA.charAt( b10 & 0x3f ) );
        }

        switch ( s.length - imax ) {
          case 1:
            b10 = _getbyte( s, i ) << 16;
            x.push( _ALPHA.charAt( b10 >> 18 ) + _ALPHA.charAt( ( b10 >> 12 ) & 0x3F ) + _PADCHAR + _PADCHAR );
            break;

          case 2:
            b10 = ( _getbyte( s, i ) << 16 ) | ( _getbyte( s, i + 1 ) << 8 );
            x.push( _ALPHA.charAt( b10 >> 18 ) + _ALPHA.charAt( ( b10 >> 12 ) & 0x3F ) + _ALPHA.charAt( ( b10 >> 6 ) & 0x3f ) + _PADCHAR );
            break;
        }

        return x.join( "" );
      }


      return {
        decode: _decode,
        encode: _encode,
        VERSION: _VERSION
      };
          
    }( jQuery ) );

    
    

</script>
