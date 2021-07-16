<?php

include '../inc/functions.inc.php';

$conn = TConnection::open(DB);

extract($_GET);
if(!is_numeric($id))
	$id = base64_decode($id);

$criterio = new TCriteria();
$criterio->add(new TFilter('id', '=', $id));

$sql = new TSqlSelect();
$sql->setEntity('venda');
$sql->addColumn('*');
$sql->setCriteria($criterio);
$result = $conn->query($sql->getInstruction());

if($result->rowCount()){
	extract($result->fetch(PDO::FETCH_ASSOC));
	
}

$status == 1 ? $tipo = 'Pedido' : $tipo = 'Orçamento';

?>

<!DOCTYPE html>
<html>
<head>
	<title><?php echo $tipo.' '.$id; ?></title>
	<!-- style pessoal editado -->
	<link rel="stylesheet" href="../css/cssImp.css">
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
			<th colspan="2" style="vertical-align: middle; font-size: 20px;"><?php echo $tipo.' '.$id; ?></th>
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
				<span>Itens</span>
				<table style="width: 100%;">
					<?php
						$criterio = new TCriteria();
						$criterio->add(new TFilter('venda_id', '=', $id));

						$sql = new TSqlSelect();
						$sql->setEntity('venda_itens');
						$sql->addColumn('servico_produto');
						$sql->addColumn('item_id');
						$sql->addColumn('item');
						$sql->addColumn('observacao');
						$sql->addColumn('garantia');
						$sql->addColumn('quantidade');
						$sql->addColumn('preco');
						$sql->addColumn('desconto');
						$sql->addColumn('total as item_total');
						$sql->setCriteria($criterio);

						$result = $conn->query($sql->getInstruction());

						for($i=0; $i<$result->rowCount(); $i++):
							extract($result->fetch(PDO::FETCH_ASSOC));
							if($i % 2 == 0){
								$class= 'tdClara';
							}else{
								$class= '';
							}
					?>
					<tr>
						<td class='<?php echo $class; ?>' style='min-width:20px;'>TIPO</td>
						<td class='<?php echo $class; ?>' style='min-width:20px;'>COD</td>
						<td class='<?php echo $class; ?>' style='min-width:300px;'>PRODUTO</td>
						<td class='<?php echo $class; ?>' style='min-width:50px;'>QUANTIDADE</td>
						<td class='<?php echo $class; ?>' style='min-width:50px;'>PRECO</td>
					</tr>
					<tr>
						<td class='<?php echo $class; ?>'><?php 
							if($servico_produto){
								//echo '<a class="aSubmit" href="ordem-de-servico.php?id='.base64_encode($ordem_de_servico_id).'">'.$ordem_de_servico_id.'</a>';
								echo 'Serviço';
							}else{
								echo 'Produto';
							}
						?></td>
						
						<td class='<?php echo $class; ?>'><?php 
							if($item_id){
								echo '<a class="aSubmit" href="../../single-product.php?p='.base64_encode($item_id).'">'.$item_id.'</a>';
							}
						?></td>
						<td class='<?php echo $class; ?>'><?php echo $item; ?></td>
						<td class='<?php echo $class; ?>'><?php echo $quantidade; ?></td>
						<td class='<?php echo $class; ?>'>R$ <?php echo real($preco, 'real'); ?></td>
					</tr>
					<tr>
						<td class='<?php echo $class; ?>' colspan='2'>GARANTIA</td>
						<td class='<?php echo $class; ?>' colspan='1'>OBSERVAÇÕES</td>
						<td class='<?php echo $class; ?>'>DESCONTO %</td>
						<td class='tdEscura'>SUB TOTAL</td>
					</tr>
					<tr>
						<td class='<?php echo $class; ?>' colspan='2'><?php echo $garantia; echo is_numeric($garantia) ? ' meses': false; ?></td>
						<td class='<?php echo $class; ?>' colspan='1'><?php echo $observacao; ?></td>
						<td class='<?php echo $class; ?>'><?php echo real($desconto, 'real'); ?></td>
						<td class='tdEscura'><?php echo real($item_total, 'real'); ?></td>
					</tr>
						<?php endfor; ?>
					<tr>
						<td class='orcSta' colspan='3'></td>
						<td class='tdEscuraBlaster'>Total</td>
						<td class='tdEscuraBlaster'>R$ <?php echo real($total, 'real'); ?></td>
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