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

	$foto = $nome = $email = $telefone = $data_de_nascimento = $funcao = null;
    $popup = null;
    $op = 'novo';
    $id = 0;

}elseif($op=='novo' or $op=='editar'){
	$cont = count($_POST);
	$array = array_keys($_POST);
    $valida = true;
    if(isset($id)){
        if(!is_numeric($id))
            $id = base64_decode($id);
    }

    $erro = 'Usuário não cadastrado. <b>Motivo:</b><br>';

    if($nome==''){
        $valida = false;
        $erro.= 'Nome do usuário inválido.<br>';   
        $error['nome'] = true;
    }

    if($email==''){
        $valida = false;
        $erro.= 'E-mail do usuário inválido.<br>';   
        $error['email'] = true;
    }

    if($telefone==''){
        $valida = false;
        $erro.= 'Telefone do usuário inválido.<br>';   
        $error['telefone'] = true;
    }

    if($funcao=='0'){
        if($op=='editar'){
            $funcao = registro($id, 'usuario', 'id_funcao');
        }else{
            $valida = false;
            $erro.= 'Função do usuário inválido.<br>';   
            $error['funcao'] = true;
        }
    }

    //filtro dos arquivos 3mb
    $tamanhoMax = "3000000";
    $tamanhoMaxAbr = "3mb";
    if(isset($_FILES['foto'])){
        if ($_FILES["foto"]["size"] > $tamanhoMax) {
            $erro .= "A imagem que você selecionou como foto é maior que o permitido para salvamento (tamanho máximo permitido $tamanhoMaxAbr).<br>";
            $valida = false;
            $error['foto'] = true;
        }
    }
    



	if($valida){
    //if(false){

		if($op=='novo'){

            $senha = rand(9999,99999);

            $sql = new TSqlInsert();
            $sql->setEntity('usuario');
            for($i=$check=0; $i<$cont; $i++){
                if($array[$i]!='op'){
                    if($array[$i]=='data_de_nascimento'){
                        $sql->setRowData($array[$i], formataDataInv($_POST[$array[$i]], 'false'));
                    }elseif($array[$i] == 'funcao'){
                        $sql->setRowData('id_funcao', $_POST[$array[$i]]);
                    }elseif($array[$i] != 'funcoes_do_sistema'){
                        $sql->setRowData($array[$i], $_POST[$array[$i]]);
                    }
                }
            }
            $sql->setRowData('senha', md5($senha));
            $sql->setRowData('status', 1);
            $result = $conn->query($sql->getInstruction());


            if($result->rowCount()){
                $id = $idUsuario = $conn->lastInsertId();

                
                if(isset($_FILES['foto'])){
                    //movendo arquivo da logomarca
                    $pasta = END_USUARIO;
                    $nome_imagem = $_FILES['foto']['name'];
                    $ext = strtolower(strrchr($nome_imagem, "."));
                    $nome_atual = strtr(strtolower($id.'-'.$nome), unserialize(CHAR_MAP)) . $ext;
                    $tmp = $_FILES['foto']['tmp_name'];
                    if (!is_dir($pasta)) {
                        mkdir($pasta, 0755, true);
                        chmod($pasta, 0755);
                    }
                    
                    if(move_uploaded_file($tmp, $pasta . $nome_atual)){
                        
                        $criterio = new TCriteria;
                        $criterio->add(new TFilter('id', '=', $id));

                        $sql = new TSqlUpdate();
                        $sql->setEntity('usuario');
                        $sql->setRowData('foto', $pasta . $nome_atual);
                        $sql->setCriteria($criterio);
                        $result = $conn->query($sql->getInstruction());
                        $foto = $pasta.$nome_atual;

                        $pop = new popup('Usuario cadastrada com sucesso!', 'verde');
                        $popup = $pop->get_pop();

                    }else{
                        
                        $pop = new popup('Usuário cadastrada com sucesso!<br>Entretando não foi possível salvar a foto.<br>Tente novamente mais tarde.', 'amarelo');
                        $popup = $pop->get_pop();

                    }

                }else{
                    
                    $pop = new popup('Usuário cadastrada com sucesso!', 'verde');
                    $popup = $pop->get_pop();
                }


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
                $mail->Subject  = "Contato ".NOME_EMPRESA; // Assunto da mensagem
                $mail->Body = "<h1>Olá $nome</h1>";
                $mail->Body .= "Sua credenciais em nosso sistema são;<br>";
                $mail->Body.= "Login: <strong>$email</strong><br>";
                $mail->Body.= "Senha: <strong>$senha</strong><br>";
                $mail->Body .= "<br>Acesse seu painel de controle através de ".END_SITE."<br><br><br>";
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
                    "Usuário $nome cadastrado com sucesso.<br>
                    Por favor certifica o e-mail (<strong>$email</strong>) para verificar a senha. 
                    O mesmo pode haver caído na caixa de <span style='color:red;'>span ou lixeira</span>.");
                    $popup = $pop->get_pop();

                    $op = 'editar';
                    $id = $idUsuario;

                }else{
                  
                    $pop = new popup(
                    "Não foi possível enviar o email para $email.<br>
                    Motivo: " . $mail->ErrorInfo, 'amarelo');
                    $popup = $pop->get_pop();
                
                }
                

            }
           
			

		}elseif($op=='editar'){


            $criterio = new TCriteria;
            $criterio->add(new TFilter('id', '=', $id));

            $sql = new TSqlUpdate();
            $sql->setEntity('usuario');
            for($i=$check=0; $i<$cont; $i++){
                if($array[$i]!='op' and $array[$i]!='id'){
                    if($array[$i]=='data_de_nascimento'){
                        $sql->setRowData($array[$i], formataDataInv($_POST[$array[$i]], 'false'));
                    }elseif($array[$i] == 'funcao'){
                        $sql->setRowData('id_funcao', $_POST[$array[$i]]);
                    }elseif($array[$i] != 'funcoes_do_sistema'){
                        $sql->setRowData($array[$i], $_POST[$array[$i]]);
                    }
                }
            }
            $sql->setCriteria($criterio);
            $result = $conn->query($sql->getInstruction());

 
            $idUsuario = $id;

            
            if(isset($_FILES['foto'])){
                //movendo arquivo da logomarca
                $pasta = END_USUARIO;
                $nome_imagem = $_FILES['foto']['name'];
                $ext = strtolower(strrchr($nome_imagem, "."));
                $nome_atual = strtr(strtolower($id.'-'.$nome), unserialize(CHAR_MAP)) . $ext;
                $tmp = $_FILES['foto']['tmp_name'];
                if (!is_dir($pasta)) {
                    mkdir($pasta, 0755, true);
                    chmod($pasta, 0755);
                }
                
                if(move_uploaded_file($tmp, $pasta . $nome_atual)){
                    
                    $criterio = new TCriteria;
                    $criterio->add(new TFilter('id', '=', $id));

                    $sql = new TSqlUpdate();
                    $sql->setEntity('usuario');
                    $sql->setRowData('foto', $pasta . $nome_atual);
                    $sql->setCriteria($criterio);
                    $result = $conn->query($sql->getInstruction());
                    $foto = $pasta.$nome_atual;

                    $pop = new popup('Usuario editado com sucesso!', 'verde');
                    $popup = $pop->get_pop();

                }else{
                    
                    $pop = new popup('Usuário editado com sucesso!<br>Entretando não foi possível salvar a foto.<br>Tente novamente mais tarde.', 'amarelo');
                    $popup = $pop->get_pop();

                }

            }else{
                
                $pop = new popup('Usuário editado com sucesso!', 'verde');
                $popup = $pop->get_pop();
            }                
 
        

        }

        /*INSERIR FUNÇÕES PARA O USUARIO*/
        if($op=='editar'){
            $criterio = new TCriteria();
            $criterio->add(new TFilter('id_usuario', '=', $id));

            $sql = new TSqlDelete();
            $sql->setEntity('usuario_credenciais');
            $sql->setCriteria($criterio);

            $result = $conn->query($sql->getInstruction());
        }
        if(isset($funcoes_do_sistema)){        
            for($i=0; $i<count($funcoes_do_sistema); $i++){

                $sql = new TSqlInsert();
                $sql->setEntity('usuario_credenciais');
                $sql->setRowData('id_usuario', $id);
                $sql->setRowData('id_sistema_ferramentas', $funcoes_do_sistema[$i]);

                $result = $conn->query($sql->getInstruction());
            }
        }



    }else{
        $pop = new popup($erro, 'vermelho');
        $popup = $pop->get_pop();
        if($op=='novo')
           $id = 0;
    }
    $foto = registro($id, 'usuario', 'foto');


}elseif($op=='deletar'){

    $foto = $nome = $email = $telefone = $data_de_nascimento = $funcao = null;

    

    if(!is_numeric($id))
        $id = base64_decode($id);
    
    $criterio = new TCriteria;
    $criterio->add(new TFilter('id', '=', $id));
    
    $sql = new TSqlUpdate;
    $sql->setEntity('usuario');
    $sql->setRowData('status', '0');
    $sql->setCriteria($criterio);
    $result = $conn->query($sql->getInstruction());

    if($result->rowCount()){
        $pop = new popup("Usuário deletado com sucesso.<br>");
        $popup = $pop->get_pop();
    }else{
        $pop = new popup("Usuário não pode ser deletado.<br>", 'vermelho');
        $popup = $pop->get_pop();
    }

}elseif($op=='ativar'){


    if(!is_numeric($id))
        $id = base64_decode($id);
    
    $criterio = new TCriteria;
    $criterio->add(new TFilter('id', '=', $id));
    
    $sql = new TSqlUpdate;
    $sql->setEntity('usuario');
    $sql->setRowData('status', 1);
    $sql->setCriteria($criterio);
    $result = $conn->query($sql->getInstruction());

    if($result->rowCount()){
        $pop = new popup("Usuário ativado com sucesso.<br>");
        $popup = $pop->get_pop();
    }else{
        $pop = new popup("Usuário não pode ser ativado.<br>", 'vermelho');
        $popup = $pop->get_pop();
    }


    $sql = new TSqlSelect;
    $sql->setEntity('usuario');
    $sql->addColumn('*');
    $sql->setCriteria($criterio);

    $result = $conn->query($sql->getInstruction());

    if($result->rowCount()){
        extract($result->fetch(PDO::FETCH_ASSOC));
    }

    $funcao = $id_funcao;
    $op = 'editar';

}elseif($op=='visualizar'){
    
    if(!is_numeric($id))
        $id = base64_decode($id);

    $criterio = new TCriteria;
    $criterio->add(new TFilter('id', '=', $id));

    $sql = new TSqlSelect;
    $sql->setEntity('usuario');
    $sql->addColumn('*');
    $sql->setCriteria($criterio);
    $result = $conn->query($sql->getInstruction());

    if($result->rowCount()){
        extract($result->fetch(PDO::FETCH_ASSOC));
    }
    
    $funcao = $id_funcao;
    $data_de_nascimento = formataData($data_de_nascimento);

    $popup = null;
    $op = 'editar';
}



$form = new form('Cadastrar Usuario', 'cadastrar-usuario.php', $op);
$form->id = $id;
$form->tabela = 'usuario';

$form->colspan = 12;
if(isset($error['foto']))
    $form->error = true;
$form->set_arg('file', 'Foto', 'Sugestão imagem de 200px x 200px', $foto);

$form->colspan = 6;
if(isset($error['nome']))
    $form->error = true;
$form->set_arg('text', 'Nome', 'Nome completo do usuário.', $nome);
$form->colspan = 6;
if(isset($error['email']))
    $form->error = true;
$form->set_arg('email', 'E-mail', 'usuario@dominio.com.br', $email);


$form->colspan = 4;
if(isset($error['telefone']))
    $form->error = true;
$form->set_arg('text', 'Telefone', '(88) 9-9999.9999', $telefone, 'tel');

$form->colspan = 4;
if(isset($error['data_de_nascimento']))
    $form->error = true;
$form->set_arg('text', 'Data de nascimento', 'dd/mm/AAAA', $data_de_nascimento, 'data');
$form->colspan = 4;
if(isset($error['funcao']))
    $form->error = true;
$criterio = new TCriteria();
$criterio->add(new TFilter('status', '=', 1));
$criterio->add(new TFilter('id', '<>', 1));
$sql = new TSqlSelect();
$sql->setEntity('usuario_funcao');
$sql->addColumn('id');
$sql->addColumn('nome');
$sql->setCriteria($criterio);
$result = $conn->query($sql->getInstruction());
$funcoes[0][] = 0;
$funcoes[0][] = '--';
for($i=1; $i<=$result->rowCount(); $i++){
    extract($result->fetch(PDO::FETCH_ASSOC));
    $funcoes[$i][] = $id;
    $funcoes[$i][] = $nome;
}

$user = new usuario();
if($user->get_usuario('id_funcao')==1){
    $form->set_arg('select', 'Função', $funcoes, $funcao);
}else{
    $form->set_arg('texthidden', 'Função', null, $funcao);
}




##CONTROLE DE FERRAMENTA DE USUARIO##

$form->colspan = 12;
$form->set_arg('h3', 'Credenciais deste usuário');

$array_pesquisa = array('Comercial', 'Gestão', 'Finanças', 'Relatório', 'Sistema');

for($j=0; $j<count($array_pesquisa); $j++){


    $criterio = new TCriteria();
    $criterio->add(new TFilter('hierarquia', '=', $array_pesquisa[$j]));
    $criterio->setProperty('order by', 'sequencia');

    $sql = new TSqlSelect();
    $sql->setEntity('sistema_ferramentas');
    $sql->addColumn('id as funcao_sistema_id');
    $sql->addColumn('agrupamento as funcao_sistema_agrupamento');
    $sql->addColumn('nome as funcao_sistema_nome');
    $sql->setCriteria($criterio);
    $result = $conn->query($sql->getInstruction());


    $form->colspan = 12;
    $form->set_arg('h3', $array_pesquisa[$j]);

    for($i=0; $i<$result->rowCount(); $i++){

        extract($result->fetch(PDO::FETCH_ASSOC));

        $sql_funcoes = new TSqlSelect();
        $sql_funcoes->setEntity('usuario_credenciais');
        $sql_funcoes->addColumn('id_sistema_ferramentas');
        if($op!='novo' and $op!='deletar'){

            $criterio_funcoes = new TCriteria();
            $criterio_funcoes->add(new TFilter('id_usuario', '=', $form->id));
            $criterio_funcoes->add(new TFilter('id_sistema_ferramentas', '=', $funcao_sistema_id));

            $sql_funcoes->setCriteria($criterio_funcoes);
            
        }

        $result_funcoes = $conn->query($sql_funcoes->getInstruction());

        if($op!='novo' and $op!='deletar'){
            if($result_funcoes->rowCount()){
                $funcao_usuario = $funcao_sistema_id;
            }else{
                $funcao_usuario = false;
            }
        }else{
            $funcao_usuario = false;
        }

        if(($i+1)==$result->rowCount()){
            
            $form->colspan = (( 4 - $i ) * 3 );
            if($form->colspan<0){
                $form->colspan *= -1;
            }
            
        }else{
            $form->colspan = 3;
        }    
        //tipo nome placeholder valor mascara
        $form->set_arg('checkbox_array', 'Funções do sistema[]', $funcao_usuario, $funcao_sistema_id, $funcao_sistema_nome.' '.$funcao_sistema_agrupamento);

    }

    



}



$html = new template;
$cod = $html->set_box('Formulário de cadastramento de usuário', $form->get_form().$popup);
$html->set_html($cod);
$html->get_html('Cadastrar Usuário', 'Cadastre novo usuário ao preencher esse formulário');

echo $form->mascara;

?>