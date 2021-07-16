<?php
include "templateUP2.php";

if ($_POST) {
	$login = $_POST["login"];
	$senha = $_POST["senha"];
} else {
	$login = "";
	$senha = "";
}

//funcao formulario de login: exibe na tela um formulario de login q envia os dados para esta pagina
function loginForm($msg) {
	echo "<form method='post' action='admin.php' enctype='multipart/form-data' style='position:relative; top:25%;'>";
	echo "<b><blink>$msg</blink></b>";
	echo "<p><input id='login' type='text' name='login' value='Login' size='10' 
				onfocus=\"if (this.value == 'Login') {this.value = '';}\" 
				onblur=\"if (this.value == '') {this.value = 'Login';}\"></p>";
	echo "<p><input id='senha' type='password' name='senha' value='Senha' size='10' 
				onfocus=\"if (this.value == 'Senha') {this.value = '';}\" 
				onblur=\"if (this.value == '') {this.value = 'Senha';}\"></p>";
	echo "<input type='submit' value='Enviar'>";
	echo "</form>";
}

$sql = mysqli_query($conexao, "select nome from usuario where email='$login'");
if (!mysqli_query($conexao, "select nome from usuario where email='$login'")){
  echo("Error description: " . mysqli_error($conexao));
}

$linha = mysqli_num_rows($sql);

//se nao encontrar o login fornecido no db
if ($linha <> 1) {
	echo loginForm("Login incorreto.<br>");
} else {

	$sql = mysqli_query($conexao, "select nome from usuario where email='$login' and senha='".md5($senha)."'");
	if (!mysqli_query($conexao, "select nome from usuario where email='$login'")){
	  echo("Error description: " . mysqli_error($conexao));
	}
	$linha = mysqli_num_rows($sql);

	//se nao encontrar a senha correta com login fornecido
	if ($linha <> 1) {
		echo loginForm("Senha inv&aacute;lida.<br>Seu login &eacute; mesmo $login?<br>");
	} else {

		$sql = mysqli_query($conexao, "select nome from usuario where email='$login' and senha='".md5($senha)."' and status='1'");
		$linha = mysqli_num_rows($sql);

		//se nao encontrar a status=Ativo com o login e senha fornecidos
		if ($linha <> 1) {
			echo "Voc&ecirc; n&atilde;o mais ativo,<br>para maiores informa&ccedil;&otilde;e<br>contate a adminsitra&ccedil;&atilde;o.<br>";
			echo "<meta http-equiv='refresh' content='5;url=index.php'> ";
		} else {

			$sql = mysqli_query($conexao, "select nome from usuario where email='$login' and senha='".md5($senha)."' and status='1' and id_funcao='1'");
			$linha = mysqli_num_rows($sql);

			//se nao encontrar a status=Ativo com o login e senha fornecidos
			if ($linha <> 1) {
				echo "$login, voc&ecirc; est&aacute; logado.<br>";
				echo "<meta http-equiv='refresh' content='2;url=setcredencial.php?login=$login&senha=$senha&tipo=usuario&redirect=admin2&template=2'> ";
			} else {
				echo "$login, voc&ecirc; est&aacute; logado.<br>";
				echo "<meta http-equiv='refresh' content='2;url=setcredencial.php?login=$login&senha=$senha&tipo=admin&redirect=admin2&template=2'> ";
			}

		}
	}
}

include "templateDOWN2.php";
?>