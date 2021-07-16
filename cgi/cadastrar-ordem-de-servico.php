<?php

include 'inc/functions.inc.php';

is_logged_usuario();

extract($_GET);
extract($_POST);

if(isset($id))
    if(!is_numeric($id))
        $id = base64_decode($id);


$conn = TConnection::open(DB);

define('MAX_ARQUIVO_ORDEM_SERVICO', 6);

$observacoes = null;
$preco = null;
if(!isset($op)){

    $cliente_id = $nome = $wpp = $telefone = $email = $instagram = $cpf = $ordem_de_servico_prioritaria = $aprovacao_da_arte_pelo = $servico_deve_vir = $cor_do_material = $cor_da_impressao = $informacoes_sobre_o_servico = $produto_id = $produto = $quantidade = $sub_total = $desconto_em_porcentagem = $desconto_em_reais = $total = $data_de_finalizacao = $data_do_evento = $hora_do_evento = $imei_ou_n_de_serie = $observacoes = $tecnico_responsavel = $servico = $preco = null;

    $data_de_abertura = date('d/m/Y');

    $fotos_qtd = $arquivos_aprovacao_qtd = 1;

    $status = 2; //aguardando revisão
   
    $op = 'novo';
    $id = $idOS = 0;

    $popup = null;


}elseif($op=='novo' or $op=='editar'){

    $conn->beginTransaction();

    $permitidos = array('cliente_id', 'nome', 'wpp', 'telefone', 'email', 'instagram', 'cpf', 'data_de_abertura', 'data_de_finalizacao', 'observacoes', 'status', 
    'os_botoes_fisicos_e_funcoes_id_funcionam', 'os_sinais_de_rede_wifi_bluetooth_funcionam', 'as_cameras_altofalantes_e_microfones_funcionam',
    'sensor_de_aproximacao', 'flash___lanterna', 'nao_apresenta_todos_os_parafusos_externos', 'saude_da_bateria', 'o_iphone_esta_carregando_normalmente',
    'carregamento_por_inducao_funciona', 'qual_a_amperagem_da_bateria', 'imei_ou_n_de_serie', 'ordem_de_servico_prioritaria', 'tecnico_responsavel', 
    'iphone_liga' );

    $criterio = new TCriteria();
    $criterio->add(new TFilter('status', '=', 1));

    !isset($ordem_de_servico_prioritaria) ? $ordem_de_servico_prioritaria = false : $ordem_de_servico_prioritaria = true;

    $data_de_abertura = formataDataInv($data_de_abertura);
    $data_de_finalizacao = formataDataInv($data_de_finalizacao);
    //$preco = real($preco, 'float');
    

    $args[] = array('email', '=', $_COOKIE['email']);
    $usuario = usuario_get($args);

    

    if($op=='novo'){

        //inserindo ordem de servico
        $sql = new TSqlInsert();
        $sql->setEntity('ordem_de_servico');
        foreach ($permitidos as $key => $value) {
            $sql->setRowData($value, $$value);
        }
        $sql->setRowData('usuario_id', $usuario[0]['id']);
        $sql->setRowData('filial_id', $_COOKIE['filial']);
        $result = $conn->query($sql->getInstruction());
        $idOS = $conn->lastInsertId();

        

        if($result->rowCount()){
            
            $pop = new popup(
            "Ordem de serviço criada com sucesso.");
            $popup = $pop->get_pop();


        }else{
          
            $pop = new popup(
            "Não foi possível criar a ordem de serviço.<br>
            Motivo: " . implode(' | ', $conn->errorInfo()), 'amarelo');
            $popup = $pop->get_pop();
            
        }

        //gerando pedido
        $sql = new TSqlInsert();
        $sql->setEntity('venda');
        $sql->setRowData('ordem_de_servico_id', $idOS);
        $sql->setRowData('data', date('Y-m-d'));
        $sql->setRowData('usuario_id', $usuario[0]['id']);
        $sql->setRowData('status', 1);
        $sql->setRowData('filial_id', $_COOKIE['filial']);
        $result = $conn->query($sql->getInstruction());

    }elseif($op=='editar'){
        $idOS = $id;
        if(!is_numeric($idOS))
            $idOS = base64_decode($idOS);

        $criterio = new TCriteria();
        $criterio->add(new TFilter('id', '=', $idOS));

        //atualizando venda
        $sql = new TSqlUpdate();
        $sql->setEntity('ordem_de_servico');
        foreach ($permitidos as $key => $value) {
            $sql->setRowData($value, $$value);
        }
        $sql->setCriteria($criterio);
        
        $result = $conn->query($sql->getInstruction());

        if($result->rowCount()){
            
            $pop = new popup(
            "Ordem de serviço editada com sucesso.");
            $popup = $pop->get_pop();


        }else{
          
            $pop = new popup(
            "Não foi possível editar a ordem de serviço.<br>
            Motivo: " . implode(' | ', $conn->errorInfo()), 'amarelo');
            $popup = $pop->get_pop();
            
        }



    }

    

    //deletando atributos caso exista
    $criterio = new TCriteria();
    $criterio->add(new TFilter('ordem_de_servico_id', '=', $idOS));

    $sql = new TSqlDelete();
    $sql->setEntity('ordem_de_servico_atributos');
    $sql->setCriteria($criterio);
    $conn->query($sql->getInstruction());

    //salvando perguntas do check list
    $criterio = new TCriteria();
    $criterio->add(new TFilter('status', '=', 1));

    $sql = new TSqlSelect();
    $sql->setEntity('ordem_de_servico_perguntas');
    $sql->addColumn('pergunta');
    $sql->setCriteria($criterio);
    $result = $conn->query($sql->getInstruction());
    
    for($i=0; $i<$result->rowCount(); $i++){
        extract($result->fetch(PDO::FETCH_ASSOC));
        $pergunta = strtolower(strtr($pergunta, unserialize(CHAR_MAP)));
        !isset($$pergunta) ? $$pergunta = 1: $$pergunta = 0;

        $sql = new TSqlInsert();
        $sql->setEntity('ordem_de_servico_atributos');
        $sql->setRowData('ordem_de_servico_id' , $idOS);
        $sql->setRowData('tipo' , $pergunta);
        $sql->setRowData('valor' , $$pergunta);
        $conn->query($sql->getInstruction());
    }
    



    //salvandos arquivos
    for($i=1; $i<=MAX_ARQUIVO_ORDEM_SERVICO; $i++){

        if(isset($_FILES['fotos_do_celular_'.$i])){
            if($_FILES['fotos_do_celular_'.$i]['size']>0){
                $pasta = END_OS.$idOS.'/';
                $nome_imagem = $_FILES['fotos_do_celular_'.$i]['name'];
                $ext = strtolower(strrchr($nome_imagem, "."));
                $nome_atual = strtr(strtolower('fotos_do_celular_'.md5(uniqid()).'_'.time()), unserialize(CHAR_MAP)) . $ext;
                $tmp = $_FILES['fotos_do_celular_'.$i]['tmp_name'];
                if (!is_dir($pasta)) {
                    mkdir($pasta, 0755, true);
                    chmod($pasta, 0755);
                }
                
                if(move_uploaded_file($tmp, $pasta.$nome_atual)){
                    
                    $sql = new TSqlInsert();
                    $sql->setEntity('ordem_de_servico_atributos');
                    $sql->setRowData('ordem_de_servico_id', $idOS);
                    $sql->setRowData('tipo', 'fotos_do_celular');
                    $sql->setRowData('valor', $pasta . $nome_atual);
                    $result = $conn->query($sql->getInstruction());
                    $foto = $pasta.$nome_atual;
                }
            }
        }
    }
    



    $op = 'editar';

    $data_de_abertura = formataData($data_de_abertura);
    $data_de_finalizacao = formataData($data_de_finalizacao);
    $preco = real($preco, 'real');


    if($conn->inTransaction()){
        $conn->commit();
    }
    
    redirecionar('cadastrar-ordem-de-servico.php?op=visualizar&id='.base64_encode($idOS), 2);

}elseif($op=='deletar' or $op=="ativar" or $op=="visualizar"){

    $cliente_id = $nome = $wpp = $telefone = $email = $instagram = $cpf = $ordem_de_servico_prioritaria = $aprovacao_da_arte_pelo = $servico_deve_vir = $cor_do_material = $cor_da_impressao = $informacoes_sobre_o_servico = $produto_id = $produto = $quantidade = $sub_total = $desconto_em_porcentagem = $desconto_em_reais = $total = $data_de_finalizacao = $data_do_evento = $hora_do_evento = $imei_ou_n_de_serie = $observacoes = $tecnico_responsavel = $servico = $preco = null;

    $status = 1;
   
    $idOS = $id;
    if(!is_numeric($idOS))
        $idOS = base64_decode($idOS);
    

    if($op=='deletar' or $op=='ativar'){

        if($op=="deletar"){
            $operacao = 'deletado';
        }elseif($op=="ativar"){
            $operacao = 'ativado';
        }
        
        $criterio = new TCriteria();
        $criterio->add(new TFilter('id', '=', $idVenda));
        
        $sql = new TSqlUpdate();
        $sql->setEntity('ordem_de_servico');
        if($op=="deletar"){
            $sql->setRowData('status', 0);
        }elseif($op=="ativar"){
            $sql->setRowData('status', 1);
        }
        $sql->setCriteria($criterio);
        $result = $conn->query($sql->getInstruction());

        if($result->rowCount()){
            $pop = new popup("Venda $operacao com sucesso.<br>");
            $popup = $pop->get_pop();
        }else{
            $pop = new popup("Venda não pode ser $operacao.<br>", 'vermelho');
            $popup = $pop->get_pop();
        }
    }

    $criterio = new TCriteria();
    $criterio->add(new TFilter('id', '=', $idOS));

    $sql = new TSqlSelect();
    $sql->setEntity('ordem_de_servico');
    $sql->addColumn('*');
    $sql->setCriteria($criterio);
    $result = $conn->query($sql->getInstruction());

    if($result->rowCount()){
        extract($result->fetch(PDO::FETCH_ASSOC));

        $data_de_abertura = formataData($data_de_abertura);
        $data_de_finalizacao = formataData($data_de_finalizacao);
        $data_do_evento = formataData($data_do_evento);
        $sub_total = real($sub_total, 'real');
        $desconto_em_porcentagem = real($desconto_em_porcentagem, 'real');
        $desconto_em_reais = real($desconto_em_reais, 'real');
        $total = real($total, 'real');



    }

    
    $op = 'editar';
    $popup = null;

}


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

$criterio = new TCriteria();
$criterio->add(new TFilter('status', '<>', 1));
$criterio->setProperty('group', 'nome');


$sql = new TSqlSelect();
$sql->setEntity('ordem_de_servico');
$sql->addColumn('tecnico_responsavel as tecnico');
$sql->setCriteria($criterio);


$result = $conn->query($sql->getInstruction());

$tecnicos = array();

for($i=0; $i<$result->rowCount(); $i++){
    extract($result->fetch(PDO::FETCH_ASSOC));

    $tecnicos[] = $tecnico;

}

function select_array($array){
    $array2 = array();
    foreach ($array as $key => $value) {
        $array2[] = array($value, $value);
    }
    return $array2;
}

$form = new form('Cadastrar Ordem de Serviço', null, $op);
$form->id = $idOS;
$form->tabela = 'ordem_de_servico';

$codpopup = null;
if($op=='novo'){

    
    $form->set_arg('hidden', 'cliente_id', null, $cliente_id);

    $form->set_arg_generic('
    <div class="col-sm-12">
        <div class="clearfix">
            <span class="pull-left">Progresso</span>
            <small class="pull-right" id="bar-info">Inserindo dados do cliente</small>
        </div>
        <div class="progress xs">
            <div class="progress-bar progress-bar-blue" id="bar-pro" style="width: 12.5%;"></div>
        </div>
    </div>');

    ####################### PARTE 1 #######################
    $parte = 1;
    $form->colspan = 4;
    if(isset($error['nome']))
        $form->error = true;
    $form->max = 250;
    $form->ajax_valores = array_values($autocomplete);
    $form->ajax_index = array_values($index);
    $form->div_class = 'parte_'.$parte;
    $form->set_js(null, 'onblur="filtro(\'parte_'.$parte.'\');"');
    $form->set_arg('ajaxtext', 'Nome', 'Nome do cliente', $nome);

    $form->colspan = 4;
    if(isset($error['wpp']))
        $form->error = true;
    $form->max = 250;
    $form->div_class = 'parte_'.$parte;
    $form->set_js(null, 'onblur="filtro(\'parte_'.$parte.'\');"');
    $form->set_arg('text', 'Wpp', '(88) 0000.00000', $wpp, 'tel');

    $form->colspan = 4;
    if(isset($error['telefone']))
        $form->error = true;
    $form->max = 250;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg('text', 'Telefone', '(88) 0000.00000', $telefone, 'tel');


    $form->colspan = 4;
    if(isset($error['email']))
        $form->error = true;
    $form->max = 250;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg('email', 'Email', 'email@dominio.com', $email);
    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg('text', 'Instagram', '@perfil.do.cliente', $instagram, 'instagram');
    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte;
    $form->set_js(null, 'onblur="filtro(\'parte_'.$parte.'\');"');
    $form->set_arg('text', 'CPF', '000.000.000-00', $cpf, 'cpf');

    $form->colspan = 12;
    $form->set_arg_generic('<span id="parte_'.$parte.'_pro" class="btn btn-default pull-right">Próximo</span>');



    $botoes = 'Check List';
    $form->div_class = 'parte';
    $form->set_arg('h3', $botoes);

    $opcoes[0][] = '1';
    $opcoes[0][] = 'Sim';
    $opcoes[1][] = '0';
    $opcoes[1][] = 'Não';  

    ####################### PARTE 2 #######################
    $parte = 2;
    $iphone_liga = '--';

    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic('<img style="display: block; margin-left: auto; margin-right: auto;max-height: 150px;" src="img\os_icons\telefone_liga.png">');

    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte;
    //$form->set_js('function parte_2(v){  }', 'onchange="parte_2(this.value);"');
    $form->set_js(null, 'onchange="filtro(\'parte_'.$parte.'\');"');
    $form->set_arg('select', 'Iphone liga', $opcoes, $iphone_liga);

    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic(null);

    $form->colspan = 12;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic('<span id="parte_'.$parte.'_vol" onclick="proximo('.($parte-1).');" class="btn btn-danger pull-left">Voltar</span>
    <span id="parte_'.$parte.'_pro" class="btn btn-default pull-right">Próximo</span>');



    ####################### PARTE 3 #######################
    $parte = 3;
    $os_botoes_fisicos_e_funcoes_id_funcionam = '--';

    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic('<img style="display: block; margin-left: auto; margin-right: auto;max-height: 150px;" src="img\os_icons\os_botoes_fisicos_e_funcoes_id_funcionam.png">');

    $form->colspan = 4;
    $form->tooltip = 'Selecione o status da ordem de serviço.';
    $form->div_class = 'parte_'.$parte;
    //$form->set_js('function parte_2(v){  }', 'onchange="parte_2(this.value);"');
    $form->set_js(null, 'onchange="filtro(\'parte_'.$parte.'\');"');
    $form->set_arg('select', 'Os botões físicos e funções ID funcionam', $opcoes, $os_botoes_fisicos_e_funcoes_id_funcionam);

    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte.'_b';
    $form->set_js('$(function(){ if($("select[name=\'os_botoes_fisicos_e_funcoes_id_funcionam\']").val()!=\'1\'){ $("parte_'.$parte.'_b").hide();}});', null);
    $form->set_arg_generic('<h2>Quais Botões</h2><br>
    <input type="checkbox" id="botao_power" name="botao_power" value="0"> <label for="botao_power">Botão Power</label><br>
    <input type="checkbox" id="botao_home" name="botao_home" value="0"> <label for="botao_home">Botão Home</label><br>
    <input type="checkbox" id="botao_de_volume" name="botao_de_volume" value="0"> <label for="botao_de_volume">Botão de volume</label><br>
    <input type="checkbox" id="botao_de_silenciar" name="botao_de_silenciar" value="0"> <label for="botao_de_silenciar">Botão de silenciar</label><br>
    <input type="checkbox" id="botao_touch_id" name="touch_id" value="0"> <label for="botao_touch_id">Touch ID</label><br>
    <input type="checkbox" id="botao_face_id" name="face_id" value="0"> <label for="botao_face_id">Face ID</label>');

    $form->colspan = 12;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic('<span id="parte_'.$parte.'_vol" onclick="proximo('.($parte-1).');" class="btn btn-danger pull-left">Voltar</span>
    <span id="parte_'.$parte.'_pro" class="btn btn-default pull-right">Próximo</span>');



    ####################### PARTE 4 #######################
    $parte = 4;
    $os_sinais_de_rede_wifi_bluetooth_funcionam = '--';

    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic('<img style="display: block; margin-left: auto; margin-right: auto;max-height: 150px;" src="img\os_icons\os_sinais_de_rede__wifi__bluetooth_funcionam.png">');

    $form->colspan = 4;
    $form->tooltip = 'Selecione o status da ordem de serviço.';
    $form->div_class = 'parte_'.$parte;
    //$form->set_js('function parte_2(v){  }', 'onchange="parte_2(this.value);"');
    $form->set_js(null, 'onchange="filtro(\'parte_'.$parte.'\');"');
    $form->set_arg('select', 'Os sinais de rede, WIFI, bluetooth funcionam', $opcoes, $os_sinais_de_rede_wifi_bluetooth_funcionam);

    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte.'_b';
    $form->set_js('$(function(){ if($("select[name=\'os_botoes_fisicos_e_funcoes_id_funcionam\']").val()!=\'1\'){ $("parte_'.$parte.'_b").hide();}});', null);
    $form->set_arg_generic('<h2>Quais componentes não estão funcionando?</h2><br>
    <input type="checkbox" id="rede_wifi" name="wifi" value="0"> <label for="rede_wifi">WIFI</label><br>
    <input type="checkbox" id="rede_wifi_intermitente" name="wifi_intermitente" value="0"> <label for="rede_wifi_intermitente">WIFI Intermitente</label><br>
    <input type="checkbox" id="rede_bluetooth" name="bluetooth" value="0"> <label for="rede_bluetooth">Bluetooth</label><br>
    <input type="checkbox" id="rede_sinal_operadora" name="sinal_da_operadora" value="0"> <label for="rede_sinal_operadora">Sinal da operadora</label>');

    $form->colspan = 12;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic('<span id="parte_'.$parte.'_vol" onclick="proximo('.($parte-1).');" class="btn btn-danger pull-left">Voltar</span>
    <span id="parte_'.$parte.'_pro" class="btn btn-default pull-right">Próximo</span>');


    ####################### PARTE 5 #######################
    $parte = 5;
    $as_cameras_altofalantes_e_microfones_funcionam = '--';

    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic('<img style="display: block; margin-left: auto; margin-right: auto;max-height: 150px;" src="img\os_icons\as_cameras__alto_falantes_e_microfones_funcionam.png">');

    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte;
    //$form->set_js('function parte_2(v){  }', 'onchange="parte_2(this.value);"');
    $form->set_js(null, 'onchange="filtro(\'parte_'.$parte.'\');"');
    $form->set_arg('select', 'As câmeras, alto-falantes e microfones funcionam', $opcoes, $as_cameras_altofalantes_e_microfones_funcionam);

    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte.'_b';
    $form->set_js('$(function(){ if($("select[name=\'os_botoes_fisicos_e_funcoes_id_funcionam\']").val()!=\'1\'){ $("parte_'.$parte.'_b").hide();}});', null);
    $form->set_arg_generic('<h2>Quais componentes não estão funcionando?</h2><br>
    <input type="checkbox" id="ca_alto_falante_principal" name="autofalante_principal" value="0"> <label for="ca_alto_falante_principal">Auto-falante principal</label><br>
    <input type="checkbox" id="ca_camera_frontal" name="camera_frontal" value="0"> <label for="ca_camera_frontal">Câmera frontal</label><br>
    <input type="checkbox" id="ca_microfrone_frontal" name="microfone_frontal" value="0"> <label for="ca_microfrone_frontal">Micfrofone frontal</label><br>
    <input type="checkbox" id="ca_auto_falante_auricular" name="auto_falante_auricular" value="0"> <label for="ca_auto_falante_auricular">Auto falante auricular</label><br>
    <input type="checkbox" id="ca_camera_traseira_1x" name="camera_traseira_1x" value="0"> <label for="ca_camera_traseira_1x">Camera Traseira 1x</label><br>
    <input type="checkbox" id="ca_camera_traseira_2x" name="camera_traseira_2x" value="0"> <label for="ca_camera_traseira_2x">Camera Traseira 2x</label><br>
    <input type="checkbox" id="ca_camera_traseira_3x" name="camera_traseira_3x" value="0"> <label for="ca_camera_traseira_3x">Camera Traseira 3x</label><br>
    <input type="checkbox" id="ca_microfone_traseiro" name="microfone_traseiro" value="0"> <label for="ca_microfone_traseiro">Microfone traseiro</label><br>
    <input type="checkbox" id="ca_microfone_principal" name="microfone_principal" value="0"> <label for="ca_microfone_principal">Microfone principal</label><br>');

    $form->colspan = 12;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic('<span id="parte_'.$parte.'_vol" onclick="proximo('.($parte-1).');" class="btn btn-danger pull-left">Voltar</span>
    <span id="parte_'.$parte.'_pro" class="btn btn-default pull-right">Próximo</span>');


    ####################### PARTE 6 #######################
    $parte = 6;

    $sensor_de_aproximacao = $flash___lanterna = $nao_apresenta_todos_os_parafusos_externos = 1;

    $form->colspan = 12;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic('As demais funções externas do iphone estão funcionando?');

    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg('select', 'Sensor de aproximação', $opcoes, $sensor_de_aproximacao);

    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg('select', 'Flash / Lanterna', $opcoes, $flash___lanterna);

    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg('select', 'Não apresenta todos os parafusos externos', $opcoes, $nao_apresenta_todos_os_parafusos_externos);

    $form->colspan = 12;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic('<span id="parte_'.$parte.'_vol" onclick="proximo('.($parte-1).');" class="btn btn-danger pull-left">Voltar</span>
    <span id="parte_'.$parte.'_pro" onclick="proximo('.($parte+1).');" class="btn btn-primary pull-right">Próximo</span>');


    ####################### PARTE 7 #######################
    $parte = 7;

    $saude_da_bateria = $qual_a_amperagem_da_bateria = null;
    $o_iphone_esta_carregando_normalmente = $carregamento_por_inducao_funciona = 1;

    $form->colspan = 12;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic('Como está a bateria e o carregamento do iphone');

    $form->colspan = 6;
    $form->div_class = 'parte_'.$parte;
    $form->max = 10;
    $form->input_group_addon_back = '%';
    $form->set_arg('text', 'Saúde da bateria', '00,00', real($saude_da_bateria, 'real'), 'Valor2');

    $form->colspan = 6;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg('select', 'O iphone está carregando normalmente', $opcoes, $o_iphone_esta_carregando_normalmente);

    $form->colspan = 6;
    $form->div_class = 'parte_'.$parte;
    $form->max = 10;
    $form->input_group_addon_back = 'A';
    $form->set_arg('text', 'Qual a amperagem da bateria', '00,00', real($qual_a_amperagem_da_bateria, 'real'), 'Valor2');

    $form->colspan = 6;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg('select', 'Carregamento por indução funciona', $opcoes, $carregamento_por_inducao_funciona);

    $form->colspan = 12;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic('<span id="parte_'.$parte.'_vol" onclick="proximo('.($parte-1).');" class="btn btn-danger pull-left">Voltar</span>
    <span id="parte_'.$parte.'_pro" onclick="proximo('.($parte+1).');" class="btn btn-primary pull-right">Próximo</span>');


    ####################### PARTE 8 #######################
    $parte = 8;
    $form->max = 10;
    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg('text', 'Data de abertura', 'dd/mm/AAAA', $data_de_abertura, 'data');
    $form->max = 10;
    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg('text', 'Data de finalização', 'dd/mm/AAAA', $data_de_finalizacao, 'data');

    $criterio = new TCriteria();
    $criterio->add(new TFilter('status', '=', 1));

    $sql = new TSqlSelect();
    $sql->setEntity('ordem_de_servico_status');
    $sql->addColumn('nome as statusNome');
    $sql->setCriteria($criterio);

    $result = $conn->query($sql->getInstruction());

    for($i=0; $i<$result->rowCount(); $i++){
        extract($result->fetch(PDO::FETCH_ASSOC));
        $statusNomes[] = array($i+1, $statusNome);
    }

    $form->colspan = 4;
    $form->tooltip = 'Selecione o status da ordem de serviço.';
    $form->div_class = 'parte_'.$parte;
    $form->set_js(null, 'onchange="verifica_status(this.value);"');
    $form->set_arg('select', 'Status', $statusNomes, $status);

    $form->colspan = 4;
    $var = 'Ordem de serviço prioritária';
    $form->tooltip = 'Se esta ordem de serviço for prioridade, marque esta opção e ela irá ficar em destaque no mapa de produção.';
    !isset($ordem_de_servico_prioritaria) ? $ordem_de_servico_prioritaria = false : false;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg('checkbox_array', $var, $ordem_de_servico_prioritaria, $var, $var);

    $form->colspan = 4;
    if(isset($error['imei_ou_n_de_serie']))
        $form->error = true;
    $form->max = 50;
    $form->div_class = 'parte_'.$parte;
    $form->set_js(null, 'onchange="filtro(\'parte_'.$parte.'\');"');
    $form->set_arg('text', 'IMEI ou N de Serie', '000000', $imei_ou_n_de_serie);

    $form->colspan = 4;
    $form->max = 50;
    $form->div_class = 'parte_'.$parte;
    $form->ajax_valores = array_values($tecnicos);
    $form->set_arg('ajaxtext', 'Técnico Responsavel', 'Nome do técnico', $tecnico_responsavel);

    $form->colspan = 12;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic('<span id="parte_'.$parte.'_vol" onclick="proximo('.($parte-1).', true);" class="btn btn-danger pull-left">Voltar</span>
    <span id="parte_'.$parte.'_pro" onclick="salvar();" class="btn btn-default pull-right">Salvar</span>');
}else{

    $criterio = new TCriteria();
    $criterio->add(new TFilter('ordem_de_servico_id', '=', $idOS));

    $sql = new TSqlSelect();
    $sql->setEntity('ordem_de_servico_atributos');
    $sql->addColumn('tipo');
    $sql->addColumn('valor');
    $sql->setCriteria($criterio);
    $result = $conn->query($sql->getInstruction());

    for($i=0; $i<$result->rowCount(); $i++){
        extract($result->fetch(PDO::FETCH_ASSOC));
        $$tipo = $valor;
    }

    $wpplink = str_replace('(', '', $wpp);
    $wpplink = str_replace(')', '', $wpplink);
    $wpplink = str_replace(' ', '', $wpplink);
    $wpplink = str_replace('.', '', $wpplink);
    $wpplink = '55'.$wpplink;

    $texto = urlencode('Olá '.$nome.', estou enviando o link para visualização da ordem de serviço '.$idOS.' '.
    $_SERVER['HTTP_HOST'].str_replace('cadastrar-ordem-de-servico.php', '', $_SERVER['HTTP_REFERER']).'imp/ordem-de-servico.php?id='.base64_encode($idOS));

    $form->colspan = 12;
    
    $link = '
    <span style="float:left;">
        ORDEM DE SERVIÇO '.$idOS.'
    </span>
    <span style="float:right;">
        <a target="blank_" href="imp/ordem-de-servico.php?id='.base64_encode($idOS).'" style="padding-right: 20px;">
            <span class="fa fa-print" data-toggle="tooltip" title="Imprimir"></span>
        </a> 
        <a href="cadastrar-venda.php?op=visualizar&idOS='.base64_encode($idOS).'" style="padding-right: 20px;">
            <span class="fa fa-file-text-o" data-toggle="tooltip" title="Venda"></span>
        </a> 
        <a href="javascript:void(0);" onclick=" window.open(\'https://api.whatsapp.com/send?phone='.$wpplink.'&text='.$texto.'\',\'page\');" style="padding-right: 20px;">
            <span class="fa fa-phone-square" data-toggle="tooltip" title="Enviar WhatsApp"></span>
        </a>
    </span>';
    $form->set_arg('h3', $link);


    $botoes = 'Check List';
    $form->div_class = 'parte';
    $form->set_arg('h3', $botoes);

    $opcoes[0][] = '1';
    $opcoes[0][] = 'Sim';
    $opcoes[1][] = '0';
    $opcoes[1][] = 'Não';  


    $form->set_arg('hidden', 'cliente_id', null, $cliente_id);
    ####################### PARTE 1 #######################
    $parte = 1;
    $form->colspan = 4;
    if(isset($error['nome']))
        $form->error = true;
    $form->max = 250;
    $form->ajax_valores = array_values($autocomplete);
    $form->ajax_index = array_values($index);
    $form->div_class = 'parte_'.$parte;
    $form->set_js(null, 'onblur="filtro(\'parte_'.$parte.'\');"');
    $form->set_arg('ajaxtext', 'Nome', 'Nome do cliente', $nome);

    $form->colspan = 4;
    if(isset($error['wpp']))
        $form->error = true;
    $form->max = 250;
    $form->div_class = 'parte_'.$parte;
    $form->set_js(null, 'onblur="filtro(\'parte_'.$parte.'\');"');
    $form->set_arg('text', 'Wpp', '(88) 0000.00000', $wpp, 'tel');

    $form->colspan = 4;
    if(isset($error['telefone']))
        $form->error = true;
    $form->max = 250;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg('text', 'Telefone', '(88) 0000.00000', $telefone, 'tel');


    $form->colspan = 4;
    if(isset($error['email']))
        $form->error = true;
    $form->max = 250;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg('email', 'Email', 'email@dominio.com', $email);
    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg('text', 'Instagram', '@perfil.do.cliente', $instagram, 'instagram');
    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte;
    $form->set_js(null, 'onblur="filtro(\'parte_'.$parte.'\');"');
    $form->set_arg('text', 'CPF', '000.000.000-00', $cpf, 'cpf');

    $form->colspan = 12;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic('');


    ####################### PARTE 2 #######################
    $parte = 2;

    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic('<img style="display: block; margin-left: auto; margin-right: auto;max-height: 150px;" src="img\os_icons\telefone_liga.png">');

    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte;
    //$form->set_js('function parte_2(v){  }', 'onchange="parte_2(this.value);"');
    $form->set_js(null, 'onchange="filtro(\'parte_'.$parte.'\');"');
    $form->set_arg('select', 'Iphone liga', $opcoes, $iphone_liga);

    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic(null);

    $form->colspan = 12;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic('');

    ####################### PARTE 3 #######################
    $parte = 3;
    $botao_power == 0 ? $botao_power = 'checked="yes"' : $botao_power = null;
    $botao_home == 0 ? $botao_home = 'checked="yes"' : $botao_home = null;
    $botao_de_volume == 0 ? $botao_de_volume = 'checked="yes"' : $botao_de_volume = null;
    $botao_de_silenciar == 0 ? $botao_de_silenciar = 'checked="yes"' : $botao_de_silenciar = null;
    $touch_id == 0 ? $touch_id = 'checked="yes"' : $touch_id = null;
    $face_id == 0 ? $face_id = 'checked="yes"' : $face_id = null;
    
    
    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic('<img style="display: block; margin-left: auto; margin-right: auto;max-height: 150px;" src="img\os_icons\os_botoes_fisicos_e_funcoes_id_funcionam.png">');

    $form->colspan = 4;
    $form->tooltip = 'Selecione o status da ordem de serviço.';
    $form->div_class = 'parte_'.$parte;
    //$form->set_js('function parte_2(v){  }', 'onchange="parte_2(this.value);"');
    $form->set_js(null, 'onchange="filtro(\'parte_'.$parte.'\');"');
    $form->set_arg('select', 'Os botões físicos e funções ID funcionam', $opcoes, $os_botoes_fisicos_e_funcoes_id_funcionam);

    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte.'_b';
    $form->set_js('$(function(){ if($("select[name=\'os_botoes_fisicos_e_funcoes_id_funcionam\']").val()!=\'1\'){ $("parte_'.$parte.'_b").hide();}});', null);
    $form->set_arg_generic('<h2>Quais Botões</h2><br>
    <input type="checkbox" id="botao_power" name="botao_power" value="0" '.$botao_power.'> <label for="botao_power">Botão Power</label><br>
    <input type="checkbox" id="botao_home" name="botao_home" value="0" '.$botao_home.'> <label for="botao_home">Botão Home</label><br>
    <input type="checkbox" id="botao_de_volume" name="botao_de_volume" value="0" '.$botao_de_volume.'> <label for="botao_de_volume">Botão de volume</label><br>
    <input type="checkbox" id="botao_de_silenciar" name="botao_de_silenciar" value="0" '.$botao_de_silenciar.'> <label for="botao_de_silenciar">Botão de silenciar</label><br>
    <input type="checkbox" id="botao_touch_id" name="touch_id" value="0" '.$touch_id.'> <label for="botao_touch_id">Touch ID</label><br>
    <input type="checkbox" id="botao_face_id" name="face_id" value="0" '.$face_id.'> <label for="botao_face_id">Face ID</label>');

    $form->colspan = 12;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic('');


    ####################### PARTE 4 #######################
    $parte = 4;
    $wifi == 0 ? $wifi = 'checked="yes"' : $wifi = null;
    $wifi_intermitente == 0 ? $wifi_intermitente = 'checked="yes"' : $wifi_intermitente = null;
    $bluetooth == 0 ? $bluetooth = 'checked="yes"' : $bluetooth = null;
    $sinal_da_operadora == 0 ? $sinal_da_operadora = 'checked="yes"' : $sinal_da_operadora = null;
    
    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic('<img style="display: block; margin-left: auto; margin-right: auto;max-height: 150px;" src="img\os_icons\os_sinais_de_rede__wifi__bluetooth_funcionam.png">');

    $form->colspan = 4;
    $form->tooltip = 'Selecione o status da ordem de serviço.';
    $form->div_class = 'parte_'.$parte;
    //$form->set_js('function parte_2(v){  }', 'onchange="parte_2(this.value);"');
    $form->set_js(null, 'onchange="filtro(\'parte_'.$parte.'\');"');
    $form->set_arg('select', 'Os sinais de rede, WIFI, bluetooth funcionam', $opcoes, $os_sinais_de_rede_wifi_bluetooth_funcionam);

    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte.'_b';
    $form->set_js('$(function(){ if($("select[name=\'os_botoes_fisicos_e_funcoes_id_funcionam\']").val()!=\'1\'){ $("parte_'.$parte.'_b").hide();}});', null);
    $form->set_arg_generic('<h2>Quais componentes não estão funcionando?</h2><br>
    <input type="checkbox" id="rede_wifi" name="wifi" value="0" '.$wifi.'> <label for="rede_wifi">WIFI</label><br>
    <input type="checkbox" id="rede_wifi_intermitente" name="wifi_intermitente" value="0" '.$wifi_intermitente.'> <label for="rede_wifi_intermitente">WIFI Intermitente</label><br>
    <input type="checkbox" id="rede_bluetooth" name="bluetooth" value="0" '.$bluetooth.'> <label for="rede_bluetooth">Bluetooth</label><br>
    <input type="checkbox" id="rede_sinal_operadora" name="sinal_da_operadora" value="0" '.$sinal_da_operadora.'> <label for="rede_sinal_operadora">Sinal da operadora</label>');

    $form->colspan = 12;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic('');

    ####################### PARTE 5 #######################
    $parte = 5;
    
    
    $autofalante_principal == 0 ? $autofalante_principal = 'checked="yes"' : $autofalante_principal = null;
    $camera_frontal == 0 ? $camera_frontal = 'checked="yes"' : $camera_frontal = null;
    $microfone_frontal == 0 ? $microfone_frontal = 'checked="yes"' : $microfone_frontal = null;
    $auto_falante_auricular == 0 ? $auto_falante_auricular = 'checked="yes"' : $auto_falante_auricular = null;
    $camera_traseira_1x == 0 ? $camera_traseira_1x = 'checked="yes"' : $camera_traseira_1x = null;
    $camera_traseira_2x == 0 ? $camera_traseira_2x = 'checked="yes"' : $camera_traseira_2x = null;
    $camera_traseira_3x == 0 ? $camera_traseira_3x = 'checked="yes"' : $camera_traseira_3x = null;
    $microfone_traseiro == 0 ? $microfone_traseiro = 'checked="yes"' : $microfone_traseiro = null;
    $microfone_principal == 0 ? $microfone_principal = 'checked="yes"' : $microfone_principal = null;
    
    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic('<img style="display: block; margin-left: auto; margin-right: auto;max-height: 150px;" src="img\os_icons\as_cameras__alto_falantes_e_microfones_funcionam.png">');

    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte;
    //$form->set_js('function parte_2(v){  }', 'onchange="parte_2(this.value);"');
    $form->set_js(null, 'onchange="filtro(\'parte_'.$parte.'\');"');
    $form->set_arg('select', 'As câmeras, alto-falantes e microfones funcionam', $opcoes, $as_cameras_altofalantes_e_microfones_funcionam);

    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte.'_b';
    $form->set_js('$(function(){ if($("select[name=\'os_botoes_fisicos_e_funcoes_id_funcionam\']").val()!=\'1\'){ $("parte_'.$parte.'_b").hide();}});', null);
    $form->set_arg_generic('<h2>Quais componentes não estão funcionando?</h2><br>
    <input type="checkbox" id="ca_alto_falante_principal" name="autofalante_principal" value="0" '.$autofalante_principal.'> <label for="ca_alto_falante_principal">Auto-falante principal</label><br>
    <input type="checkbox" id="ca_camera_frontal" name="camera_frontal" value="0" '.$camera_frontal.'> <label for="ca_camera_frontal">Câmera frontal</label><br>
    <input type="checkbox" id="ca_microfrone_frontal" name="microfone_frontal" value="0" '.$microfone_frontal.'> <label for="ca_microfrone_frontal">Micfrofone frontal</label><br>
    <input type="checkbox" id="ca_auto_falante_auricular" name="auto_falante_auricular" value="0" '.$auto_falante_auricular.'> <label for="ca_auto_falante_auricular">Auto falante auricular</label><br>
    <input type="checkbox" id="ca_camera_traseira_1x" name="camera_traseira_1x" value="0" '.$camera_traseira_1x.'> <label for="ca_camera_traseira_1x">Camera Traseira 1x</label><br>
    <input type="checkbox" id="ca_camera_traseira_2x" name="camera_traseira_2x" value="0" '.$camera_traseira_2x.'> <label for="ca_camera_traseira_2x">Camera Traseira 2x</label><br>
    <input type="checkbox" id="ca_camera_traseira_3x" name="camera_traseira_3x" value="0" '.$camera_traseira_3x.'> <label for="ca_camera_traseira_3x">Camera Traseira 3x</label><br>
    <input type="checkbox" id="ca_microfone_traseiro" name="microfone_traseiro" value="0" '.$microfone_traseiro.'> <label for="ca_microfone_traseiro">Microfone traseiro</label><br>
    <input type="checkbox" id="ca_microfone_principal" name="microfone_principal" value="0" '.$microfone_principal.'> <label for="ca_microfone_principal">Microfone principal</label><br>');

    $form->colspan = 12;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic('');

    ####################### PARTE 6 #######################
    $parte = 6;

    $form->colspan = 12;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic('As demais funções externas do iphone estão funcionando?');

    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg('select', 'Sensor de aproximação', $opcoes, $sensor_de_aproximacao);

    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg('select', 'Flash / Lanterna', $opcoes, $flash___lanterna);

    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg('select', 'Não apresenta todos os parafusos externos', $opcoes, $nao_apresenta_todos_os_parafusos_externos);

    $form->colspan = 12;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic('');

    ####################### PARTE 7 #######################
    $parte = 7;

    $form->colspan = 12;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic('Como está a bateria e o carregamento do iphone');

    $form->colspan = 6;
    $form->div_class = 'parte_'.$parte;
    $form->max = 10;
    $form->input_group_addon_back = '%';
    $form->set_arg('text', 'Saúde da bateria', '00,00', real($saude_da_bateria, 'real'), 'Valor2');

    $form->colspan = 6;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg('select', 'O iphone está carregando normalmente', $opcoes, $o_iphone_esta_carregando_normalmente);

    $form->colspan = 6;
    $form->div_class = 'parte_'.$parte;
    $form->max = 10;
    $form->input_group_addon_back = 'A';
    $form->set_arg('text', 'Qual a amperagem da bateria', '00,00', real($qual_a_amperagem_da_bateria, 'real'), 'Valor2');

    $form->colspan = 6;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg('select', 'Carregamento por indução funciona', $opcoes, $carregamento_por_inducao_funciona);

    $form->colspan = 12;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic('');

    ####################### PARTE 8 #######################
    $parte = 8;
    $form->max = 10;
    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg('text', 'Data de abertura', 'dd/mm/AAAA', $data_de_abertura, 'data');
    $form->max = 10;
    $form->colspan = 4;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg('text', 'Data de finalização', 'dd/mm/AAAA', $data_de_finalizacao, 'data');

    $criterio = new TCriteria();
    $criterio->add(new TFilter('status', '=', 1));

    $sql = new TSqlSelect();
    $sql->setEntity('ordem_de_servico_status');
    $sql->addColumn('nome as statusNome');
    $sql->setCriteria($criterio);

    $result = $conn->query($sql->getInstruction());

    for($i=0; $i<$result->rowCount(); $i++){
        extract($result->fetch(PDO::FETCH_ASSOC));
        $statusNomes[] = array($i+1, $statusNome);
    }

    $form->colspan = 4;
    $form->tooltip = 'Selecione o status da ordem de serviço.';
    $form->div_class = 'parte_'.$parte;
    $form->set_js(null, 'onchange="verifica_status(this.value);"');
    $form->set_arg('select', 'Status', $statusNomes, $status);

    $form->colspan = 4;
    $var = 'Ordem de serviço prioritária';
    $form->tooltip = 'Se esta ordem de serviço for prioridade, marque esta opção e ela irá ficar em destaque no mapa de produção.';
    !isset($ordem_de_servico_prioritaria) ? $ordem_de_servico_prioritaria = false : false;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg('checkbox_array', $var, $ordem_de_servico_prioritaria, $var, $var);

    $form->colspan = 4;
    if(isset($error['imei_ou_n_de_serie']))
        $form->error = true;
    $form->max = 50;
    $form->div_class = 'parte_'.$parte;
    $form->set_js(null, 'onchange="filtro(\'parte_'.$parte.'\');"');
    $form->set_arg('text', 'IMEI ou N de Serie', '000000', $imei_ou_n_de_serie);

    $form->colspan = 4;
    $form->max = 50;
    $form->div_class = 'parte_'.$parte;
    $form->ajax_valores = array_values($tecnicos);
    $form->set_arg('ajaxtext', 'Técnico Responsavel', 'Nome do técnico', $tecnico_responsavel);

    $form->colspan = 12;
    $form->div_class = 'parte_'.$parte;
    $form->set_arg_generic('');






    $form->colspan = 12;
    $form->div_class = 'parte';
    $form->set_arg('hidden', 'fotos_qtd', null, 1);
    $form->colspan = 12;
    $form->div_class = 'parte';
    $cod = "
    function show_hide_foto(op){

        var fotos_qtd = parseInt($('input[name=\"fotos_qtd\"]').val());

        if(op == 'mais'){
            if(fotos_qtd >= ".MAX_ARQUIVO_ORDEM_SERVICO."){
                alert('Quantidade de arquivos maximo atingida.');
            }else{
                fotos_qtd += 1;
            }
        }else if(op == 'menos'){
            if(fotos_qtd <= 1){
                alert('Quantidade de arquivos mimima atingida.');
            }else{
                fotos_qtd -= 1;
            }
        }

        for(var i = 1; i <= ".MAX_ARQUIVO_ORDEM_SERVICO." ; i++ ){
            if(i <= fotos_qtd){
                
                $('#foto_' + i).show();
                

            }else if(i > fotos_qtd){
                
                $('#foto_' + i).hide();

            }
        }

        $('input[name=\"fotos_qtd\"]').val(fotos_qtd);
    }
    ";
    $form->set_js($cod, null);
    $botoes = '<span onclick="show_hide_foto(\'mais\');" class="fa fa-plus-circle" style="cursor:pointer;" data-toggle="tooltip" title="Adicionar arquivo"></span> Fotos do Celular <span onclick="show_hide_foto(\'menos\');" class="fa fa-minus-circle" style="cursor:pointer;" data-toggle="tooltip" title="Remover arquivo"></span>';
    $form->set_arg('h3', $botoes);

    $botoes = $codpopup = null;
    if($idOS){

        $criterio = new TCriteria();
        $criterio->add(new TFilter('ordem_de_servico_id', '=', $idOS));
        $criterio->add(new TFilter('tipo', '=', 'fotos_do_celular'));

        $sql = new TSqlSelect();
        $sql->setEntity('ordem_de_servico_atributos');
        $sql->addColumn('id as id_arquivos');
        $sql->addColumn('valor as arquivos_cliente');
        $sql->setCriteria($criterio);
        $result = $conn->query($sql->getInstruction());

        for($i=0; $i<$result->rowCount(); $i++){
            extract($result->fetch(PDO::FETCH_ASSOC));
            
            $popbotao = new popup(null, 'amarelo');
            $popbotao->texto = '
                <center>
                    Deseja mesmo deletar esta imagem?<br>
                    <img style="max-height: 250px;" src="'.$arquivos_cliente.'">
                </center>
                <button type="button" onclick="deletarimg('.$id_arquivos.');" class="btn btn-danger pull-left">Deletar</button>
                <button type="button" onclick="'.$popbotao->get_action_hide().'" class="btn btn-info pull-right">Cancelar</button>
                ';

            $popbotao->display = 'none';

            $codpopup .= $popbotao->get_pop();

            //$botoes.= $arquivos_cliente;
            $botoes .= '
            <div id="'.$id_arquivos.'" class="arquivos">
                <span class="fa fa-file-image-o" onclick="window.open(\''.$arquivos_cliente.'\',\'GoogleWindow\', \'width=400, height=400\');"></span><br>
                <span class="fa fa-search" onclick="window.open(\''.$arquivos_cliente.'\',\'GoogleWindow\', \'width=400, height=400\');"></span>
                <span class="fa fa-times" onclick="'.$popbotao->get_action_show().'"></span>
            </div>';
                

        }
        $form->colspan = 12;
        $form->div_class = 'parte';
        $form->set_arg('h3', $botoes);

    }


    for($i = 1; $i <= MAX_ARQUIVO_ORDEM_SERVICO; $i++){

        $var_end                     = 'end_'.$i;
        
        $form->colspan = 4;
        $popup_file = new popup('Tamanho do arquivo muito grande!<br>Tamanho maximo 3mb.', 'vermelho');
        $popup_file->display = 'none';
        //tamanho maximo 3 mb
        $tamanhoMax = "3000000";
        $cod_js = 
        "<script type='text/javascript'>
            //função para verificar se o arquivo é maior que 3MB antes de o usuário enviar o arquivo
            function checarTamanhoFoto_".$i."(arq){
                //this.files[0].size gets the size of your file.
                if(arq.files[0].size >= ".$tamanhoMax."){
                    ".$popup_file->get_action_show()."
                    $(arq).attr('value', '');
                }
            }   
        </script>";
        $cod_js .= $popup_file->get_pop();
        $form->set_js($cod_js, 'onchange="checarTamanhoFoto_'.$i.'(this);"', false);
        !isset($$var_end) ? $$var_end  = null: false;
        $form->div_id = 'foto_'.$i;
        $form->div_class = 'parte';
        $form->set_arg('file', 'Fotos do celular '.$i, 'São suportados arquivos de até 3mb.', $$var_end);

        
    }

    $form->colspan = 12;
    $form->max = 50;
    $form->div_class = 'parte';
    $form->set_arg('longtext', 'Observações', null, $observacoes);



    if(!isset($assinatura)){
        $form->colspan = "12' style='height: 74px;";
        $form->set_arg_generic('<label></label><span class="btn btn-primary btn-block btn-flat"  onclick="window.open(\'inc/ordem_de_servico.inc.php?id='.base64_encode($idOS).'\',\'GoogleWindow\', \'width=650, height=500\');">Inserir assinatura</span>');
    }else{
        $form->colspan = 12;
        $form->set_arg_generic('<label>Assinatura do Cliente</label><br><img src="'.$assinatura.'">');
        $form->colspan = "12' style='height: 74px;";
        $form->set_arg_generic('<label></label><span class="btn btn-primary btn-block btn-flat"  onclick="window.open(\'inc/ordem_de_servico.inc.php?id='.base64_encode($idOS).'\',\'GoogleWindow\', \'width=650, height=500\');">Atualizar assinatura</span>');
    }

    

    $form->button_ativar_deletar = false;



}





$pop_filtro = new popup('<span id="filtro_alerta"></span>', 'vermelho');
$pop_filtro->display = 'none';
$popup2 = $pop_filtro->get_pop();

if($op=='novo'){
    $form->button_ativar_deletar = false;
    $form->button_editar = false;
}

$html = new template;
$cod = $html->set_box('Formulário de Ordem de Serviço', $form->get_form().$popup.$popup2);
$html->set_html($cod.$codpopup);
$html->get_html('Cadastrar Ordem de Serviço', 'Cadastre uma venda ao preencher esse formulário');

echo $form->mascara;



?>


<script type="text/javascript">

    $(function(){

        //se operação = a novo então fazer o passo a passo
        var op = $('input[name="op"]').val();
        if(op=='novo'){
        
            for(var i = 2; i<9; i++){
                $('.parte_'+ i ).hide();
                $('.parte_'+i+'_b').hide();
            }
        
            $('.parte').hide();

        }

        $('input[name="data_de_abertura"]').attr('style', 'cursor: no-drop; pointer-events: none; border: none;');

        var fotos_qtd = parseInt($('input[name="fotos_qtd"]').val());
        for(i = 1; i <= <?php echo MAX_ARQUIVO_ORDEM_SERVICO; ?>; i++ ){
            
            if(op!='novo'){
                if(i <= fotos_qtd){
                    $('#foto_' + i).show();
                }else if(i > fotos_qtd){
                    $('#foto_' + i).hide();
                }
            }
            
        }
        
        filtro('parte_1');
        filtro('parte_2');
        filtro('parte_3');
        filtro('parte_4');
        filtro('parte_5');
        filtro('parte_6');
        filtro('parte_7');
        filtro('parte_8');
    }); 


    function filtro(op){
        
        if(op=='parte_1'){

            var nome = $('input[name="nome"]').val();
            var wpp = $('input[name="wpp"]').val();
            var cpf = $('input[name="cpf"]').val();

            if(nome=='' || wpp=='' || cpf==''){
                $('#parte_1_pro').attr('class', 'btn btn-default pull-right');
                $('#parte_1_pro').attr('onclick', 'alert(\'Você precisa preencher os campos nome, wpp e cpf!\')');
            }else{
                $('#parte_1_pro').attr('class', 'btn btn-primary pull-right');
                $('#parte_1_pro').attr('onclick', 'proximo(\'2\')');
            }
        
        }else if(op=='parte_2'){
            var iphone_liga = $('select[name="iphone_liga"]').val();
            
            if(iphone_liga == '1'){ //iphone liga continue testando
                $('#parte_2_pro').attr('class', 'btn btn-primary pull-right');
                $('#parte_2_pro').attr('onclick', 'proximo(\'3\')'); // va para segunda etapa
            }else if(iphone_liga == '0'){ // iphone n liga
                $('#parte_2_pro').attr('class', 'btn btn-primary pull-right');
                $('#parte_2_pro').attr('onclick', 'proximo(\'8\')'); // va para ultima etapa
            }else{
                $('#parte_2_pro').attr('class', 'btn btn-default pull-right');
                $('#parte_2_pro').attr('onclick', '');
            }
        }else if(op=='parte_3'){
            var os_botoes_fisicos_e_funcoes_id_funcionam = $('select[name="os_botoes_fisicos_e_funcoes_id_funcionam"]').val();
            
            if(os_botoes_fisicos_e_funcoes_id_funcionam == '1'){ //n tem nenhum problema com os botoes então ir para proxima etapa
                
                $('.parte_3_b').hide();

                $('#parte_3_pro').attr('class', 'btn btn-primary pull-right');
                $('#parte_3_pro').attr('onclick', 'proximo(\'4\')');
            }else if(os_botoes_fisicos_e_funcoes_id_funcionam == '0'){ // tem problema com os botões então aparecer a parte b

                $('.parte_3_b').show();
                
                $('#parte_3_pro').attr('class', 'btn btn-primary pull-right');
                $('#parte_3_pro').attr('onclick', 'proximo(\'4\')'); // va para ultima etapa
            }else{
                $('#parte_3_pro').attr('class', 'btn btn-default pull-right');
                $('#parte_3_pro').attr('onclick', '');
            }
        }else if(op=='parte_4'){
            var rede = $('select[name="os_sinais_de_rede_wifi_bluetooth_funcionam"]').val();
            
            if(rede == '1'){ //n tem nenhum problema com os botoes então ir para proxima etapa
                
                $('.parte_4_b').hide();

                $('#parte_4_pro').attr('class', 'btn btn-primary pull-right');
                $('#parte_4_pro').attr('onclick', 'proximo(\'5\')');
            }else if(rede == '0'){ // tem problema com os botões então aparecer a parte b

                $('.parte_4_b').show();
                
                $('#parte_4_pro').attr('class', 'btn btn-primary pull-right');
                $('#parte_4_pro').attr('onclick', 'proximo(\'5\')'); // va para ultima etapa
            }else{
                $('#parte_4_pro').attr('class', 'btn btn-default pull-right');
                $('#parte_4_pro').attr('onclick', '');
            }
        }else if(op=='parte_5'){
            var camera = $('select[name="as_cameras_altofalantes_e_microfones_funcionam"]').val();
            
            if(camera == '1'){ //n tem nenhum problema com os botoes então ir para proxima etapa
                
                $('.parte_5_b').hide();

                $('#parte_5_pro').attr('class', 'btn btn-primary pull-right');
                $('#parte_5_pro').attr('onclick', 'proximo(\'6\')');
            }else if(camera == '0'){ // tem problema com os botões então aparecer a parte b

                $('.parte_5_b').show();
                
                $('#parte_5_pro').attr('class', 'btn btn-primary pull-right');
                $('#parte_5_pro').attr('onclick', 'proximo(\'6\')'); // va para ultima etapa
            }else{
                $('#parte_5_pro').attr('class', 'btn btn-default pull-right');
                $('#parte_5_pro').attr('onclick', '');
            }
        }else if(op=='parte_8'){
            var imei = $('input[name="imei_ou_n_de_serie"]').val();
            
            if(imei == ''){ //se não tiver imei então cancelar salvamento
                $('#parte_8_pro').attr('class', 'btn btn-default pull-right');
                $('#parte_8_pro').attr('onclick', 'alert(\'Você precisa preencher o campo IMEI ou N de Série!\')');
            }else{
                $('#parte_8_pro').attr('class', 'btn btn-success pull-right');
                $('#parte_8_pro').attr('onclick', 'salvar()');
            }
        }


    }

    function proximo(op, verifica = null){
        var delay = 500;
        
        var iphone_liga = $('select[name="iphone_liga"]').val();
        if(iphone_liga == 0 && verifica){
            op = 2;
        }
        
        for(var i = 1; i<=8; i++){

            if(op==i){
                $('.parte_'+i).show(delay);
                //$('.parte_'+i+'_b').show(delay);
                $('#parte_' + i + '_pro').show(delay);
                $('#parte_' + i + '_ant').show(delay);
                

                if(i==1){
                    $('#bar-info').html('Inserindo dados do cliente');
                }else if(i==2){
                    $('#bar-info').html('Check List');
                }
                $('#bar-pro').attr('style', 'width: ' + (i * 12.5) + '%;');

            }else{
                $('.parte_'+i).hide(delay);
                $('.parte_'+i+'_b').hide(delay);
                $('#parte_' + i + '_pro').hide(delay);
                $('#parte_' + i + '_ant').hide(delay);
            }
        }
    }

    function salvar(){

        $('form[name="cadastrar_ordem_de_servico"]').submit();

    }


    function deletarimg(img){
        $.post('inc/ajax_ordem_de_servico.inc.php', {
            op: 'deletarimg',
            id: img
        }).done(function(data){
            if(data){
                console.log(data);
                $('#'+img).hide(500);
            }
        });
    }
    
    function verifica_status(valor){
        
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = today.getFullYear();

        today = dd + '/' + mm + '/' + yyyy;
        if(valor == 5 || valor == 1){ // concluida
            $('input[name="data_de_finalizacao"]').val(today);
        }else{
            $('input[name="data_de_finalizacao"]').val('');
        }
        
    }
   
</script>