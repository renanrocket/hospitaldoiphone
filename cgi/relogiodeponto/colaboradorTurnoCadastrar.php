<script type='text/javascript'>
	function showForm(opcao) {
		if (opcao == "Programar turno") {
			document.getElementById('formularioPre').style.display = "";
			document.getElementById('formularioPos').style.display = "none";
		} else if (opcao == "Usar um turno pre-programado") {
			document.getElementById('formularioPre').style.display = "none";
			document.getElementById('formularioPos').style.display = "";
		} else if (opcao == "--") {
			document.getElementById('formularioPre').style.display = "none";
			document.getElementById('formularioPos').style.display = "none";
		}
	}
</script>
<?php
include "templateUP2.php";

extract($_GET);

echo "Selecione se você deseja: ";
echo "<select name='selectTurno' id='selectTurno' onchange=\"showForm(this.value)\">";
echo "<option value='--'>--</option>";
echo "<option value='Programar turno'>Programar turno</option>";
echo "<option value='Usar um turno pre-programado'>Usar um turno pre-programado</option>";
echo "</select><br><br><br>";

echo "<div id='formularioPre' style='display:none'>";
$sql= mysqli_query("select nome from usuario where id='$id'");
extract(mysqli_fetch_assoc($sql));
formularioTurno("novousuario", "$id", "$nome");
echo "</div>";

echo "<div id='formularioPos' style='display:none'>";

echo "<form method='get' action='colaboradorTurnoCadastrar2.php' enctype='multipart/form-data'>";
echo "<input type='hidden' name='id' value='$id'>";

echo "<table id='formFast'>";

echo "<tr>";
echo "<td></td>";
echo "<td>ID</td>";
echo "<td>Nome do Turno</td>";
echo "</tr>";

$sql = mysqli_query("select * from turno_padrao where status='Ativo'");
$linha = mysqli_num_rows($sql);


for ($i = 0; $i < $linha; $i++) {
	extract(mysqli_fetch_assoc($sql));
	echo "<tr>";
	echo "<td><input type='radio' name='turno' value='$id'></td>";
	echo "<td>$id</td>";
	echo "<td>$nome</td>";
	echo "</tr>";
}

echo "</table>";

echo "<br><br><br>";

echo "<input type='submit' value='Enviar'>";

echo "</form>";

echo "</div>";

include "templateDOWN2.php";
?>