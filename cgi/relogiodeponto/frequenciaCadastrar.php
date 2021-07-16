<?php
include "templateUP2.php";


if($_POST){
		
	$cod = "";
	$valida = true;
	
	extract($_POST);
	
	//filtro do post
	$array= array_keys($_POST);
	$cont= count($_POST)-1;
	
	if(!$_POST[$array[0]]){
		$cod .= "Você não digitou o nome para esse padrão de turno.<br>";
		$valida = false;
		$array[0] = "avisoInput";
	}else{
		$array[0] = "";
	}
	
	for($i=2, $j=4;$i<=$cont;$i+=2, $j+=2){//começa do 1 pois pula a variavel turno e soma mais 2 pois pula pra proxima entrada
		if($j>$cont){
			$j=null;
		}
		
		$sep= explode("_", $array[$i]);
			
		switch ($sep[0]) {
			case 'dom':
				$sep[0] = "Domingo";
				break;
			
			case 'seg':
				$sep[0] = "Segunda";
				break;
				
			case 'ter':
				$sep[0] = "Terça";
				break;
			
			case 'qua':
				$sep[0] = "Quarta";
				break;
			
			case 'qui':
				$sep[0] = "Quinta";
				break;
			
			case 'sex':
				$sep[0] = "Sexta";
				break;
			
			case 'sab':
				$sep[0] = "Sábado";
				break;
		}
		
		switch ($sep[1]) {
			case 't1':
				$sep[1] = "Turno 1";
				break;
			
			case 't2':
				$sep[1] = "Turno 2";
				break;
		}
		
		switch ($sep[2]) {
			case 'e':
				$sep[2] = "Entrada";
				break;
			
			case 's':
				$sep[2] = "Saída";
				break;
		}
		//verifica se pelo menos 1 dos pontos de entrada ou de saida estao vazios e o outro preenchido
		if((empty($_POST[$array[$i]]) and !empty($_POST[$array[$i+1]])) or (!empty($_POST[$array[$i]]) and $_POST[$array[$i]]<>"avisoInput" and empty($_POST[$array[$i+1]]))){
			$cod .= "O $sep[1] de $sep[0] para existir deve-se preencher os horários de entrada e saída.<br>";
			$valida = false;
			$array[$i] = $array[$i+1] = "avisoInput";
		//verifica se o turno de entrada eh maior do q o turno de saida
		}elseif(!empty($_POST[$array[$i]]) and $_POST[$array[$i]]<>"avisoInput" and !empty($_POST[$array[$i+1]]) and (strtotime($_POST[$array[$i]]) >= strtotime($_POST[$array[$i+1]]))){
			$cod.= "O $sep[1] de entrada é maior do que o $sep[1] de saída do $sep[0] (".$_POST[$array[$i]].">=".$_POST[$array[$i+1]].")<br>";
			$valida = false;
			$array[$i] = $array[$i+1] = "avisoInput";
		//verifica se o turno 1 de saida eh maior do q o turno 2 de entrada
		}elseif($sep[1]=="Turno 1" and !empty($_POST[$array[$j]]) and $_POST[$array[$i+1]]<>"avisoInput" and !empty($_POST[$array[$i+1]]) and (strtotime($_POST[$array[$j]]) <= strtotime($_POST[$array[$i+1]]))){
			$cod.= "O $sep[1] de saída é maior do que o $sep[1] de entrada do $sep[0] (".$_POST[$array[$j]].">=".$_POST[$array[$i+1]].")<br>";
			$valida = false;
			$array[$i+1] = "avisoInput";
		}else{
			$array[$i] = $array[$i+1] = NULL;
		}
	
	}
	
	
	
	if ($valida){
		
		
		if($op=="novo"){
			
			$instrucao = "insert into turno_padrao (nome, dom_t1_e, dom_t1_s, dom_t2_e, dom_t2_s, seg_t1_e, seg_t1_s, seg_t2_e, seg_t2_s, ";
			$instrucao.= "ter_t1_e, ter_t1_s, ter_t2_e, ter_t2_s, qua_t1_e, qua_t1_s, qua_t2_e, qua_t2_s, qui_t1_e, qui_t1_s, qui_t2_e, qui_t2_s, ";
			$instrucao.= "sex_t1_e, sex_t1_s, sex_t2_e, sex_t2_s, sab_t1_e, sab_t1_s, sab_t2_e, sab_t2_s) values ";
			$instrucao.= "('$turno', '$dom_t1_e', '$dom_t1_s', '$dom_t2_e', '$dom_t2_s', '$seg_t1_e', '$seg_t1_s', '$seg_t2_e', '$seg_t2_s', ";
			$instrucao.= "'$ter_t1_e', '$ter_t1_s', '$ter_t2_e', '$ter_t2_s', '$qua_t1_e', '$qua_t1_s', '$qua_t2_e', '$qua_t2_s', '$qui_t1_e', '$qui_t1_s', '$qui_t2_e', '$qui_t2_s', ";
			$instrucao.= "'$sex_t1_e', '$sex_t1_s', '$sex_t2_e', '$sex_t2_s', '$sab_t1_e', '$sab_t1_s', '$sab_t2_e', '$sab_t2_s')";
		
		}elseif($op=="novousuario"){
			
			
			
			$sql = mysqli_query("select max(id) from turnos where log_colaborador='$id'");
			$linha = mysqli_num_rows($sql);
			if($linha>0){
				$reg= mysqli_fetch_row($sql);
				$instrucao = "UPDATE turnos ";
				$instrucao.= "SET data_vencimento='".date('Y-m-d')."' WHERE id = '$reg[0]';";
				$sql= mysqli_query($instrucao);
			}
			
			$instrucao = "INSERT INTO turnos ";
            $instrucao.= "(`log_colaborador`, `data_validade`, `dom_t1_e`, `dom_t1_s`, `dom_t2_e`, `dom_t2_s`, `seg_t1_e`, `seg_t1_s`, `seg_t2_e`, ";
			$instrucao.= "`seg_t2_s`, `ter_t1_e`, `ter_t1_s`, `ter_t2_e`, `ter_t2_s`, `qua_t1_e`, `qua_t1_s`, `qua_t2_e`, `qua_t2_s`, `qui_t1_e`, ";
			$instrucao.= "`qui_t1_s`, `qui_t2_e`, `qui_t2_s`, `sex_t1_e`, `sex_t1_s`, `sex_t2_e`, `sex_t2_s`, `sab_t1_e`, `sab_t1_s`, `sab_t2_e`, ";
			$instrucao.= "`sab_t2_s`) VALUES ('$id', '".date('Y-m-d')."', '$dom_t1_e', '$dom_t1_s', '$dom_t2_e', '$dom_t2_s', ";
			$instrucao.= "'$seg_t1_e', '$seg_t1_s', '$seg_t2_e', '$seg_t2_s', '$ter_t1_e', '$ter_t1_s', '$ter_t2_e', '$ter_t2_s', '$qua_t1_e', '$qua_t1_s', '$qua_t2_e', ";
			$instrucao.= "'$qua_t2_s', '$qui_t1_e', '$qui_t1_s', '$qui_t2_e', '$qui_t2_s', '$sex_t1_e', '$sex_t1_s', '$sex_t2_e', '$sex_t2_s', '$sab_t1_e', '$sab_t1_s', ";
			$instrucao.= "'$sab_t2_e', '$sab_t2_s')";
			
		}
		
		
		$sql = mysqli_query($instrucao);

		confirmacaoDB("Turno cadastrado com sucesso.", "admin2.php");
		
	}else{
		if(!isset($id)){
			$id=NULL;
		}
		echo $cod;
		formularioTurno(
		//variaveis de valor
			$op, $id,
			$turno,
			$dom_t1_e, $dom_t1_s, $dom_t2_e, $dom_t2_s,
			$seg_t1_e, $seg_t1_s, $seg_t2_e, $seg_t2_s,
			$ter_t1_e, $ter_t1_s, $ter_t2_e, $ter_t2_s,
			$qua_t1_e, $qua_t1_s, $qua_t2_e, $qua_t2_s,
			$qui_t1_e, $qui_t1_s, $qui_t2_e, $qui_t2_s,
			$sex_t1_e, $sex_t1_s, $sex_t2_e, $sex_t2_s,
			$sab_t1_e, $sab_t1_s, $sab_t2_e, $sab_t2_s,
			$array[0],
			$array[1],$array[2],$array[3],$array[4],
			$array[5],$array[6],$array[7],$array[8],
			$array[9],$array[10],$array[11],$array[12],
			$array[13],$array[14],$array[15],$array[16],
			$array[17],$array[18],$array[19],$array[20],
			$array[21],$array[22],$array[23],$array[24],
			$array[25],$array[26],$array[27],$array[28]
		);
	}
	
}else{
	formularioTurno("novo");
}


include "templateDOWN2.php";
?>