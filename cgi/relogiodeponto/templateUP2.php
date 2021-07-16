<?php
include "conecta.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
            "http://www.w3.org/TR/html4/loose.dtd">
<html>
<header>
	<title>
		Relógio de Ponto
	</title>
	<meta http-equiv="content-Type" content="text/html; charset=UTF-8" />
	<meta charset="UTF-8">
	<script type="text/javascript" src="mascara.js"></script>
	<style type="text/css">
		@import url(admin.css);
	</style>
	<script type="text/javascript" src="mascara.js"></script>
	<script language="JavaScript">
		<!--
		function msg(){
			//alert("Voce nao esta logado");
		}
		
		
		function showtime(){
			setTimeout("showtime();",1000);
			callerdate.setTime(callerdate.getTime()+1000);
			var hh = String(callerdate.getHours());
			var mm = String(callerdate.getMinutes());
			var ss = String(callerdate.getSeconds());
			document.clock.hora.value =
			((hh < 10) ? " " : "") + hh;
			document.clock.min.value =
			((mm < 10) ? "0" : "") + mm;
			document.clock.seg.value =
			((ss < 10) ? "0" : "") + ss;
			
		}
		
		callerdate=new Date(<?php date_default_timezone_set('America/Sao_Paulo'); echo date("Y,m,d,H,i,s");?>);
		//-->
	</script>
	<link rel="shortcut icon" href="imagem/icon.png">
</header>
	<?php
		$login = "";
		$senha = "";
		$tipo = "";
		
		$titulo = explode("/", $_SERVER['PHP_SELF']);
		$linha = (count($titulo)) - 1;
		
		
		if (empty($_COOKIE["login"]) and $titulo[$linha]=="admin.php"){
			echo "<body><center>";
		}elseif(empty($_COOKIE["login"]) and $titulo[$linha]<>"admin.php"){
			echo "<body onload='msg();'><center>";
			echo "<meta http-equiv='refresh' content='2;url=admin.php'> ";
		}elseif(!empty($_COOKIE["login"]) and $titulo[$linha]<>"admin.php"){
			extract($_COOKIE);
			echo "<body><center>";
			include_once "inc/funcoes.php";
			include_once "inc/menu.inc";
		}
		
		
		//login senha tipo
		echo "<center><div id='tableMain'>";

		
		
	?>
