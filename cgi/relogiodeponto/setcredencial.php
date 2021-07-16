<?php
$login = $_GET["login"];
$senha = $_GET["senha"];
$tipo = $_GET["tipo"];
$redirect = $_GET["redirect"];
$template = $_GET["template"];

//seta os cookies administradores e redireciona para pagina da administração
setcookie('login', $login, time() + 3600);
//esse cookie expira em 1 hr
setcookie('senha', $senha, time() + 3600);
//esse cookie expira em 1 hr
setcookie('tipo', $tipo, time() + 3600);
//esse cookie expira em 1 hr

switch ($template) {
	case '2' :
		include_once "templateUP2.php";
		break;

	default :
		include_once "templateUP.php";
		break;
}
echo "Setando credênciais";
echo "<meta http-equiv='refresh' content='1;url=" . $redirect . ".php'>";

switch ($template) {
	case '2' :
		include_once "templateDOWN2.php";
		break;

	default :
		include_once "templateDOWN.php";
		break;
}
?>
