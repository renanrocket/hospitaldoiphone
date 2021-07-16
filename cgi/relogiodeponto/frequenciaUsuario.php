<?php
	include "templateUP2.php";
	
		
		
		
		
		
		echo "<form method='post' action='frequenciaUsuario2.php' enctype='multipart/form-data'>";
			echo "Selecione o colaborador:";
			echo "<select name='id'>";
			echo "<option value=''>--</option>";
			$sql= mysqli_query($conexao, "select id, nome, id_funcao, nome, email from usuario where status='1' and id<>1 order by nome");
			while($reg = mysqli_fetch_assoc($sql)){
				extract($reg);
				echo "<option value='$id'>$nome [".registro($id_funcao,'usuario_funcao','nome')."] - $email</option>";
			}
			echo "</select><br>";
			echo "Período:";
			echo "<select name='mes'>";
			$meses = array(
				1=>'Janeiro',
				2=>'Fevereiro',
				3=>'Março',
				4=>'Abril',
				5=>'Maio',
				6=>'Junho',
				7=>'Julho',
				8=>'Agosto',
				9=>'Setembro',
				10=>'Outubro',
				11=>'Novembro',
				12=>'Dezembro');

				for($i=1; $i<count($meses); $i++){
					if(date('n')==$i){
						echo "<option value='".$i."' selected='yes'>".$meses[$i]."</option>";
					}else{
						echo "<option value='".$i."'>".$meses[$i]."</option>";
					}
				}
			
			echo "</select>";
			echo " de ";
			echo "<select name='ano'>";
			for ($i=2019; $i<2030; $i++){
				if(date('Y')==$i){
					echo "<option value='$i' selected='yes'>$i</option>";
				}else{
					echo "<option value='$i'>$i</option>";
				}
				
			}
			echo "</select><br><br>";
			echo "<input type='submit' value='Enviar'>";
		echo "</form>";
		
		
		
		
		
	include "templateDOWN2.php";
?>