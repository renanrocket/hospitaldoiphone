<?php

$instrucao = "select * from turno_padrao where status='Ativo'";
$sql = mysqli_query($instrucao);
$linha = mysqli_num_rows($sql);

echo "<table id='formFast'>";
	
	echo "<tr>";
		echo "<td>ID</td>";
		echo "<td>Nome do Turno</td>";
		echo "<td></td>";
	echo "</tr>";
	
	for ($i = 0; $i < $linha; $i++) {
		
		extract(mysqli_fetch_assoc($sql));
		
		echo "<tr>";
			echo "<td>";
				echo "<form method='get' action='colaboradorTurnoCadastrar2.php' enctype='multipart/form-data'>";
				echo "<input type='hidden' name='op' value='visualizar'>";
				echo "<input type='hidden' name='id' value='$id'>";
				echo "<input type='submit' value='$id'>";
				echo "</form>";
			echo "</td>";
			
			echo "<td>$nome</td>";
			
			echo "<td>";
				echo "<form method='get' action='colaboradorTurnoCadastrar2.php' enctype='multipart/form-data'>";
				echo "<input type='hidden' name='op' value='deletar'>";
				echo "<input type='hidden' name='id' value='$id'>";
				echo "<input type='submit' value='X' id='deletar'>";
				echo "</form>";
			echo "</td>";
		echo "</tr>";
		
	}

echo "</table>";
?>