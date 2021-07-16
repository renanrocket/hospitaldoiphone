<?php

 echo "<ul id='menu'>";
        
    //menu rapido
    $login = $_COOKIE["login"];
	$sql = mysqli_query($conexao, "select id_funcao, id from usuario where email='$login'");
	$funcao = mysqli_fetch_row($sql);
	$cred = array("1");
	$mach = isFuncao($cred, $funcao);
	if ($mach) {
		echo "<li><a href='#'>Cadastro</a>";
	        echo "<div class='dropdown_3columns'>";
				echo "<div class='col_1'>";
					echo "<h3>Frequência</h3>";
	                echo "<ul>";
	                        echo "<li><a href='frequenciaCadastrar.php'>Cadastrar</a></li>";
	                        echo "<li><a href='frequenciaVisualizar.php'>Visualizar</a></li>";
	                    echo "</ul>";
	            echo "</div>";
				echo "<div class='col_1'>";
					echo "<h3>Função</h3>";
	                echo "<ul>";
	                        echo "<li><a href='funcaoCadastrar.php'>Cadastrar</a></li>";
	                    echo "</ul>";
	            echo "</div>";
				 echo "<div class='col_1'>";
					echo "<h3>Colaborador</h3>";
	                echo "<ul>";
	                        echo "<li><a href='../cadastrar-usuario.php'>Cadastrar</a></li>";
	                        echo "<li><a href='../pesquisar-usuario.php'>Buscar</a></li>";
	                    echo "</ul>";
	            echo "</div>";
	        echo "</div>";
	    echo "</li>";
		echo "<li><a href='#'>Frequência</a>";
	        echo "<div class='dropdown_1column'>";
	            echo "<div class='col_1'>";
	                echo "<ul class='greybox'>";
	                        echo "<li><a href='frequenciaUsuario.php'>Visualizar</a></li>";
	                        echo "<li><a href='justificar.php'>Justificar</a></li>";
	                    echo "</ul>";
	            echo "</div>";
	        echo "</div>";
	    echo "</li>";
    }
	
	$sql= mysqli_query($conexao, "select * from usuario where email='".$_COOKIE["login"]."' and senha='".md5($_COOKIE["senha"])."'");
	$reg= mysqli_fetch_row($sql);
	
	echo "<li class='menu_right'>
		<a href='#'>Conta $reg[3]</a>";
		 echo "<div class='dropdown_1column'>";
            echo "<div class='col_1'>";
                echo "<ul class='greybox'>";
                        echo "<li><a href='colaboradorCadastrar.php?id=$reg[0]'>Editar</a></li>";
                        echo "<li><a href='logout.php'>Sair</a></li>";
                    echo "</ul>";
            echo "</div>";
        echo "</div>";
	echo "</li>";
	
echo "</ul>";


?>