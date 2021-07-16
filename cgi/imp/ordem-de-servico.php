<?php

include '../inc/functions.inc.php';

$conn = TConnection::open(DB);

extract($_GET);
if(!is_numeric($id))
	$id = base64_decode($id);

$idOS = $id;

$criterio = new TCriteria();
$criterio->add(new TFilter('id', '=', $id));

$sql = new TSqlSelect();
$sql->setEntity('ordem_de_servico');
$sql->addColumn('*');
$sql->setCriteria($criterio);
$result = $conn->query($sql->getInstruction());

if($result->rowCount()){
	extract($result->fetch(PDO::FETCH_ASSOC));
	
}



?>

<!DOCTYPE html>
<html>
<head>
	<title>Ordem de serviço <?php echo $id; ?></title>
	<!-- style pessoal editado -->
	<link rel="stylesheet" href="../css/cssImp.css">
	<!-- Font Awesome -->
	  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
	  <style type="text/css">
	  	.fa{
	  		top: unset;
		    color: unset; 
		    padding-left: unset; 
		    display: unset; 
		    text-transform: none; 
		    background-color: unset; 
		    border-bottom: unset; 
		    margin-bottom: unset;
	  	}
	  	.fa-check{
	  		color: green;
	  	}
	  	.fa-close{
	  		color: red;
	  	}
	  </style>
</head>
<!--<body onload="javascript:self.print()">-->
<body>
	<table id='tabelaImp' style='width: 820px;'>
		<tr>
			<td colspan="2">
				<?php include 't_up.php'; ?>
			</td>
		</tr>
		<tr>
			<th colspan="2" style="vertical-align: middle; font-size: 20px;">Ordem de serviço <?php echo $id; ?></th>
		</tr>
		<tr>
			<td colspan="2">
				<span>Cliente</span>
				<table style="width: 100%;">
					<tr>
						<td class='orcSta orcTop' style='min-width:200px;'>Nome</td>
						<td class='orcMid orcTop' style='min-width:200px;'>Email</td>
						<td class='orcMid orcTop' style='min-width:100px;'>Whats app</td>
						<td class='orcEnd orcTop' style='min-width:100px;'>Telefone</td>
					</tr>
					<tr>
						<td class=''><?php echo $cliente_id.' '.$nome; ?></td>
						<td class=''><?php echo $email; ?></td>
						<td class=''><?php echo $wpp; ?></td>
						<td class=''><?php echo $telefone; ?></td>
					</tr>
					<?php if($instagram): ?>
					<tr>
						<td class='orcMid orcTop' colspan='4'>Instagram</td>
					</tr>
					<tr>
						<td class='' colspan='4'><?php echo $instagram; ?></td>
					</tr>
					<?php endif; ?>
				</table>
				
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<span>Serviço</span>
				<table style="width: 100%;">
					<tr>
						<td class='orcMid orcTop' style='min-width:300px;' colspan='2'>Nome</td>
						<td class='orcEnd orcTop' style='min-width:50px;' colspan='2'>SubTotal</td>
					</tr>
					<tr>
						<td class='' colspan='2'><?php echo $servico; ?></td>
						<td class='' colspan='2'>R$ <?php echo real($preco, 'real'); ?></td>
					</tr>
					<tr>
						<td class='orcSta' colspan='2'></td>
						<td class='tdClara'>Total</td>
						<td class='tdClara'>R$ <?php echo real($preco, 'real'); ?></td>
					</tr>

				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<span>Informações para produção do serviço</span>
				<table style="width: 100%;">
					<tr>
						<td class='tdClara'>Data de Abertura</td>
						<td class='tdClara' colspan="2">Data de finalização</td>
						<td class='tdClara'>Data do evento</td>
					</tr>
					<tr>
						<td class=''><?php echo formataData($data_de_abertura); ?></td>
						<td class='' colspan="2"><?php echo formataData($data_de_finalizacao); ?></td>
						<td class=''><?php echo formataData($data_do_evento).' '.$hora_do_evento; ?></td>
					</tr>
					<tr>
						<td class='tdClara'>Status</td>
						<td class='tdClara' colspan="2">Imei ou numero de série</td>
						<td class='tdClara'>Técnico responsável</td>
					</tr>
					<tr>
						<td class='' style="font-weight: bold;"><?php echo registro($status, 'ordem_de_servico_status', 'nome'); ?></td>						
						<td class='' colspan="2"><?php echo $imei_ou_n_de_serie; ?></td>
						<td class=''><?php echo $tecnico_responsavel; ?></td>
					</tr>
					<tr>
						<td class='tdEscura' colspan="4" style="text-align: center;">Check List</td>
					</tr>

<?php
	
	$criterio = new TCriteria();
	$criterio->add(new TFilter('status', '=', 1));

	$sql = new TSqlSelect();
	$sql->setEntity('ordem_de_servico_perguntas');
	$sql->addColumn('pergunta');
	$sql->setCriteria($criterio);

	$result = $conn->query($sql->getInstruction());

	$cod = '<tr>';
	for($i=1; $i<=$result->rowCount(); $i++){

		extract($result->fetch(PDO::FETCH_ASSOC));


		$criterio_p = new TCriteria();
		$criterio_p->add(new TFilter('tipo', '=', strtolower(strtr($pergunta, unserialize(CHAR_MAP)))));

		$sql_p = new TSqlSelect();
		$sql_p->setEntity('ordem_de_servico_atributos');
		$sql_p->addColumn('valor as resposta');
		$sql_p->setCriteria($criterio_p);
		
		$result_p = $conn->query($sql_p->getInstruction());

		$cod .= '<td class="tdClara" colspan="">'.$pergunta.'</td>';
		if($result_p->rowCount()){
			extract($result_p->fetch(PDO::FETCH_ASSOC));
			$resposta == 0 ? $resposta = 'Não <span class="fa fa-close"></span>' : $resposta = 'Sim <span class="fa fa-check"></span>';
			$cod .= '<td class="" colspan="">'.$resposta.'</td>';	
		}else{
			$cod .= '<td class="" colspan="">Não</td>';
		}

		if($i%2==0){
			$cod .= '</tr>';
			
			if($i+1<$result->rowCount())
				$cod .= '<tr>';
		}


	}

	echo $cod;

?>

					<tr>
						<td class='tdClara' colspan="4" style="text-align: center;">Imagens</td>
					</tr>
					<tr>
						<td class='' colspan="4" >
							
<?php
	
	$criterio = new TCriteria();
	$criterio->add(new TFilter('ordem_de_servico_id', '=', $id));
	$criterio->add(new TFilter('tipo', '=', 'fotos_do_celular'));

	$sql = new TSqlSelect();
	$sql->setEntity('ordem_de_servico_atributos');
	$sql->addColumn('valor as img');
	$sql->setCriteria($criterio);

	$result = $conn->query($sql->getInstruction());
	$cod = null;
	for($i=1; $i<=$result->rowCount(); $i++){

		extract($result->fetch(PDO::FETCH_ASSOC));

		$cod .= '<a href="../'.$img.'" target="_blank"><img style="max-width:100px; max-height:100px;" src="../'.$img.'"></a>';


	}

	echo $cod;

?>

						</td>
					</tr>
					<tr>
						<td class='tdClara' colspan="4">Observações</td>
					</tr>
					<tr>
						<td class='' colspan="4"><?php echo $observacoes; ?></td>
					</tr>
					<tr>
						<td class='tdEscura' colspan="3">Serviço</td>
						<td class='tdEscura' colspan="">Preço</td>
					</tr>
					<tr>
						<td class='' colspan="3"><?php echo $servico; ?></td>
						<td class='' colspan="">R$ <?php echo real($preco, 'real'); ?></td>
					</tr>
					
				</table>
			</td>
		</tr>
		<tr>
			<?php include 't_down.php'; ?>
		</tr>
	</table>
</body>
</html>