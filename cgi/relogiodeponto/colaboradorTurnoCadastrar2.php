<?php
include "templateUP2.php";

extract($_GET);
$valida = true;

if (empty($turno)) {
	echo "Por favor selecione o turno.<br>";
	$valida = false;
}

if ($valida) {

	$log_colaborador = $id;

	$sql = mysqli_query("select max(id) from turnos where log_colaborador='$id'");
	$linha = mysqli_num_rows($sql);
	if ($linha > 0) {
		$reg = mysqli_fetch_row($sql);
		$instrucao = "UPDATE turnos ";
		$instrucao .= "SET data_vencimento='" . date('Y-m-d') . "' WHERE id = '$reg[0]';";
		$sql = mysqli_query($instrucao);
	}

	$instrucao = "SELECT `dom_t1_e`, `dom_t1_s`, `dom_t2_e`, `dom_t2_s`, `seg_t1_e`, `seg_t1_s`, `seg_t2_e`, `seg_t2_s`, `ter_t1_e`, `ter_t1_s`, `ter_t2_e`, ";
	$instrucao .= "`ter_t2_s`, `qua_t1_e`, `qua_t1_s`, `qua_t2_e`, `qua_t2_s`, `qui_t1_e`, `qui_t1_s`, `qui_t2_e`, `qui_t2_s`, `sex_t1_e`, `sex_t1_s`, `sex_t2_e`, ";
	$instrucao .= "`sex_t2_s`, `sab_t1_e`, `sab_t1_s`, `sab_t2_e`, `sab_t2_s` FROM `turno_padrao` where id='$turno'";
	$sql = mysqli_query($instrucao);
	extract(mysqli_fetch_assoc($sql));

	$instrucao = "INSERT INTO turnos ";
	$instrucao .= "(`log_colaborador`, `data_validade`, `dom_t1_e`, `dom_t1_s`, `dom_t2_e`, `dom_t2_s`, `seg_t1_e`, `seg_t1_s`, `seg_t2_e`, ";
	$instrucao .= "`seg_t2_s`, `ter_t1_e`, `ter_t1_s`, `ter_t2_e`, `ter_t2_s`, `qua_t1_e`, `qua_t1_s`, `qua_t2_e`, `qua_t2_s`, `qui_t1_e`, ";
	$instrucao .= "`qui_t1_s`, `qui_t2_e`, `qui_t2_s`, `sex_t1_e`, `sex_t1_s`, `sex_t2_e`, `sex_t2_s`, `sab_t1_e`, `sab_t1_s`, `sab_t2_e`, ";
	$instrucao .= "`sab_t2_s`) VALUES ('$id', '" . date('Y-m-d') . "', '$dom_t1_e', '$dom_t1_s', '$dom_t2_e', '$dom_t2_s', ";
	$instrucao .= "'$seg_t1_e', '$seg_t1_s', '$seg_t2_e', '$seg_t2_s', '$ter_t1_e', '$ter_t1_s', '$ter_t2_e', '$ter_t2_s', '$qua_t1_e', '$qua_t1_s', '$qua_t2_e', ";
	$instrucao .= "'$qua_t2_s', '$qui_t1_e', '$qui_t1_s', '$qui_t2_e', '$qui_t2_s', '$sex_t1_e', '$sex_t1_s', '$sex_t2_e', '$sex_t2_s', '$sab_t1_e', '$sab_t1_s', ";
	$instrucao .= "'$sab_t2_e', '$sab_t2_s')";
	
	
	$sql = mysqli_query($instrucao);

	confirmacaoDB("Operação realizada com sucesso.", "colaboradorCadastrar.php?id=$log_colaborador");

} else {
	irAuto("colaboradorTurnoCadastrar.php?id=$id", "2");
}

include "templateDOWN2.php";
?>