<?php
echo "Sentimos muito, mas n&atilde;o foi possivel completar essa operação.<br>";
if(mysqli_error($conexao)){
	echo "Motivo: <b style='color:red;'>" . mysqli_error($conexao) . "</b><br>";
}
echo "Tente mais tarde ou contate o desenvolvedor do software:<br>";
echo "falecom@rocketsolution.com.br";
?>