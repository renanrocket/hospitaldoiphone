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
	
	$foto = $nome = $cpf = $email = $wpp = $telefone  = $data_de_nascimento = 
    $endereco = $numero = $bairro = $cidade = $estado = $cep = $status = $data_cadastro = $instagram = null;
    $estado = 6;
    $cidade = 1722;
    $popup = null;
    $op = 'novo';
    $idCliente = null;

}elseif($op=='novo' or $op=='editar'){
	
	$cont = count($_POST);
	$array = array_keys($_POST);
    $valida = true;
    


    $erro = 'Cliente não cadastrado. <b>Motivo:</b><br>';
    //filtros
    if($nome==''){
        $valida = false;
        $erro.= 'Nome do cliente inválido.<br>';
        $error['nome'] = true;
    }
    if($email==''){
        $valida = false;
        $erro.= 'Campo e-mail inválido.<br>';
        $error['email'] = true;
    }
    if($data_de_nascimento==''){
        $valida = false;
        $erro.= 'Campo data de nascimento não preenchido.<br>';
        $error['data_de_nascimento'] = true;
    }
    if($cpf!=''){
        if(!validaCPF($cpf)){
            $valida = false;
            $erro.= 'CPF informado inválido.<br>';   
            $error['cpf'] = true;
        }

        if($op=='novo'){
            $criterio = new TCriteria;
            $criterio->add(new TFilter('cpf', '=', $cpf));
            if($op=='editar'){
                $criterio->add(new TFilter('id', '<>', $id));
            }
            $sql = new TSqlSelect();
            $sql->setEntity('cliente');
            $sql->addColumn('*');
            $sql->setCriteria($criterio);
            $result = $conn->query($sql->getInstruction());
            if($result->rowCount()){
                $valida = false;
                $erro.= 'CPF informado já está cadastrado.<br>';
                $error['cpf'] = true;
            }
        }
    }

    if($op=='novo'){
        $criterio = new TCriteria;
        $criterio->add(new TFilter('email', '=', $email));
        if($op=='editar'){
            $criterio->add(new TFilter('id', '<>', $id));
        }

        $sql = new TSqlSelect();
        $sql->setEntity('cliente');
        $sql->addColumn('*');
        $sql->setCriteria($criterio);
        $result = $conn->query($sql->getInstruction());
        if($result->rowCount()){
            $valida = false;
            $erro.= 'Email informado já está cadastrado.<br>'; 
            $error['email'] = true;
        }
    }



    if($valida){

        if($op=='novo'){

            $sql = new TSqlInsert;
            $sql->setEntity('cliente');
            for($i=$check=0; $i<$cont; $i++){
                if($array[$i]!='op'){
                    if($array[$i]=='data_de_nascimento'){
                        $sql->setRowData($array[$i], formataDataInv($_POST[$array[$i]], 'false'));
                    }else{
                        $sql->setRowData($array[$i], $_POST[$array[$i]]);
                    }
                }
            }
            $usu = new usuario;
            $sql->setRowData('id_usuario', $usu->get_usuario('id'));
            $sql->setRowData('data_cadastro', date('Y-m-d'));
            $sql->setRowData('status', 1); //status ativo          
            $result = $conn->query($sql->getInstruction());
            if($result->rowCount()){
                $idCliente = $id_cliente = $conn->lastInsertId();

                $pop = new popup(
                "Cliente $nome cadastrado com sucesso.", 'verde');
                $popup = $pop->get_pop();

                //upload da foto
                if(isset($_FILES['foto']['name'])){
                    //tratando a imagem
                    $pasta = END_CLIENTE.$idCliente."/";
                    if(!file_exists($pasta)){
                        mkdir($pasta, 755, true);
                    }
                    /* formatos de imagem permitidos */
                    $permitidos = array(".jpg", ".jpeg", ".gif", ".png", ".bmp");
                    $nome_imagem = $_FILES['foto']['name'];
                    $tamanho_imagem = $_FILES['foto']['size'];
                    
                    /* pega a extensão do arquivo */
                    $ext = strtolower(strrchr($nome_imagem, "."));
                    
                    /*  verifica se a extensão está entre as extensões permitidas */
                    if (in_array($ext, $permitidos)) {
                        
                        //nome atual do arquivo
                        $tmp = $_FILES['foto']['tmp_name'];
                        //futuro nome do arquivo
                        $nome_imagem = explode('.', $nome_imagem);
                        $nome_atual = strtr(strtolower($nome_imagem[0]), unserialize(CHAR_MAP)) . $ext;
                        
                
                        /* se enviar a foto, insere o nome da foto no banco de dados */
                        if (move_uploaded_file($tmp, $pasta . $nome_atual)) {

                            $criterio = new TCriteria;
                            $criterio->add(new TFilter('id', '=', $idCliente));

                            $sql = new TSqlUpdate;
                            $sql->setEntity('cliente');
                            $sql->setRowData('foto', $pasta . $nome_atual);
                            $sql->setCriteria($criterio);
                            $result = $conn->query($sql->getInstruction());

                            /*
                            $img = new Canvas();
                            $img->carrega( $pasta . $nome_atual )
                              ->hexa( '#FFFFFF' )
                              ->redimensiona( 200, 200, 'preenchimento' )
                              ->grava();
                            */

                            $foto = $pasta . $nome_atual;

                            /*
                            $instrucaoImg = "update empresa set imgsrc='" . $pasta . $nome_atual . "' ";
                            if($op=="editar"){
                                $instrucaoImg .= "where id='$id'";
                                if (file_exists(registro($id, "empresa", "imgsrc"))) {
                                    unlink(registro($id, "empresa", "imgsrc"));
                                }
                            }
                            */
                            
                        } else {
                            $erro.= 'Falha ao enviar a imagem.<br>'; 
                            $error['foto'] = true;
                        }
                    
                    } else {
                        $erro.= 'Somente são validos arquivos de imagem: ".jpg", ".jpeg", ".gif", ".png", ".bmp"'; 
                        $error['foto'] = true;
                    }   
                }

/*
                // Inicia a classe PHPMailer
                $mail = new PHPMailer();

                // Define os dados do servidor e tipo de conexão
                // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
                $mail->IsSMTP(); // Define que a mensagem será SMTP
                $mail->Host = HOSTMAIL; // Endereço do servidor SMTP
                $mail->SMTPAuth = true; // Usa autenticação SMTP? (opcional)
                $mail->Username = EMAIL; // Usuário do servidor SMTP
                $mail->Password = SENHA; // Senha do servidor SMTP

                // Define o remetente
                // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
                $mail->From = EMAIL; // Seu e-mail
                $mail->FromName = FROM; // Seu nome

                // Define os destinatário(s)
                // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
                $mail->AddAddress($email, $nome);
                $mail->AddAddress(EMAIL, FROM);
                //$mail->AddCC('ciclano@site.net', 'Ciclano'); // Copia
                //$mail->AddBCC('falecom@rocketsolution.com.br', 'Atendimento Rocket Solution');

                // Define os dados técnicos da Mensagem
                // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
                $mail->IsHTML(true); // Define que o e-mail será enviado como HTML
                $mail->CharSet = 'utf-8'; // Charset da mensagem (opcional)

                // Define a mensagem (Texto e Assunto)
                // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
                $mail->Subject  = "Contato Multicores Formaturas"; // Assunto da mensagem
                $mail->Body = "<h1>Olá $nome</h1>";
                $mail->Body .= "Sua credenciais em nosso sistema são;<br>";
                $mail->Body.= "Login: <strong>$email</strong><br>";
                $mail->Body.= "Senha: <strong>$cpf</strong><br>";
                $mail->Body .= "<br>Acesse seu painel de controle através de http://".$_SERVER['HTTP_HOST']."<br><br><br>";
                $mail->Body .= "<a href='http://www.rocketsolution.com.br/'><img src='".IMGASS."'></a>";
                

                //$mail->AltBody = "Este é o corpo da mensagem de teste, em Texto Plano! \r\n :)";
                // Define os anexos (opcional)
                // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
                //$mail->AddAttachment("c:/temp/documento.pdf", "novo_nome.pdf");  // Insere um anexo

                // Envia o e-mail
                $enviado = $mail->Send();

                // Limpa os destinatários e os anexos
                $mail->ClearAllRecipients();
                $mail->ClearAttachments();
                if($enviado){
                    
                    $pop = new popup(
                    "Cliente $nome cadastrado com sucesso.<br>
                    Por favor certifica o e-mail (<strong>$email</strong>) para verificar a senha. 
                    O mesmo pode haver caído na caixa de <span style='color:red;'>span ou lixeira</span>.");
                    $popup = $pop->get_pop();

                    $op = 'editar';
                    $id = $idCliente;

                }else{
                  
                    $pop = new popup(
                    "Não foi possível enviar o email para $email.<br>
                    Motivo: " . $mail->ErrorInfo, 'amarelo');
                    $popup = $pop->get_pop();
                
                }
                  
                          */    

            }else{

                $pop = new popup(
                "Não foi possível cadastrar o cliente $nome.<br>
                Motivo: " . $mail->ErrorInfo, 'vermelho');
                $popup = $pop->get_pop();

            }
     
       
        }elseif($op=='editar'){

            

            $criterio = new TCriteria;
            $criterio->add(new TFilter('id', '=', $id));
            $idCliente = $id;
            
            $sql = new TSqlUpdate;
            $sql->setEntity('cliente');
            for($i=$check=0; $i<$cont; $i++){
                if($array[$i]!='op' and $array[$i]!='id'){
                    if($array[$i]=='data_de_nascimento'){
                        $sql->setRowData($array[$i], formataDataInv($_POST[$array[$i]], 'false'));
                    }else{
                        $sql->setRowData($array[$i], $_POST[$array[$i]]);
                    }
                }
            }
            
            $sql->setCriteria($criterio);

            $result = $conn->query($sql->getInstruction());
            
            if($result->rowCount()){
                 $pop = new popup("Cliente $nome editado com sucesso.<br>");
                    $popup = $pop->get_pop();

            }else{
                 $pop = new popup(
                    "Cliente $nome não pode ser editado.<br>
                    Entre em contato com o suporte para maiores informações.<br>", 'vermelho');
                $popup = $pop->get_pop();
            }

            //upload da foto
            if(isset($_FILES['foto']['name'])){
                //tratando a imagem
                $pasta = END_CLIENTE.$idCliente."/";
                if(!file_exists($pasta)){
                    mkdir($pasta, 755, true);
                }
                /* formatos de imagem permitidos */
                $permitidos = array(".jpg", ".jpeg", ".gif", ".png", ".bmp");
                $nome_imagem = $_FILES['foto']['name'];
                $tamanho_imagem = $_FILES['foto']['size'];
                
                /* pega a extensão do arquivo */
                $ext = strtolower(strrchr($nome_imagem, "."));
                
                /*  verifica se a extensão está entre as extensões permitidas */
                if (in_array($ext, $permitidos)) {
                    
                    //nome atual do arquivo
                    $tmp = $_FILES['foto']['tmp_name'];
                    //futuro nome do arquivo
                    $nome_imagem = explode('.', $nome_imagem);
                    $nome_atual = strtr(strtolower($nome_imagem[0]), unserialize(CHAR_MAP)) . $ext;
                    
            
                    /* se enviar a foto, insere o nome da foto no banco de dados */
                    if (move_uploaded_file($tmp, $pasta . $nome_atual)) {

                        $criterio = new TCriteria;
                        $criterio->add(new TFilter('id', '=', $idCliente));

                        $sql = new TSqlSelect;
                        $sql->setEntity('cliente');
                        $sql->addColumn('foto as nome_antigo');
                        $sql->setCriteria($criterio);
                        $result = $conn->query($sql->getInstruction());

                        if($result->rowCount()){
                            extract($result->fetch(PDO::FETCH_ASSOC));

                            if (file_exists($nome_antigo)) {
                                unlink($nome_antigo);
                            }
                        
                        }

                        $sql = new TSqlUpdate;
                        $sql->setEntity('cliente');
                        $sql->setRowData('foto', $pasta . $nome_atual);
                        $sql->setCriteria($criterio);
                        $result = $conn->query($sql->getInstruction());

                        /*
                        $img = new Canvas();
                        $img->carrega( $pasta . $nome_atual )
                          ->hexa( '#FFFFFF' )
                          ->redimensiona( 200, 200, 'preenchimento' )
                          ->grava();
                        */

                        $foto = $pasta . $nome_atual;

                        $pop = new popup("Cliente $nome editado com sucesso.<br>");
                        $popup = $pop->get_pop();

                        
                        
                    } else {
                        $erro.= 'Falha ao enviar a imagem.<br>'; 
                        $error['foto'] = true;
                    }
                
                } else {
                    $erro.= 'Somente são validos arquivos de imagem: ".jpg", ".jpeg", ".gif", ".png", ".bmp"'; 
                    $error['foto'] = true;
                }   
            }



        }



        $op='editar';


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
    
    $sql = new TSqlUpdate;
    $sql->setEntity('cliente');
    $sql->setRowData('status', '0');
    $sql->setCriteria($criterio);
    $result = $conn->query($sql->getInstruction());

    if($result->rowCount()){
        $pop = new popup("Cliente deletado com sucesso.<br>");
        $popup = $pop->get_pop();
    }else{
        $pop = new popup("Cliente não pode ser deletado.<br>", 'vermelho');
        $popup = $pop->get_pop();
    }

    $foto = $nome = $cpf = $email = $wpp = $telefone  = $data_de_nascimento = 
    $endereco = $numero = $bairro = $cidade = $estado = $cep = $status = $data_cadastro = $instagram = null;
    $estado = 6;
    $cidade = 1722;
    $op = 'novo';
    $id = 0;

}elseif($op=='ativar'){

    if(!is_numeric($id))
        $id = base64_decode($id);
    
    $criterio = new TCriteria;
    $criterio->add(new TFilter('id', '=', $id));
    $sql = new TSqlUpdate;
    $sql->setEntity('cliente');
    $sql->setRowData('status', 1);
    $sql->setCriteria($criterio);
    $result = $conn->query($sql->getInstruction());

    if($result->rowCount()){
        $pop = new popup("Cliente ativado com sucesso.<br>");
        $popup = $pop->get_pop();
    }else{
        $pop = new popup("Cliente não pode ser ativado.<br>", 'vermelho');
        $popup = $pop->get_pop();
    }

    

    $sql = new TSqlSelect;
    $sql->setEntity('cliente');
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
    $sql->setEntity('cliente');
    $sql->addColumn('*');
    $sql->setCriteria($criterio);
    $result = $conn->query($sql->getInstruction());

    if($result->rowCount()){
        extract($result->fetch(PDO::FETCH_ASSOC));
        $data_de_nascimento = formataData($data_de_nascimento);
        $popup = null;
        $idCliente = $id;
    }

    $op = 'editar';

}


$form = new form('Cadastrar Cliente', 'cadastrar-cliente.php', $op);
$form->id = $idCliente;
$form->tabela = 'cliente';

$form->colspan = 12;
$popup_file = new popup('Tamanho da foto muito grande!<br>Tamanho maximo 3mb.', 'vermelho');
$popup_file->display = 'none';
//tamanho maximo 3 mb
$tamanhoMax = "3000000";
$cod_js = 
"<script type='text/javascript'>
    //função para verificar se o arquivo é maior que 3MB antes de o usuário enviar o arquivo
    function checarTamanho(arq){
        //this.files[0].size gets the size of your file.
        if(arq.files[0].size >= ".$tamanhoMax."){
            ".$popup_file->get_action_show()."
            $(arq).attr('value', '');
        }
    }   
</script>";
$cod_js .= $popup_file->get_pop();
$form->set_js($cod_js, 'onchange="checarTamanho(this);"', false);
!isset($foto) ? $foto = null: false;
$form->set_arg('file', 'Foto', 'Sugestão imagem de 200px x 200px', $foto);

$form->colspan = 6;
$form->max = 100;
if(isset($error['nome']))
    $form->error = true;
$form->set_arg('text', 'Nome', 'Preencha com o nome complemto do Cliente', $nome);
$form->colspan = 3;
if(isset($error['cpf']))
    $form->error = true;
$form->set_arg('text', 'CPF', '000.000.000-00', $cpf, 'cpf');
$form->colspan = 3;
if(isset($error['data_de_nascimento']))
    $form->error = true;
$form->set_arg('text', 'Data de nascimento', 'dd/mm/AAAA', $data_de_nascimento, 'data');

$form->colspan = 6;
if(isset($error['email']))
    $form->error = true;
$form->set_arg('email', 'Email', 'email@dominio.com', $email);
$form->colspan = 6;
$form->set_arg('text', 'Instagram', '@perfil.do.cliente', $instagram, 'instagram');


$form->colspan = 6;
$form->set_arg('text', 'Wpp', '(88) 0000.00000', $wpp, 'tel');
$form->colspan = 6;
$form->set_arg('text', 'Telefone', '(88) 0000.00000', $telefone, 'tel');


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
