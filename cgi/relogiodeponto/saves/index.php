<html>
	<head>
		<title>Rel&oacute;gio de ponto</title>
		<script language="JavaScript">
		<!--
		function showtime()
		{ setTimeout("showtime();",1000);
		callerdate.setTime(callerdate.getTime()+1000);
		var hh = String(callerdate.getHours());
		var mm = String(callerdate.getMinutes());
		var ss = String(callerdate.getSeconds());
		document.clock.face.value =
		((hh < 10) ? " " : "") + hh +
		((mm < 10) ? ":0" : ":") + mm +
		((ss < 10) ? ":0" : ":") + ss;
		
		}
		
		callerdate=new Date(<?php date_default_timezone_set('America/Sao_Paulo'); echo date("Y,m,d,H,i,s");?>);
		//-->
		</script>
		<link rel="shortcut icon" href="icon.png">
	</head>
		
	<body onLoad="showtime()" background='bg.jpg'>	
		<?php
		setcookie("login","");
		setcookie("senha","");
		setcookie("acao","");
		?>
	<font face='Arial'><center><br><br><br><br><br><br><br><br><br><br><br><table bgcolor='white'><tr><td><center>
	<form name="clock"><input type="text" name="face" style="border:0px #000000 solid;background-color:transparent;color:#FF0000;font-family:Arial;font-weight:bold;font-size:96px;z-index:0" size="3" value="">
	</form>
	<form method='post' action='relogiodeponto.php' enctype='multipart/form-data'>
		Login:<input type='text' name='login'/><br>
		Senha:<input type='password' name='senha'/><br>
		A&ccedil;&atilde;o:<select name='acao'>
			<option value='--'>--</option>
			<option value='entrar'>Entrar</option>
			<option value='sair'>Sair</option>
		</select><br>
		<input type='submit' value='Marcar ponto'/>
	</form>
	</center></td></tr></table>
	</center></font>
	</body>
</html>