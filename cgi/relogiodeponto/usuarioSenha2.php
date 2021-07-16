<?php
include "templateUP2.php";
	
	extract($_POST);
	$login= $_COOKIE["login"];
	$valida= true;
	
	if(strlen($nova)<5){
		echo "Desculpe, mas senha nova muito curta.<br>";
		echo historico("-1");
		$valida= false;
	}
	$sql= mysqli_query("select * from usuario where id='$id' and senha='$antiga'");
	if(mysqli_num_rows($sql)<=0){
		echo "Desculpe, mas senha antiga incorreta.<br>";
		echo historico("-1");
		$valida= false;
	}
	if($nova<>$repitenova){
		echo "Desculpe, mas voc&ecirc repetiu a senha incorretamente.<br>";
		echo historico("-1");
		$valida= false;
	}
	if ($valida){
		
		
		$instrucao= "update usuario set senha='$nova' where id='$id'";
		$sql= mysqli_query($instrucao);
		
		if (mysqli_affected_rows($conexao) > 0) {
		echo "Senha alterada com sucesso.<br>";
			echo historico("-2");
		} else {
			include_once "msgErro.php";
		}
		
	}
	
	
include "templateDOWN2.php";
?>