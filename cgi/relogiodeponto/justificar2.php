<?php
include "templateUP2.php";

extract($_POST);
//id(colaborador) justificativa (Atrazo, Saida antes do horario, Falta Justificada, Feriado, Licenca)
//turno(t1, t2, t1t2) descricao dia mes ano

$valida = TRUE;

if (empty($id)) {
	echo "Selecione o colaborador para a justificativa.<br>";
	$valida = FALSE;
}
if (empty($justificativa)) {
	echo "Selecione a Justificativa.<br>";
	$valida = FALSE;
}
if (empty($turno)) {
	echo "Selecione o turno no qual deseje justificar.<br>";
	$valida = FALSE;
}
if (empty($descricao)) {
	echo "Digite uma breve descri&ccedil;&atilde;o para essa justificativa.<br>";
	$valida = FALSE;
}

if ($valida) {

	$sql = mysqli_query("select login from usuario where id='$id'");
	$loginColaborado = mysqli_fetch_row($sql);
	$instrucao = "select * from turnos where log_colaborador='$id' and data_validade=(select max(data_validade) from turnos where log_colaborador='$id')";
	$sql = mysqli_query($instrucao);
	extract(mysqli_fetch_assoc($sql));
	//id log_colaborador dava_validade data_vencimento dom_t1_e dom_t1_s dom_t2_e dom_t2_s
	//seg_t1_e seg_t1_s seg_t2_e seg_t2_s ter_t1_e ter_t1_s ter_t2_e ter_t2_s qua_t1_e qua_t1_s qua_t2_e qua_t2_s
	//qui_t1_e qui_t1_s qui_t2_e qui_t2_s sex_t1_e sex_t1_s sex_t2_e sex_t2_s sab_t1_e sab_t1_s sab_t2_e sab_t2_s

	$diaSemana = date('w', mktime(0, 0, 0, $mes, $dia, $ano));
	if ($diaSemana == 0) {
		$horaCheck1e = $dom_t1_e;
		$horaCheck1s = $dom_t1_s;
		$horaCheck2e = $dom_t2_e;
		$horaCheck2s = $dom_t2_s;
	} elseif ($diaSemana == 1) {
		$horaCheck1e = $seg_t1_e;
		$horaCheck1s = $seg_t1_s;
		$horaCheck2e = $seg_t2_e;
		$horaCheck2s = $seg_t2_s;
	} elseif ($diaSemana == 2) {
		$horaCheck1e = $ter_t1_e;
		$horaCheck1s = $ter_t1_s;
		$horaCheck2e = $ter_t2_e;
		$horaCheck2s = $ter_t2_s;
	} elseif ($diaSemana == 3) {
		$horaCheck1e = $qua_t1_e;
		$horaCheck1s = $qua_t1_s;
		$horaCheck2e = $qua_t2_e;
		$horaCheck2s = $qua_t2_s;
	} elseif ($diaSemana == 4) {
		$horaCheck1e = $qui_t1_e;
		$horaCheck1s = $qui_t1_s;
		$horaCheck2e = $qui_t2_e;
		$horaCheck2s = $qui_t2_s;
	} elseif ($diaSemana == 5) {
		$horaCheck1e = $sex_t1_e;
		$horaCheck1s = $sex_t1_s;
		$horaCheck2e = $sex_t2_e;
		$horaCheck2s = $sex_t2_s;
	} elseif ($diaSemana == 6) {
		$horaCheck1e = $sab_t1_e;
		$horaCheck1s = $sab_t1_s;
		$horaCheck2e = $sab_t2_e;
		$horaCheck2s = $sab_t2_s;
	}

	if ($justificativa == "Atrazo") {

		switch ($turno) {
			case 't1' :
				$instrucao = "update ponto set hora='$horaCheck1e', justificativa='$justificativa', descricao='$descricao' where hora<'$horaCheck1s' and acao='Entrar' and ";
				$instrucao .= "data='$ano-$mes-$dia' and login='$loginColaborado[0]'";
				$sql = mysqli_query($instrucao); echo "<br>$instrucao<br>";
				break;
			case 't2' :
				$instrucao = "update ponto set hora='$horaCheck1e', justificativa='$justificativa', descricao='$descricao' where hora>'$horaCheck1s' and acao='Entrar' and ";
				$instrucao .= "data='$ano-$mes-$dia' and login='$loginColaborado[0]'";
				$sql = mysqli_query($instrucao); echo "<br>$instrucao<br>";
				break;
			case 't1t2' :
				$instrucao = "update ponto set hora='$horaCheck1e', justificativa='$justificativa', descricao='$descricao' where hora<'$horaCheck1s' and acao='Entrar' and ";
				$instrucao .= "data='$ano-$mes-$dia' and login='$loginColaborado[0]'";
				$sql = mysqli_query($instrucao); echo "<br>$instrucao<br>";
				$instrucao = "update ponto set hora='$horaCheck1e', justificativa='$justificativa', descricao='$descricao' where hora>'$horaCheck1s' and acao='Entrar' and ";
				$instrucao .= "data='$ano-$mes-$dia' and login='$loginColaborado[0]'";
				$sql = mysqli_query($instrucao); echo "<br>$instrucao<br>";
				break;
		}

	} elseif ($justificativa == "Saida antes do horario") {

		switch ($turno) {
			case 't1' :
				if ($horaCheck2e){
					$instrucao = "update ponto set hora='$horaCheck1s', justificativa='$justificativa', descricao='$descricao' where hora<'$horaCheck2e' and acao='Sair' and ";
				}else{
					$instrucao = "update ponto set hora='$horaCheck1s', justificativa='$justificativa', descricao='$descricao' where and acao='Sair' and ";
				}
				$instrucao .= "data='$ano-$mes-$dia' and login='$loginColaborado[0]'";
				$sql = mysqli_query($instrucao); echo "<br>$instrucao<br>";
				break;
			case 't2' :
				$instrucao = "update ponto set hora='$horaCheck2s', justificativa='$justificativa', descricao='$descricao' where hora>'$horaCheck2e' and acao='Sair' and ";
				$instrucao .= "data='$ano-$mes-$dia' and login='$loginColaborado[0]'";
				$sql = mysqli_query($instrucao); echo "<br>$instrucao<br>";
				break;
			case 't1t2' :
				$instrucao = "update ponto set hora='$horaCheck1s', justificativa='$justificativa', descricao='$descricao' where hora<'$horaCheck2e' and acao='Sair' and ";
				$instrucao .= "data='$ano-$mes-$dia' and login='$loginColaborado[0]'";
				$sql = mysqli_query($instrucao); echo "<br>$instrucao<br>";
				$instrucao = "update ponto set hora='$horaCheck2s', justificativa='$justificativa', descricao='$descricao' where hora>'$horaCheck2e' and acao='Sair' and ";
				$instrucao .= "data='$ano-$mes-$dia' and login='$loginColaborado[0]'";
				$sql = mysqli_query($instrucao); echo "<br>$instrucao<br>";
				break;
		}

	}elseif($justificativa == "Falta justificada" or $justificativa == "Feriado" or $justificativa == "Licenca"){
		
		switch ($turno) {
			case 't1' :
				
				$instrucao = "insert into ponto ";
				$instrucao .= "(login, acao, data, hora, end_foto, justificativa, descricao) values ";
				$instrucao .= "('$loginColaborado[0]', 'Entrar', '$ano-$mes-$dia', '$horaCheck1e', 'x','$justificativa', '$descricao'), ";
				$instrucao .= "('$loginColaborado[0]', 'Sair', '$ano-$mes-$dia', '$horaCheck1s', 'x','$justificativa', '$descricao')";
				$sql = mysqli_query($instrucao); echo "<br>$instrucao<br>";
				
				break;
			case 't2' :
			
				$instrucao = "insert into ponto ";
				$instrucao .= "(login, acao, data, hora, end_foto, justificativa, descricao) values ";
				$instrucao .= "('$loginColaborado[0]', 'Entrar', '$ano-$mes-$dia', '$horaCheck2e', 'x','$justificativa', '$descricao'), ";
				$instrucao .= "('$loginColaborado[0]', 'Sair', '$ano-$mes-$dia', '$horaCheck2s', 'x','$justificativa', '$descricao')";
				$sql = mysqli_query($instrucao); echo "<br>$instrucao<br>";
				
				break;
			case 't1t2' :
				
				$instrucao = "insert into ponto ";
				$instrucao .= "(login, acao, data, hora, end_foto, justificativa, descricao) values ";
				$instrucao .= "('$loginColaborado[0]', 'Entrar', '$ano-$mes-$dia', '$horaCheck1e', 'x','$justificativa', '$descricao'), ";
				$instrucao .= "('$loginColaborado[0]', 'Sair', '$ano-$mes-$dia', '$horaCheck1s', 'x','$justificativa', '$descricao')";
				$sql = mysqli_query($instrucao); echo "<br>$instrucao<br>";
				
				$instrucao = "insert into ponto ";
				$instrucao .= "(login, acao, data, hora, end_foto, justificativa, descricao) values ";
				$instrucao .= "('$loginColaborado[0]', 'Entrar', '$ano-$mes-$dia', '$horaCheck2e', 'x','$justificativa', '$descricao'), ";
				$instrucao .= "('$loginColaborado[0]', 'Sair', '$ano-$mes-$dia', '$horaCheck2s', 'x','$justificativa', '$descricao')";
				$sql = mysqli_query($instrucao); echo "<br>$instrucao<br>";
				
				break;
		}
		
	}

}

include "templateDOWN2.php";
?>