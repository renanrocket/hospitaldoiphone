<?php
	setcookie("login","");
	setcookie("senha","");
	setcookie("acao","");
	setcookie("turno","");
	
	include "templateUP.php";

		echo "<div class='divTopRelogio'>";
		echo "<form name='clock' method='post' action='saves/relogiodeponto.php' enctype='multipart/form-data'>";
		echo "<img class='imgRelogio' src='imagens/relogio.png'>";
		echo "<input type='text' name='face' style='width:1000px;' value='' class='relogio'>";
		echo "</div>";
		
		echo "<div class='divMidRelogio'><br>";
			echo "<p><input id='login' type='text' name='login' value='Login' size='10' 
			onfocus=\"if (this.value == 'Login') {this.value = '';}\" 
			onblur=\"if (this.value == '') {this.value = 'Login';}\" class='inputRelogio'></p>";
			echo "<p><input id='senha' type='password' name='senha' value='Senha' size='10' 
			onfocus=\"if (this.value == 'Senha') {this.value = '';}\" 
			onblur=\"if (this.value == '') {this.value = 'Senha';}\" class='inputRelogio'></p>";
			echo "<p><input id='marcar' type='submit' value='Marcar Ponto' class='inputBotaoRelogio'></p>";
		echo "</div>";
		
		echo "</form>";
		
		echo "
			<div class='loginAdmin'>
				Conta
				<div class='up'>
					<form method='post' action='admin.php' enctype='multipart/form-data'>
					<p><input id='login' type='text' name='login' value='Login' size='10' 
					onfocus=\"if (this.value == 'Login') {this.value = '';}\" 
					onblur=\"if (this.value == '') {this.value = 'Login';}\" class='inputRelogio'></p>
					<p><input id='senha' type='password' name='senha' value='Senha' size='10' 
					onfocus=\"if (this.value == 'Senha') {this.value = '';}\" 
					onblur=\"if (this.value == '') {this.value = 'Senha';}\" class='inputRelogio'></p>
					<p><input id='marcar' type='submit' value='Logar' class='inputBotaoRelogio'></p>
					</form>
				</div>
			</div>
		";


	include "templateDOWN.php";
?>