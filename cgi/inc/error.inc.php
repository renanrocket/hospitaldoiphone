<style type="text/css">
	.alert-body {
	    top: 0% !important;
	    left: 10% !important;
	    width: 85% !important;
	}
</style>
<?php

include_once 'functions.inc.php';

global $conn;
global $sql;

$motivo = $debug = null;
$php = "Versão do Php e Sistema operacional: " . PHP_VERSION . " " . PHP_OS . "<br>";
if (isset($errno)) {
	$motivo .= "Motivo php: <b>" . $errstr . "</b><br>";
	$debug .= "Debug php: <b>Linha " . $errline . " no Arquivo " . $errfile . "</b><br>";
}

$dbErro = $conn->errorInfo();

if ($dbErro[2]) {

	$motivo .= "Motivo Query: <b>" . $dbErro[2] . "</b><br>";
	if (null !== $sql->getInstruction()) {
		$debug .= "Debug Query: <b>" . $sql->getInstruction() . "</b><br>";
	}
}

if($conn->inTransaction()){
	$conn->rollback();//voltando toda operação q foi feita nessa transação e prevenir erros no banco de dados
	$conn = null;
}

$debug .= debugVariaveis()."<br>";

//if($_SERVER["HTTP_HOST"] != 'localhost'){
if(true){

	$args[] = array('email', '=', $_COOKIE['email']);
    $usuario = usuario_get($args);
	$nome = $usuario[0]['nome'];
	$telefone = $usuario[0]['telefone'];
	$email = $usuario[0]['email'];

	$corpo = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">";
	$corpo .= "<html>";
	$corpo .= "<head>";
	$corpo .= "<meta http-equiv=\"content-Type\" content=\"text/html; charset=UTF-8\" />";
	$corpo .= "<title>Delphos</title>";
	$corpo .= "</head>";
	$corpo .= "<body>";
	$corpo .= "<div style=\"";
	$corpo .= "font-size:18px;";
	$corpo .= "font-family: Arial, Helvetica, sans-serif;";
	$corpo .= "border: solid 2px #4F0E11;";
	$corpo .= "padding: 10px;";
	$corpo .= "border-radius: 10px;";
	$corpo .= "\" >";
	$corpo .= "<img src='" . $_SERVER["HTTP_HOST"] . "/img/boneco_duvida.png' style='float: right;'>";
	$corpo .= "<a href='http://www.rocketsolution.com.br'>";
	$corpo .= "<img src='" . $_SERVER["HTTP_HOST"] . "/img/logo.png' style='margin-right: 30px; margin-bottom: 20px;'><br>";
	$corpo .= "</a>";
	$corpo .= "<h3>Houston we have a problem!</h3><br>";
	$corpo .= "Identificamos um bug em um de nossos sistemas!<br>";
	$corpo .= "Empresa: <b style='color:red;'>$nome</b><br>";
	$corpo .= "Telefone: <b style='color:red;'>$telefone</b><br>";
	$corpo .= "E-mail: <b style='color:red;'>$email</b><br>";
	$corpo .= "Utilizador Nome: <b style='color:red;'>" . $nome . "</b><br>";
	$corpo .= "Utilizador telefone: <b style='color:red;'>" . $telefone. "</b><br>";
	$corpo .= "Utilizador e-email: <b style='color:red;'>" . $email . "</b><br>";
	$corpo .= "Hora da ocorrência: <b style='color:red;'>" . date("d/m/Y H:i:s") . "</b><br>";
	$corpo .= "Página em que foi encontrado o bug: <b style='color:red;'>" . $_SERVER["PHP_SELF"] . "</b><br>";
	$corpo .= $php;
	$corpo .= $motivo;
	$corpo .= $debug;
	$corpo .= "I.A. Delphos";
	$corpo .= "</div>";
	$corpo .= "</body>";
	$corpo .= "</html>";

	// Inicia a classe PHPMailer
    $mail = new PHPMailer();

    // Define os dados do servidor e tipo de conexão
    // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
    $mail->IsSMTP(); // Define que a mensagem será SMTP
    $mail->Host = HOSTMAIL_SUPORTE; // Endereço do servidor SMTP
    $mail->SMTPAuth = true; // Usa autenticação SMTP? (opcional)
    $mail->Username = EMAIL_SUPORTE; // Usuário do servidor SMTP
    $mail->Password = SENHA_SUPORTE; // Senha do servidor SMTP
    //$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port 	= PORT_SUPORTE;

    // Define o remetente
    // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
    $mail->From = EMAIL_SUPORTE; // Seu e-mail
    $mail->FromName = FROM_SUPORTE; // Seu nome

    // Define os destinatário(s)
    // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
    $mail->AddAddress($email, $nome);
    $mail->AddAddress(EMAIL_SUPORTE, FROM_SUPORTE);

    // Define os dados técnicos da Mensagem
    // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
    $mail->IsHTML(true); // Define que o e-mail será enviado como HTML
    $mail->CharSet = 'utf-8'; // Charset da mensagem (opcional)

    // Define a mensagem (Texto e Assunto)
    // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
    $mail->Subject  = "Contato ".NOME_EMPRESA; // Assunto da mensagem
    $mail->Body = $corpo;
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

	if ($enviado) {
		$internet = true;
	} else {
		$internet = false;		
	}
}else{
	$internet = false;
}


//se for um problema de banco de dados exibir mensagem na tela solicitando suporte
if ($internet) {
	$msg = "<h1>Aconteceu algo errado!</h1>";
	$pagina = explode("/", $_SERVER['PHP_SELF']);
	$include = "img/robo.png";
	$msg .= "<img src='$include' style='float:right; margin-left:1em;'>";
	$msg .= "Sentimos muito, mas não foi possivel completar essa operação.<br>";
	$msg .= "Um e-mail foi enviado para os programadores da <b style='color:white;'>Rocket Solution</b>, e em breve ";
	$msg .= "entraremos em contato com <b>$nome</b>, através dos telefones: <b>$telefone</b> ";
	if ($email) {
		$msg .= "ou atravês do e-mail: <b>$email</b>.";
	}

	$popup = new popup($msg, 'vermelho');
	echo $popup->get_pop();


	//die();
}else{

	$msg = "<h1>Aconteceu algo errado!</h1>";
	$pagina = explode("/", $_SERVER['PHP_SELF']);
	$include = "img/robo.png";
	$msg .= "<img src='$include' style='float:right; margin-left:1em;'>";
	$msg .= "Sentimos muito, mas não foi possivel completar essa operação.<br>";
	$msg .= "Como o seu sistema não está na internet, contate o nosso suporte e informe o seguinte problema:<br>";
	$msg .= $php . $motivo . $debug;
		
	$popup = new popup($msg, 'vermelho');
	echo $popup->get_pop();

}

?>