<?php
include "templateUP2.php";

function formulario($funcao = null, $descricao = null, $id = null, $op = null) {
	echo "<form method='get' action='funcaoCadastrar.php' enctype='multipart/form-data'>";
	if($op){
		echo "<input type='hidden' name='op' value='editar'>";
		echo "<input type='hidden' name='id' value='$id'>";
	}else{
		echo "<input type='hidden' name='op' value='novo'>";
	}
	echo "Nome da Função:<br>";
	echo "<input type='text' value='$funcao' name='funcao'><br>";
	echo "Uma breve descrição<br>";
	echo "<input type='text' value='$descricao' name='descricao'><br>";
	echo "<input type='submit' value='Enviar'>";
	echo "</form>";
}

if (!$_GET) {

	formulario();

	echo "<br><br><br>";

	echo "<table id='formfast'>";
	echo "<tr>";
	echo "<td>Id</td>";
	echo "<td>Nome</td>";
	echo "<td>Função</td>";
	echo "<td></td>";
	echo "</tr>";

	$instrucao = "select * from funcoes order by status";
	$sql = mysqli_query($instrucao);
	$linha = mysqli_num_rows($sql);
	for ($i = 0; $i < $linha; $i++) {
		extract(mysqli_fetch_assoc($sql));

		echo "<tr>";

		echo "<td>";
		echo "<form method='get' action='funcaoCadastrar.php' enctype='multipart/form-data'>";
		echo "<input type='hidden' name='op' value='visualizar'>";
		echo "<input type='hidden' name='id' value='$id'>";
		echo "<input type='submit' value='$id'>";
		echo "</form>";
		echo "</td>";

		echo "<td>$nome</td>";
		echo "<td>$descricao</td>";

		if ($status == "Ativo") {
			echo "<td>";
			echo "<form method='get' action='funcaoCadastrar.php' enctype='multipart/form-data'>";
			echo "<input type='hidden' name='op' value='deletar'>";
			echo "<input type='hidden' name='id' value='$id'>";
			echo "<input type='submit' value='X' id='deletar'>";
			echo "</form>";
			echo "</td>";
		} else {
			echo "<td>";
			echo "<form method='get' action='funcaoCadastrar.php' enctype='multipart/form-data'>";
			echo "<input type='hidden' name='op' value='ativar'>";
			echo "<input type='hidden' name='id' value='$id'>";
			echo "<input type='submit' value='Ativar'>";
			echo "</form>";
			echo "</td>";
		}

		echo "</tr>";

	}

	echo "</table>";

} else {
	extract($_GET);

	if ($op == "novo") {

		$valida = true;
		if (empty($funcao)) {
			echo "Para inserir uma nova função, você deve antes dar um nome para ela.<br>";
			$valida = false;
		}

		if ($valida) {

			$instrucao = "insert into funcoes (nome, descricao) values ('$funcao', '$descricao')";
			$sql = mysqli_query($instrucao);

			confirmacaoDB("Operação realizada com sucesso.", "funcaoCadastrar.php");

		} else {
			echo irAuto("funcaoCadastrar.php", "2");
		}
	} elseif ($op == "deletar") {

		$instrucao = "update funcoes set status='Inativo' where id='$id'";
		$sql = mysqli_query($instrucao);

		confirmacaoDB("Operação realizada com sucesso.", "funcaoCadastrar.php");

	} elseif ($op == "ativar") {

		$instrucao = "update funcoes set status='Ativo' where id='$id'";
		$sql = mysqli_query($instrucao);

		confirmacaoDB("Operação realizada com sucesso.", "funcaoCadastrar.php");

	} elseif ($op == "visualizar") {

		$instrucao = "select * from funcoes where id='$id'";
		$sql = mysqli_query($instrucao);
		extract(mysqli_fetch_assoc($sql));
		
		formulario($nome, $descricao, $id, true);
		
	} elseif ($op == "editar") {

		$valida = true;
		if (empty($funcao)) {
			echo "Para editar uma nova função, você deve antes dar um nome para ela.<br>";
			$valida = false;
		}

		if ($valida) {

			$instrucao = "update funcoes set nome='$funcao', descricao='$descricao' where id='$id'";
			$sql = mysqli_query($instrucao);

			confirmacaoDB("Operação realizada com sucesso.", "funcaoCadastrar.php");

		} else {
			echo irAuto("funcaoCadastrar.php", "2");
		}
		
	}

}

include "templateDOWN2.php";
?>