<?php

if(!isset($filial)){
	$filial = NOME_EMPRESA_SLOGAN;
}

if(!isset($endereco)){
	$endereco = ENDERECO_EMPRESA;
}

if(!isset($telefone_1)){
	$telefone_1 = TELEFONE_EMPRESA;
}else{
	$telefone_1 = $telefone_1.' '.$telefone_2;
}

?>

<img src="<?php echo IMG_EMPRESA ?>" style="max-width: 150px; float:left; margin-left:15px;margin-top:15px;">
<div class="cbNome" style="text-align: center">
	<?php echo NOME_EMPRESA ?>
</div>
<div class="cbSlogan" style="text-align: center">
	<?php echo $filial ?>
</div>
<!--
<div class="cbText" style="text-align: center">
	<?php echo CNPJ_EMPRESA ?>
</div>
-->
<div class="cbText" style="text-align: center">
	<?php echo $endereco ?>
</div>
<div class="cbTelefone" style="text-align: center">
	<?php echo $telefone_1 ?>
</div>