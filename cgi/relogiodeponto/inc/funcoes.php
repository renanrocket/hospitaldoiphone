<?php

// funcao que corrige o problema do ponto e da virgula em valores que envolve moeda. essa funcao recebem um valor float
function real($num) {

	if ($num) {
		$num = str_replace(".", ",", $num);
		if (strstr($num, ",") == false) {
			$num .= ",00";
		}
		$check = explode(",", $num);
		if (strstr($num, ",") == true and strlen($check[1]) < 2) {
			$num .= "0";
		}
	} else {
		$num = "00,00";
	}

	return $num;
}

//funcao nome que recebem o login da pessoa e armazena em uma variavel o primeiroNome segundoNome (login)
//recebe um parametro string com o login da pessoa
function login($login) {

	if (!(empty($login))) {
		if (is_numeric($login)) {
			$sql = mysqli_query("select nome from colaborador where cont='$login'");
		} else {
			$sql = mysqli_query("select nome from colaborador where login='$login'");

		}

		if (mysqli_num_rows($sql) < 1) {
			$instrucao = $login;
		} else {
			$reg = mysqli_fetch_row($sql);
			$nome = explode(" ", $reg[0]);
			$num = (count($nome)) - 1;
			$instrucao = $nome[0] . " " . $nome[$num];
		}

	} else {
		$instrucao = "--";
	}
	return $instrucao;

}

//funcao data, formata a data. Recebe uma variavel assim: YYYY-MM-DD HH:II:SS
//e a transforma assim: DD/MM/YYYY HH:II:SS
function formataData($DATA) {
	if ($DATA and ($DATA <> "0000-00-00 00:00:00" and $DATA <> "0000-00-00")) {
		$data = explode("-", $DATA);
		if (strstr($data[2], " ") == True) {
			$Data = explode(" ", $data[2]);
			return $Data[0] . "/" . $data[1] . "/" . $data[0] . " " . $Data[1];
		} else {
			return $data[2] . "/" . $data[1] . "/" . $data[0];
		}
	} else {
		return "";
	}
}

//funcao formataDataInv, formata a data para forma do banco de dados. Recebe uma variavel assim DD/MM/YYYY HH:II:SS
//e a transforma assim: YYYY-MM-DD HH:II:SS
function formataDataInv($data) {
	if (strlen($data) > 0) {
		$data = explode("/", $data);
		if (strstr($data[2], " ", true)) {
			$Data = explode(" " . $data[2]);
			$data = $Data[0] . "-" . $data[1] . "-" . $data[0] . " " . $Data[1];
		} else {
			$data = $data[2] . "-" . $data[1] . "-" . $data[0];
		}
	} else {
		$data = "0000-00-00 00:00:00";
	}
	return $data;
}

//funcao que transforma um valor em numero extenso. recebe o valor no formato 123.13 onde apois o ponto representa os
//centavos e recebe true ou false para saber se a primeira letra de cada palavra eh maiuscula (true) ou menuscula(false)
function extenso($valor = 0, $maiusculas = false) {

	$singular = array("centavo", "real", "mil", "milh&atilde;o", "bilhão", "trilh&atilde;o", "quatrilh&atilde;o");
	$plural = array("centavos", "reais", "mil", "milh&otilde;es", "bilh&otilde;es", "trilh&otilde;es", "quatrilh&otilde;es");

	$c = array("", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
	$d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa");
	$d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove");
	$u = array("", "um", "dois", "tr&ecirc;s", "quatro", "cinco", "seis", "sete", "oito", "nove");

	$z = 0;
	$rt = "";

	$valor = number_format($valor, 2, ".", ".");
	$inteiro = explode(".", $valor);
	for ($i = 0; $i < count($inteiro); $i++)
		for ($ii = strlen($inteiro[$i]); $ii < 3; $ii++)
			$inteiro[$i] = "0" . $inteiro[$i];

	$fim = count($inteiro) - ($inteiro[count($inteiro) - 1] > 0 ? 1 : 2);
	for ($i = 0; $i < count($inteiro); $i++) {
		$valor = $inteiro[$i];
		$rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
		$rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
		$ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

		$r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd && $ru) ? " e " : "") . $ru;
		$t = count($inteiro) - 1 - $i;
		$r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
		if ($valor == "000")
			$z++;
		elseif ($z > 0)
			$z--;
		if (($t == 1) && ($z > 0) && ($inteiro[0] > 0))
			$r .= (($z > 1) ? " de " : "") . $plural[$t];
		if ($r)
			$rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? (($i < $fim) ? ", " : " e ") : " ") . $r;
	}

	if (!$maiusculas) {
		return ($rt ? $rt : "zero");
	} else {
		if ($rt)
			$rt = preg_replace("{ E }", " e ", ucwords($rt));
		return (($rt) ? ($rt) : "Zero");
	}

}

//volta ou avança a pagina recebe um numero inteiro
function historico($valor) {
	if ($valor < 0) {
		$txt = "Voltar.";
	} else {
		$txt = "Ir.";
	}
	return "<br><a href='javascript:window.history.go($valor)'>$txt</a><br>";
}

//funcao para montar os selects do tipo de pagamento forma de pagamento e bandeira
function opcaoSelect($nomeTb, $status, $selected = NULL, $usar_reg1 = NULL) {
	$sql = mysqli_query("select * from $nomeTb where status='$status'");
	$linhas = mysqli_num_rows($sql);
	$script = "<option value='0'>--</option>";
	for ($i = 0; $i < $linhas; $i++) {
		$reg = mysqli_fetch_row($sql);

		if ($usar_reg1) {
			if ($reg[1] == $selected) {
				$script .= "<option value='$reg[1]' selected>$reg[1]</option>";
			} else {
				$script .= "<option value='$reg[1]'>$reg[1]</option>";
			}
		} else {
			if ($reg[0] == $selected) {
				$script .= "<option value='$reg[0]' selected>$reg[1]</option>";
			} else {
				$script .= "<option value='$reg[0]'>$reg[1]</option>";
			}
		}

	}
	return $script;
}

function turnZero($var) {
	if (empty($var) or $var == "") {
		$var = 0;
	}
	return $var;
}

//funcao q seleciona o atributo da tabela a partir de um resultado do PK
//pk eh o numero do cont
//nomeTabela eh o nome da tabela a qual se refere
//atributo eh o numero do atributo na sequencia q segue a tabela
function registro($pk, $nomeTabela, $atributo) {
	global $conexao;
	$sql = mysqli_query($conexao, "select $atributo from $nomeTabela where id='$pk'");
	echo "select * from $nomeTabela where id='$pk'";
	$reg = mysqli_fetch_row($sql);
	return $reg[0];
}

//funcao para pegar a primeira e a ultima palavra da string
//usualmente no mapa de OS e derivados
function firstLast($nome) {
	if ($nome == "--") {
		$retorno = $nome;
	} else {
		$nome = explode(" ", $nome);
		$tamanho = count($nome) - 1;
		$retorno = "$nome[0] $nome[$tamanho]";
	}
	return $retorno;
}

//se vazio retorna -- para variavel
function isEmpty($variavel) {
	if (empty($variavel)) {
		$variavel = "--";
	} elseif ($variavel == "0000-00-00 00:00:00" or $variavel == "00/00/0000 00:00:00") {
		$variavel = "--";
	}
	return $variavel;
}

//funcao atualizar pagina
function irAuto($destino, $tempo) {
	return "<meta http-equiv='refresh' content='" . $tempo . ";url=" . $destino . "'>";
}

//funcao de confirmação do banco de dados
function confirmacaoDB($msg, $redirecionamento) {

	global $conexao;

	if (mysqli_affected_rows($conexao) > 0) {
		echo "$msg<br>";
		echo "<meta HTTP-EQUIV='refresh' CONTENT='2;URL=$redirecionamento'>";
	} else {
		include_once "msgErro.php";
	}
	echo "<br>";
}

//funcao para descobrir se existe alguma string dentro de um array dentro de outro array
//usado para descobrir se um usuario possui uma das funcoes nas suas 3 funcoes
function isFuncao($arrayDeBusca, $array) {
	$count = count($arrayDeBusca);
	$mach = false;
	for ($i = 0; $i < $count; $i++) {
		if ($mach) {
			$i = $count;
		} else {
			$mach = in_array($arrayDeBusca[$i], $array);
		}
	}
	return $mach;
}

function formularioTurno(
//funcao para determinar q tipo de formulario eh
$op = null, $id = null,
//variaveis de valor
$turno = null,
$dom_t1_e = null, $dom_t1_s = null, $dom_t2_e = null, $dom_t2_s = null,
$seg_t1_e = null, $seg_t1_s = null, $seg_t2_e = null, $seg_t2_s = null,
$ter_t1_e = null, $ter_t1_s = null, $ter_t2_e = null, $ter_t2_s = null,
$qua_t1_e = null, $qua_t1_s = null, $qua_t2_e = null, $qua_t2_s = null,
$qui_t1_e = null, $qui_t1_s = null, $qui_t2_e = null, $qui_t2_s = null,
$sex_t1_e = null, $sex_t1_s = null, $sex_t2_e = null, $sex_t2_s = null,
$sab_t1_e = null, $sab_t1_s = null, $sab_t2_e = null, $sab_t2_s = null,
//variaveis de alerta
$turno_a = null,
$dom_t1_e_a = null, $dom_t1_s_a = null, $dom_t2_e_a = null, $dom_t2_s_a = null,
$seg_t1_e_a = null, $seg_t1_s_a = null, $seg_t2_e_a = null, $seg_t2_s_a = null,
$ter_t1_e_a = null, $ter_t1_s_a = null, $ter_t2_e_a = null, $ter_t2_s_a = null,
$qua_t1_e_a = null, $qua_t1_s_a = null, $qua_t2_e_a = null, $qua_t2_s_a = null,
$qui_t1_e_a = null, $qui_t1_s_a = null, $qui_t2_e_a = null, $qui_t2_s_a = null,
$sex_t1_e_a = null, $sex_t1_s_a = null, $sex_t2_e_a = null, $sex_t2_s_a = null,
$sab_t1_e_a = null, $sab_t1_s_a = null, $sab_t2_e_a = null, $sab_t2_s_a = null
){
	
	echo "Não preencha os horários em que não houver turno.";
	
	echo "<form method='post' action='frequenciaCadastrar.php' enctype='multipart/form-data'>";
	echo "<input type='hidden' name='op' value='$op'>";
	if($id){
		echo "<input type='hidden' name='id' value='$id'>";
	}
	
	
	echo "
	<table>
		<tr>
			<th colspan='2'>Turno</th>
			<th colspan='3'><input type='text' name='turno' value='$turno' id='$turno_a'/></th>
		</tr>
		
		<tr>
			<th rowspan='2' class='turno'>Domingo</th>
			<th class='turnoTop'>Turno 1</th>
			<th class='turnoTop'><input type='text' class='hora' id='$dom_t1_e_a' name='dom_t1_e' value='$dom_t1_e' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'/></th>
			<th class='turnoTop'>às</th>
			<th class='turnoTop2'><input type='text' class='hora' id='$dom_t1_s_a' name='dom_t1_s' value='$dom_t1_s' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'/></th>
		</tr>
		<tr>
			<th class='turnoBottom'>Turno 2</th>
			<th class='turnoBottom'><input type='text' class='hora' id='$dom_t2_e_a' name='dom_t2_e' value='$dom_t2_e' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'/></th>
			<th class='turnoBottom'>às</th>
			<th class='turnoBottom2'><input type='text' class='hora' id='$dom_t2_s_a' name='dom_t2_s' value='$dom_t2_s' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'/></th>
		</tr>
		
		<tr>
			<th rowspan='2'>Segunda</th>
			<th>Turno 1</th>
			<th><input type='text' class='hora' id='$seg_t1_e_a' name='seg_t1_e' value='$seg_t1_e' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'/></th>
			<th>às</th>
			<th><input type='text' class='hora' id='$seg_t1_s_a' name='seg_t1_s' value='$seg_t1_s' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'/></th>
		</tr>
		<tr>
			<th>Turno 2</th>
			<th><input type='text' class='hora' id='$seg_t2_e_a' name='seg_t2_e' value='$seg_t2_e' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'/></th>
			<th>às</th>
			<th><input type='text' class='hora' id='$seg_t2_s_a' name='seg_t2_s' value='$seg_t2_s' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'/></th>
		</tr>
		
		<tr>
			<th rowspan='2' class='turno'>Terça</th>
			<th class='turnoTop'>Turno 1</th>
			<th class='turnoTop'><input type='text' class='hora' id='$ter_t1_e_a' name='ter_t1_e' value='$ter_t1_e' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'/></th>
			<th class='turnoTop'>às</th>
			<th class='turnoTop2'><input type='text' class='hora' id='$ter_t1_s_a' name='ter_t1_s' value='$ter_t1_s' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'/></th>
		</tr>
		<tr>
			<th class='turnoBottom'>Turno 2</th>
			<th class='turnoBottom'><input type='text' class='hora' id='$ter_t2_e_a' name='ter_t2_e' value='$ter_t2_e' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'/></th>
			<th class='turnoBottom'>às</th>
			<th class='turnoBottom2'><input type='text' class='hora' id='$ter_t2_s_a' name='ter_t2_s' value='$ter_t2_s' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'/></th>
		</tr>
		
		<tr>
			<th rowspan='2'>Quarta</th>
			<th>Turno 1</th>
			<th><input type='text' class='hora' id='$qua_t1_e_a' name='qua_t1_e' value='$qua_t1_e' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'/></th>
			<th>às</th>
			<th><input type='text' class='hora' id='$qua_t1_s_a' name='qua_t1_s' value='$qua_t1_s' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'/></th>
		</tr>
		<tr>
			<th>Turno 2</th>
			<th><input type='text' class='hora' id='$qua_t2_e_a' name='qua_t2_e' value='$qua_t2_e' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'/></th>
			<th>às</th>
			<th><input type='text' class='hora' id='$qua_t2_s_a' name='qua_t2_s' value='$qua_t2_s' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'/></th>
		</tr>
		
		<tr>
			<th rowspan='2' class='turno'>Quinta</th>
			<th class='turnoTop'>Turno 1</th>
			<th class='turnoTop'><input type='text' class='hora' id='$qui_t1_e_a' name='qui_t1_e' value='$qui_t1_e' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'/></th>
			<th class='turnoTop'>às</th>
			<th class='turnoTop2'><input type='text' class='hora' id='$qui_t1_s_a' name='qui_t1_s' value='$qui_t1_s' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'/></th>
		</tr>
		<tr>
			<th class='turnoBottom'>Turno 2</th>
			<th class='turnoBottom'><input type='text' class='hora' id='$qui_t2_e_a' name='qui_t2_e' value='$qui_t2_e' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'/></th>
			<th class='turnoBottom'>às</th>
			<th class='turnoBottom2'><input type='text' class='hora' id='$qui_t2_e_a' name='qui_t2_s' value='$qui_t2_s' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'/></th>
		</tr>
		
		<tr>
			<th rowspan='2'>Sexta</th>
			<th>Turno 1</th>
			<th><input type='text' class='hora' id='$sex_t1_e_a' name='sex_t1_e' value='$sex_t1_e' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'/></th>
			<th>às</th>
			<th><input type='text' class='hora' id='$sex_t1_s_a' name='sex_t1_s' value='$sex_t1_s' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'/></th>
		</tr>
		<tr>
			<th>Turno 2</th>
			<th><input type='text' class='hora' id='$sex_t2_e_a' name='sex_t2_e' value='$sex_t2_e' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'/></th>
			<th>às</th>
			<th><input type='text' class='hora' id='$sex_t2_s_a' name='sex_t2_s' value='$sex_t2_s' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'/></th>
		</tr>
		
		<tr>
			<th rowspan='2' class='turno'>Sábado</th>
			<th class='turnoTop'>Turno 1</th>
			<th class='turnoTop'><input type='text' class='hora' id='$sab_t1_e_a' name='sab_t1_e' value='$sab_t1_e' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'/></th>
			<th class='turnoTop'>às</th>
			<th class='turnoTop2'><input type='text' class='hora' id='$sab_t1_s_a' name='sab_t1_s' value='$sab_t1_s' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'/></th>
		</tr>
		<tr>
			<th class='turnoBottom'>Turno 2</th>
			<th class='turnoBottom'><input type='text' class='hora' id='$sab_t2_e_a' name='sab_t2_e' value='$sab_t2_e' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'/></th>
			<th class='turnoBottom'>às</th>
			<th class='turnoBottom2'><input type='text' class='hora' id='$sab_t2_s_a' name='sab_t2_s' value='$sab_t2_s' maxlength='5' onKeyDown='Mascara(this,Hora);' onKeyPress='Mascara(this,Hora);' onKeyUp='Mascara(this,Hora);'/></th>
		</tr>
		
	</table>
	<input type='submit' value='Enviar'>
	</form>
	
	";
	
	
}

?>
