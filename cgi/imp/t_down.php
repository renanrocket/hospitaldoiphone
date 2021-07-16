<td colspan="1" style="height: 65px; width: 50%">
	<span>Assinatura Vendedor</span>
	<br>
</td>
<td colspan="1">
	<span>Assinatura Cliente</span>
	<?php

		if(isset($ordem_de_servico_id)){
			$idOS = $ordem_de_servico_id;
		}
		if(isset($idOS)){
			$assinatura = true;
		}else{
			$assinatura = false;
		}

		if($assinatura){
			$criterio = new TCriteria();
			$criterio->add(new TFilter('id', '=', $idOS));	

			$sql = new TSqlSelect();
			$sql->setEntity('ordem_de_servico');
			$sql->addColumn('assinatura');
			$sql->setCriteria($criterio);

			$result = $conn->query($sql->getInstruction());

			if($result->rowCount()){
				extract($result->fetch(PDO::FETCH_ASSOC));

				echo "<img style='max-width: 140px;' src='".$assinatura."'>";
			}
		}
		

	?>
	<br>
</td>