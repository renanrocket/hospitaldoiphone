<?php
date_default_timezone_set('America/Sao_Paulo');
include "../conecta.php";

extract($_POST);
//$refresh = "<meta HTTP-EQUIV='refresh' CONTENT='5;URL=../index.php'>";
$refresh = null;

$sql = mysqli_query($conexao, "select id, nome from usuario where email='$login' and senha='".md5($senha)."' and status='1'");
$linha = mysqli_num_rows($sql);

if ($linha == 1) {

	$reg = mysqli_fetch_row($sql);
	$nome = explode(" ", $reg[1]);
	$idUsuario = $reg[0];
	$data = date('Y-m-d');
	$hora = $face;
	//setando os turnos
	if (strtotime($hora) >= strtotime("04:01") and strtotime($hora) <= strtotime("12:00")) {
		$turno = "Matutino";
	} elseif (strtotime($hora) >= strtotime("12:01") and strtotime($hora) <= strtotime("20:00")) {
		$turno = "Vespertino";
	} else {
		$turno = "Noturnor";
	}
	$diaSemana = date('w');
	//dias da semana multiplicados por 4 pois sao 1 entrada +1 saida (primeiro turno)
	//+1 entrada +1 saida segundo turno.
	//o resultado +4 pois os 3 primeiros registros sao da tabela sao id, log_colaborador, data_validade, data_vencimento
	//+2 pois estou usando como referencia o horario de entrada do segundo turno, para saber em q turno o colaborador
	//esta batendo ponto
	$contador = ($diaSemana * 4) + 4 + 2;

	$instrucao = "select * from relogio_turnos where ";
	$instrucao .= "data_validade=(select (max(data_validade)) from relogio_turnos where id_usuario='$idUsuario') ";
	$instrucao .= "and id_usuario='$idUsuario'";
	
	$sql = mysqli_query($conexao, $instrucao);
	$reg = mysqli_fetch_row($sql);
	//tratando o ponto referencial para ficar 20min antes do horario de ponto do segundo turno
	if ($reg[$contador]) {// verificando se ela existe para n subitrair com null
		$referencia = strtotime($reg[$contador] . '-20 minutes');
		$referencia = date('H:i', $referencia);
	} else {
		//se caso tiver apenas um turno (exemplo: sabado)
		$referencia = strtotime('24:00');
		$referencia = date('H:i', $referencia);
	}

	//condicao para descobrir se ele estar no primeiro turno ou no segundo
	if (strtotime($hora) < strtotime($referencia)) {
		//esta no primeiro turno
		//selecionando se esta entrando ou saindo da empresa
		$sql = mysqli_query($conexao, "select * from relogio_ponto where data='$data' and id_usuario='$idUsuario'");
		$linha = mysqli_num_rows($sql);
		if ($linha == 0) {
			$acao = "Entrar";
		} elseif ($linha == 1) {
			$acao = "Sair";
		}

	} else {
		//esta no segundo turno
		//selecionando se esta entrando ou saindo da empresa
		$sql = mysqli_query($conexao, "select * from relogio_ponto where data='$data' and id_usuario='$idUsuario' and hora>='$referencia'");
		$linha = mysqli_num_rows($sql);
		if ($linha == 0) {
			$acao = "Entrar";
		} elseif ($linha == 1) {
			$acao = "Sair";
		}
	}
	
	//$sql = mysqli_query($conexao, "insert into relogio_ponto (id_usuario, acao, data, hora) values ('$idUsuario', '$acao', '$data', '$hora')");
	/*
	if (mysqli_affected_rows($conexao) >= 0) {
		
		setcookie("login", "$login");
		setcookie("senha", "$senha");
		setcookie("acao", "$acao");
		setcookie("turno", "$turno");
		setcookie("id", mysqli_insert_id($conexao));
	}
	*/

}

function ponto($login, $senha, $acao, $turno, $data, $hora) {
		
	global $cod, $conexao;
	
	$sql = mysqli_query($conexao, "insert into relogio_ponto (id_usuario, acao, data, hora) values ('$login', '$acao', '$data', '$hora')");

	if (mysqli_affected_rows($conexao)) {
		setcookie("login", "$login");
		setcookie("senha", "$senha");
		setcookie("acao", "$acao");
		setcookie("turno", "$turno");
		setcookie("id", mysqli_insert_id($conexao));
		//flash para tirar foto
		$cod .= "Seu registro de $acao no $turno foi marcado.";

		$cod .= '<div class="area" onclick="loadCamera();">
				
				<video autoplay="true" id="webCamera" class="imagemNaoTirada"></video>
				
				<img id="imagemConvertida" class="imagemTirada" />
				
				<input  type="hidden" id="base_img" name="base_img"/>

				<button type="button" onclick="takeSnapShot()" class="imagemNaoTirada">Tirar foto e salvar</button>
				
				<span id="caminhoImagem" class="caminho-imagem imagemTirada">
					<a href="" target="_blank"></a>
				</span>
				
			</div>';

	} else {
		$cod .= "N&atilde;o foi possivel registrar seu ponto, favor tente novamente dentro de instantes ou contate o webmaster.<br>";
		$cod .= $refresh;
	}

}

$cod = "";

$cod .= "<div class='divTopRelogio'>";
$cod .= "<form name='clock' method='post' action='saves/relogiodeponto.php' enctype='multipart/form-data'>";
$cod .= "<img class='imgRelogio' src='../imagens/relogio.png'>";
$cod .= "<input type='text' name='face' style='width:1000px;' value='' class='relogio'>";
$cod .= "</div>";
$cod .= "<div class='divMidRelogio'><br>";

$sql = mysqli_query($conexao, "select id, nome from usuario where email='$login'");
$linha = mysqli_num_rows($sql);

//tratando login e senha
if ($linha <> 1) {
	$cod .= "<br><br>Login inexiste ($login) ou digitado incorretamente.<br>";
	$cod .= $refresh;
} else {
	//encontrou o login agora verificar se a senha esta correta
	$sql = mysqli_query($conexao, "select id, nome from usuario where email='$login' and senha='".md5($senha)."'");
	$linha = mysqli_num_rows($sql);

	if ($linha <> 1) {
		$cod .= "<br><br>Senha incorreta.<br>";
		$cod .= $refresh;
	} else {
		//encontrou o login e a senha e verifica se o usuario esta realmente ativo
		$sql = mysqli_query($conexao, "select id, nome from usuario where email='$login' and senha='".md5($senha)."' and status='1'");
		$linha = mysqli_num_rows($sql);

		if ($linha <> 1) {
			$cod .= "<br><br>Usuario inativo.<br>";
			$cod .= "Para maiores informa&ccedil;&atilde;o trate com a Administra&ccedil;&atilde;o.";
			$cod .= $refresh;
		} else {

			/*
			 *
			 *
			 *'
			 *	finalmente todos os filtros foram passados e agora marcar realmente o ponto
			 *
			 *
			 *
			 *
			 */


			$reg = mysqli_fetch_row($sql);
			$nome = explode(" ", $reg[1]);
			$data = date('Y-m-d');
			$hora = $face;
			//setando os turnos
			if (strtotime($hora) >= strtotime("04:01") and strtotime($hora) <= strtotime("12:00")) {
				$turno = "Matutino";
			} elseif (strtotime($hora) >= strtotime("12:01") and strtotime($hora) <= strtotime("20:00")) {
				$turno = "Vespertino";
			} elseif (strtotime($hora) >= strtotime("20:01") and strtotime($hora) <= strtotime("04:00")) {
				$turno = "Noturnor";
			}
			$diaSemana = date('w');
			//dias da semana multiplicados por 4 pois sao 1 entrada +1 saida (primeiro turno)
			//+1 entrada +1 saida segundo turno.
			//o resultado +4 pois os 3 primeiros registros sao da tabela sao id, log_colaborador, data_validade, data_vencimento
			//+2 pois estou usando como referencia o horario de entrada do segundo turno, para saber em q turno o colaborador
			//esta batendo ponto
			$contador = ($diaSemana * 4) + 4 + 2;

			$instrucao = "select * from relogio_turnos where ";
			$instrucao .= "data_validade=(select (max(data_validade)) from relogio_turnos where id_usuario='$idUsuario') ";
			$instrucao .= "and id_usuario='$idUsuario'";
			$sql = mysqli_query($conexao, $instrucao);
			$reg = mysqli_fetch_row($sql);
			//tratando o ponto referencial para ficar 20min antes do horario de ponto do segundo turno
			if ($reg[$contador]) {// verificando se ela existe para n subitrair com null
				$referencia = strtotime($reg[$contador] . '-20 minutes');
				$referencia = date('H:i', $referencia);
			} else {
				//se caso tiver apenas um turno (exemplo: sabado)
				$referencia = strtotime('24:00');
				$referencia = date('H:i', $referencia);
			}

			//condicao para descobrir se ele estar no primeiro turno ou no segundo
			
			if (strtotime($hora) < strtotime($referencia)) {
				//esta no primeiro turno
				//selecionando se esta entrando ou saindo da empresa
				$sql = mysqli_query($conexao, "select * from relogio_ponto where data='$data' and id_usuario='$idUsuario'");
				
				$linha = mysqli_num_rows($sql);
				if ($linha == 0) {
					ponto($idUsuario, $senha, "Entrar", $turno, $data, $hora);
				} elseif ($linha == 1) {
					ponto($idUsuario, $senha, "Sair", $turno, $data, $hora);
				} else {
					$cod .= "Voc&eacute; j&aacute; registrou seus pontos de entrada e saida para o primeiro turno.<br>";
					$cod .= "Aguarde at&eacute; 20min antes do inicio do segundo turno para registrar o ponto de entrada do segundo turno.";
					$cod .= $refresh;
				}

			} else {
				//esta no segundo turno
				//selecionando se esta entrando ou saindo da empresa
				$sql = mysqli_query($conexao, "select * from relogio_ponto where data='$data' and id_usuario='$idUsuario' and hora>='$referencia'");
				$linha = mysqli_num_rows($sql);
				if ($linha == 0) {
					ponto($idUsuario, $senha, "Entrar", $turno, $data, $hora);
				} elseif ($linha == 1) {
					ponto($idUsuario, $senha, "Sair", $turno, $data, $hora);
				} else {
					$cod .= "<br><br>Voc&eacute; j&aacute; registrou seus pontos de entrada e saida para o segundo turno.<br>";
					$cod .= $refresh;
				}
			}

			
			/*
			 *
			 *
			 *
			 *	e aqui acaba
			 *
			 *
			 *
			 */
		}
	}
}
$cod .= "</div>";


include "templateUP.php";

echo $cod;

include "templateDOWN.php";
?>
