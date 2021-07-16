<?php
	include_once "templateUP2.php";
	
	
	extract($_GET);
	
	if(!isset($id)){
		
		
		echo "<form name='formulario' method='post' action='colaboradorCadastrar2.php' enctype='multipart/form-data'>";
		echo "<input type='hidden' name='op' value='novo'>";
		echo "<table>";
			echo "<tr>";
				echo "<td class='td1'>Nome*</td>";
				echo "<td class='td2'><input type='text' name='nome'></td>";
				echo "<td class='td1'>Login*</td>";
				echo "<td class='td2'><input type='text' name='login' id='login'></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td class='td1'>Fun&ccedil;&atilde;o</td>";
				echo "<td class='td2' colspan='3'>
					1*<select name='funcao1'>
					".opcaoSelect("funcoes", "Ativo", NULL, TRUE)."
					</select> 
					2<select name='funcao2'>
					".opcaoSelect("funcoes", "Ativo", NULL, TRUE)."
					</select> 
					3<select name='funcao3'>
					".opcaoSelect("funcoes", "Ativo", NULL, TRUE)."
					</select> 
				</td>";
			echo "</tr>";
			$data= date('d/m/Y');
			echo "<tr>";
				echo "<td class='td1'>Data de Admiss&atilde;o*</td>";
				echo "<td class='td2'><input type='text' name='admissao' value='$data' size='8' id='admissao' maxlength='10' onKeyDown='Mascara(this,Data);' onKeyPress='Mascara(this,Data);' onKeyUp='Mascara(this,Data);'>
				<input class='btnData' name='btnData1' value='' Onclick=\"javascript:popdate('document.formulario.admissao','pop1','150',document.formulario.admissao.value)\">
				<span id='pop1' style='position:absolute; right:275px;'></span></td>";
				echo "<td class='td1'>Data de Nascimento</td>";
				echo "<td class='td2'><input type='text' name='nascimento' value='' size='8' id='nascimento' maxlength='10' onKeyDown='Mascara(this,Data);' onKeyPress='Mascara(this,Data);' onKeyUp='Mascara(this,Data);'>
				<input class='btnData' name='btnData2' value='' Onclick=\"javascript:popdate('document.formulario.nascimento','pop2','150',document.formulario.nascimento.value)\">
				<span id='pop2' style='position:absolute; right:-50px;'></span></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td class='td1'>Telefone 1*</td>";
				echo "<td class='td2'><input type='text' name='telefone1' id='telefone1' maxlength='14'
				 onKeyDown='Mascara(this,Telefone);' onKeyPress='Mascara(this,Telefone);' 
				 onKeyUp='Mascara(this,Telefone);'></td>";
				echo "<td class='td1'>Telefone 2</td>";
				echo "<td class='td2'><input type='text' name='telefone2' id='telefone1' maxlength='14' 
				onKeyDown='Mascara(this,Telefone);' onKeyPress='Mascara(this,Telefone);' 
				onKeyUp='Mascara(this,Telefone);'></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td class='td1'>E-mail</td>";
				echo "<td class='td2' colspan='3'><input type='text' name='email' size='50' id='email'></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td class='td1'>Endere&ccedil;o</td>";
				echo "<td class='td2' colspan='3'><input type='text' name='endereco' size='50'></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td class='td1'>Complemento</td>";
				echo "<td class='td2'><input type='text' name='complemento'></td>";
				echo "<td class='td1'>Bairro</td>";
				echo "<td class='td2'><input type='text' name='bairro'></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td class='td1'>Municipio</td>";
				echo "<td class='td2'><input type='text' name='municipio'></td>";
				echo "<td class='td1'>Salario Base*</td>";
				echo "<td class='td2'><input size='10' type='text' name='salario'  
				onKeyDown='Mascara(this,Valor);' onKeyPress='Mascara(this,Valor);' onKeyUp='Mascara(this,Valor);'></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td class='td1'>R.G.*</td>";
				echo "<td class='td2'><input type='text' name='rg' 
				onKeyDown='Mascara(this,Integer);' onKeyPress='Mascara(this,Integer);' onKeyUp='Mascara(this,Integer);'></td>";
				echo "<td class='td1'>C.P.F.*</td>";
				echo "<td class='td2'><input type='text' name='cpf' maxlength='14' 
				onKeyDown='Mascara(this,Cpf);' onKeyPress='Mascara(this,Cpf);' onKeyUp='Mascara(this,Cpf);'></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td class='td1'>Carteira de Trabalho</td>";
				echo "<td class='td2'><input type='text' name='carteira' 
				onKeyDown='Mascara(this,Integer);' onKeyPress='Mascara(this,Integer);' onKeyUp='Mascara(this,Integer);'></td>";
				echo "<td class='td1'>Filhos</td>";
				echo "<td class='td2'><input type='text' name='filhos'  
				onKeyDown='Mascara(this,Integer);' onKeyPress='Mascara(this,Integer);' onKeyUp='Mascara(this,Integer);'></td>";
			echo "</tr>";
			
			echo "<tr>";
				echo "<td colspan='3'></td>";
				echo "<td><input type='submit' value='Enviar'></td>";
			echo "</tr>";
			
		echo "</table>";
		
		echo "</form>";
		
		
	}else{
			
		$login = $_COOKIE["login"];
		$sql = mysqli_query($conexao, "select id_funcao, id from usuario where email='$login'");
		$funcao = mysqli_fetch_row($sql);
		$cred = array("1");
		$mach = isFuncao($cred, $funcao);
		if ($mach or $funcao[3]==$id) {
		
			$sql= mysqli_query($conexao, "select * from usuario where id='$id'");
			extract(mysqli_fetch_assoc($sql));
			
			echo "<form name='formulario' method='post' action='colaboradorCadastrar2.php' enctype='multipart/form-data'>";
			echo "<input type='hidden' name='op' value='editar'>";
			echo "<input type='hidden' name='id' value='$id'>";
			echo "<input type='hidden' name='login' value='$email' id='login'>";
			echo "<table>";
				echo "<tr>";
					echo "<td class='td1'>Status</td>";
					echo "<td class='td2' colspan='2'>
					<select name='status'>";
					if ($status=="Ativo"){
						echo "<option value='Ativo' selected>Ativo</option>";
						echo "<option value='Inativo'>Inativo</option>";
					}else{
						echo "<option value='Ativo'>Ativo</option>";
						echo "<option value='Inativo' selected>Inativo</option>";
					}
				echo "</select>
					</td>";
					echo "<td class='td1'><a href='usuarioSenha.php?id=$id'>Alterar senha</a> | <a href='colaboradorTurnoCadastrar.php?id=$id'>Cadastrar Turno</a></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td class='td1'>Nome*</td>";
					echo "<td class='td2'><input type='text' name='nome' value='$nome'></td>";
					echo "<td class='td1'>Login</td>";
					echo "<td class='td2'>$login</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td class='td1'>Fun&ccedil;&atilde;o</td>";
					echo "<td class='td2' colspan='3'>";
						if ($mach){
							echo "1*<select name='funcao1'>
							".opcaoSelect("funcoes", "Ativo", $funcao1, TRUE)."
							</select> 
							2<select name='funcao2'>
							".opcaoSelect("funcoes", "Ativo", $funcao2, TRUE)."
							</select> 
							3<select name='funcao3'>
							".opcaoSelect("funcoes", "Ativo", $funcao3, TRUE)."
							</select>";
						}else{
							echo "$funcao1 $funcao2 $funcao3";
						}
					echo "</td>";
				echo "</tr>";
				$data= date('d/m/Y');
				echo "<tr>";
					echo "<td class='td1'>Data de Admiss&atilde;o*</td>";
					echo "<td class='td2'><input type='text' name='admissao' value='".formataData($data_admissao)."' size='8' id='admissao' maxlength='10' onKeyDown='Mascara(this,Data);' onKeyPress='Mascara(this,Data);' onKeyUp='Mascara(this,Data);'>
					<input class='btnData' name='btnData1' value='' Onclick=\"javascript:popdate('document.formulario.admissao','pop1','150',document.formulario.admissao.value)\">
					<span id='pop1' style='position:absolute; right:275px;'></span></td>";
					echo "<td class='td1'>Data de Nascimento</td>";
					echo "<td class='td2'><input type='text' name='nascimento' value='".formataData($data_nascimento)."' size='8' id='nascimento' maxlength='10' onKeyDown='Mascara(this,Data);' onKeyPress='Mascara(this,Data);' onKeyUp='Mascara(this,Data);'>
					<input class='btnData' name='btnData2' value='' Onclick=\"javascript:popdate('document.formulario.nascimento','pop2','150',document.formulario.nascimento.value)\">
					<span id='pop2' style='position:absolute; right:-50px;'></span></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td class='td1'>Telefone 1*</td>";
					echo "<td class='td2'><input type='text' name='telefone1' id='telefone1' maxlength='14' value='$telefone1' 
					 onKeyDown='Mascara(this,Telefone);' onKeyPress='Mascara(this,Telefone);' 
					 onKeyUp='Mascara(this,Telefone);'></td>";
					echo "<td class='td1'>Telefone 2</td>";
					echo "<td class='td2'><input type='text' name='telefone2' id='telefone1' maxlength='14' value='$telefone2' 
					onKeyDown='Mascara(this,Telefone);' onKeyPress='Mascara(this,Telefone);' 
					onKeyUp='Mascara(this,Telefone);'></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td class='td1'>E-mail</td>";
					echo "<td class='td2' colspan='3'><input type='text' name='email' size='50' value='$email' id='email'></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td class='td1'>Endere&ccedil;o</td>";
					echo "<td class='td2' colspan='3'><input type='text' name='endereco' size='50'  value='$endereco'></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td class='td1'>Complemento</td>";
					echo "<td class='td2'><input type='text' name='complemento' value='$complemento'></td>";
					echo "<td class='td1'>Bairro</td>";
					echo "<td class='td2'><input type='text' name='bairro'  value='$bairro'></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td class='td1'>Municipio</td>";
					echo "<td class='td2'><input type='text' name='municipio' value='$municipio'></td>";
					echo "<td class='td1'>Salario Base*</td>";
					echo "<td class='td2'><input size='10' type='text' name='salario' value='$salario_base' 
					onKeyDown='Mascara(this,Valor);' onKeyPress='Mascara(this,Valor);' onKeyUp='Mascara(this,Valor);'></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td class='td1'>R.G.*</td>";
					echo "<td class='td2'><input type='text' name='rg' value='$rg' 
					onKeyDown='Mascara(this,Integer);' onKeyPress='Mascara(this,Integer);' onKeyUp='Mascara(this,Integer);'></td>";
					echo "<td class='td1'>C.P.F.*</td>";
					echo "<td class='td2'><input type='text' name='cpf' maxlength='14' value='$cpf' 
					onKeyDown='Mascara(this,Cpf);' onKeyPress='Mascara(this,Cpf);' onKeyUp='Mascara(this,Cpf);'></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td class='td1'>Carteira de Trabalho</td>";
					echo "<td class='td2'><input type='text' name='carteira' value='$carteira' 
					onKeyDown='Mascara(this,Integer);' onKeyPress='Mascara(this,Integer);' onKeyUp='Mascara(this,Integer);'></td>";
					echo "<td class='td1'>Filhos</td>";
					echo "<td class='td2'><input type='text' name='filhos' value='$filhos'  
					onKeyDown='Mascara(this,Integer);' onKeyPress='Mascara(this,Integer);' onKeyUp='Mascara(this,Integer);'></td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td colspan='3'></td>";
					echo "<td ><input type='submit' value='Enviar'></td>";
				echo "</tr>";
				
			echo "</table>";
			
			echo "</form>";
			
		}
		
		
	}
	
	
	
	include_once "templateDOWN2.php";
?>