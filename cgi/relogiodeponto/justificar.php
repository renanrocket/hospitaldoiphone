<?php
	include "templateUP2.php";
	
	
	echo "<form method='post' action='justificar2.php' enctype='multipart/form-data'>";
			echo "<table>";
			echo "<tr>";
				echo "<td>Selecione o colaborador:</td>";
				echo "<td><select name='id'>";
				echo "<option value=''>--</option>";
				$sql= mysqli_query("select id, nome, funcao, login from usuario where status='Ativo' and funcao<>'admin' order by nome");
				while($reg = mysqli_fetch_assoc($sql)){
					extract($reg);
					echo "<option value='$id'>$nome [$funcao] - $login</option>";
				}
				echo "</select></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Justificativa:</td>";
				echo "<td>";
					echo "<select name='justificativa'>";
					echo "<option value=''>--</option>";
						echo "<option value='Atrazo'>Atrazo</option>";
						echo "<option value='Saida antes do horario'>Sa&iacute;da antes do hor&aacute;rio</option>";
						echo "<option value='Falta justificada'>Falta justificada</option>";
						echo "<option value='Feriado'>Feriado</option>";
						echo "<option value='Licenca'>Licen&ccedila</option>";
					echo "</select>";
				echo"</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Per&iacute;odo:</td>";
				echo "<td>";
					echo "<select name='dia'>";
					for ($i=1; $i<32; $i++){
						echo "<option value='$i'>$i</option>";
					}
					echo "</select>";
					echo " de ";
					echo "<select name='mes'>";
					echo "<option value='1'>Janeiro</option>";
					echo "<option value='2'>Fevereiro</option>";
					echo "<option value='3'>Mar&ccedil;o</option>";
					echo "<option value='4'>Abril</option>";
					echo "<option value='5'>Maio</option>";
					echo "<option value='6'>Junho</option>";
					echo "<option value='7'>Julho</option>";
					echo "<option value='8'>Agosto</option>";
					echo "<option value='9'>Setembro</option>";
					echo "<option value='10'>Outubro</option>";
					echo "<option value='11'>Novembro</option>";
					echo "<option value='12'>Dezembro</option>";
					echo "</select>";
					echo " de ";
					echo "<select name='ano'>";
					for ($i=2012; $i<2017; $i++){
						echo "<option value='$i'>$i</option>";
					}
					echo "</select>";
				echo"</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Turno:</td>";
				echo "<td>";
					echo "<select name='turno'>";
						echo "<option value=''>--</option>";
						echo "<option value='t1'>Truno 1</option>";
						echo "<option value='t2'>Turno 2</option>";
						echo "<option value='t1t2'>Turno 1 e turno 2</option>";
					echo "</select>";
				echo"</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Descri&ccedil;&atilde;o:</td>";
				echo "<td><textarea name='descricao' cols='50' rows='7'></textarea></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td></td>";
				echo "<td><input type='submit' value='Enviar'></td>";
			echo "</tr>";
			echo "</table>";
			
	echo "</form>";
	
	
	
	
	
	include "templateDOWN2.php";
?>