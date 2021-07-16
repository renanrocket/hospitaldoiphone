<?php
include "templateUP2.php";

function obrigatorio($variavel, $variavelDisplay) {
	global $valida;
	if (empty($variavel)) {
		echo "O campo $variavelDisplay &eacute; obrigat&oacute;rio.<br>";
		$valida = false;
	}
}

function invalido($variavel, $variavelDisplay, $tamanho, $obrigatoriedade = NULL) {
	global $valida;
	if ($obrigatoriedade) {
		if (strlen($variavel) < $tamanho and $variavel <> "") {
			echo "$variavelDisplay inv&aacute;lido.<br>";
			$valida = false;
		}
	} else {
		if (strlen($variavel) < $tamanho) {
			echo "$variavelDisplay inv&aacute;lido.<br>";
			$valida = false;
		}
	}

}

extract($_POST);

$valida = true;

obrigatorio($nome, "Nome");
obrigatorio($login, "Login");
obrigatorio($funcao1, "Fun&ccedil;&atilde;o 1");
obrigatorio($admissao, "Data de admiss&atilde;o");
obrigatorio($telefone1, "Telefone 1");
obrigatorio($salario, "Sal&aacute;rio Base");
obrigatorio($rg, "R.G.");
obrigatorio($cpf, "C.P.F");

if (strstr($nome, " ") == false) {
	echo "Por favor digite o nome completo.<br>";
	$valida = false;
}
$sql = mysqli_query("select nome, id from usuario where login='$login'");
if (mysqli_num_rows($sql) and $op == "novo") {
	echo "j&aacute; existe um usuario com esse login.<br>";
	$valida = false;
}
if ($funcao1 == $funcao2) {
	echo "Escolha uma fun&ccedil;&atilde;o diferente para fun&ccedil;&atilde;o 2 que seja diferente da fun&ccedil;&atilde;o 1.<br>";
	$valida = false;
}
if ($funcao1 == $funcao3) {
	echo "Escolha uma fun&ccedil;&atilde;o diferente para fun&ccedil;&atilde;o 3 que seja diferente da fun&ccedil;&atilde;o 1.<br>";
	$valida = false;
}
if ($funcao2 <> "0" and $funcao3 <> "0" and $funcao2 == $funcao3) {
	echo "Escolha uma fun&ccedil;&atilde;o diferente para fun&ccedil;&atilde;o 3 que seja diferente da fun&ccedil;&atilde;o 2.<br>";
	$valida = false;
}

invalido($admissao, "Data de admiss&atilde;o", "10");
invalido($nascimento, "Data de nascimento", "10", true);
invalido($telefone1, "Telefone 1", "14");
invalido($telefone2, "Telefone 2", "14", true);
invalido($cpf, "C.P.F.", "14");

if ((strstr($email, "@") == false or strstr($email, ".") == false) and $email) {
	echo "E-mail inv&aacute;lido<br>";
	$valida = false;
}

if ($op == "novo" and $valida) {

	$admissao = formataDataInv($admissao);
	$nascimento = formataDataInv($nascimento);
	if (empty($filhos)) {
		$filhos = 0;
	}
	$senha = rand("1000", "9999");

	$instrucao = "insert into usuario ";
	$instrucao .= "(nome, funcao1, funcao2, funcao3, data_admissao, ";
	$instrucao .= "data_nascimento, login, senha, email, telefone1, ";
	$instrucao .= "telefone2, endereco, complemento, bairro, municipio, ";
	$instrucao .= "carteira, rg, cpf, salario_base, filhos, status) values ";
	$instrucao .= "('$nome', '$funcao1', '$funcao2' ,'$funcao3', '$admissao', ";
	$instrucao .= "'$nascimento', '$login', '$senha', '$email', '$telefone1', ";
	$instrucao .= "'$telefone2', '$endereco', '$complemento', '$bairro', '$municipio', ";
	$instrucao .= "'$carteira', '$rg', '$cpf', '$salario', '$filhos', 'Ativo')";

	$sql = mysqli_query($instrucao);
	if (mysqli_affected_rows($conexao) > 0) {
		$sql = mysqli_query("select max(id) from usuario");
		$reg = mysqli_fetch_row($sql);
		echo "<p>Cadastro do $nome efetuado com sucesso.</p><br>";
		echo "Login: $login<br>";
		echo "Senha de primeiro login:<br><span style='font-size: 20px; font-weight:bolder; color:red;'>$senha</span><br><br>";
		echo "Deseja cadastrar o turno desse colaborador? <a href='colaboradorTurnoCadastrar.php?id=$reg[0]'>Clique aqui.</a>";
	} else {
		include_once "msgErro.php";
	}

} elseif ($op == "editar" and $valida) {

	$admissao = formataDataInv($admissao);
	$nascimento = formataDataInv($nascimento);
	if (empty($filhos)) {
		$filhos = 0;
	}

	$instrucao = "update usuario set ";
	$instrucao .= "nome='$nome', funcao1='$funcao1', funcao2='$funcao2', funcao3='$funcao3', data_admissao='$admissao', ";
	$instrucao .= "data_nascimento='$nascimento', email='$email', telefone1='$telefone1', ";
	$instrucao .= "telefone2='$telefone2', endereco='$endereco', complemento='$complemento', bairro='$bairro', municipio='$municipio', ";
	$instrucao .= "carteira='$carteira', rg='$rg', cpf='$cpf', salario_base='$salario', filhos='$filhos', status='$status' ";
	$instrucao .= "where id='$id'";

	$sql = mysqli_query($instrucao);
	if (mysqli_affected_rows($conexao) > 0) {
		echo "Cadastro editado com sucesso.<br>";
		echo historico("-1");
	} else {
		include_once "msgErro.php";
	}

}

include "templateDOWN2.php";
?>