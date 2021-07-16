<?php

include '../inc/functions.inc.php';

$conn = TConnection::open(DB);

extract($_GET);
$idContaSub = $id;
if(!is_numeric($idContaSub))
	$idContaSub = base64_decode($idContaSub);

$criterio = new TCriteria();
$criterio->add(new TFilter('id', '=', $idContaSub));

$sql = new TSqlSelect();
$sql->setEntity('conta_itens');
$sql->addColumn('*');
$sql->setCriteria($criterio);
$result = $conn->query($sql->getInstruction());

if($result->rowCount()){
	extract($result->fetch(PDO::FETCH_ASSOC));

	$idConta = $conta_id;

	$criterio = new TCriteria();
	$criterio->add(new TFilter('id', '=', $idConta));

	$sql = new TSqlSelect();
	$sql->setEntity('conta');
	$sql->addColumn('recebemos_pagamos');
	$sql->addColumn('tabela_referente');
	$sql->addColumn('referido');
	$sql->addColumn('valor as total');
	$sql->addColumn('ja_pago');
	$sql->addColumn('filial_id');
	$sql->setCriteria($criterio);
	$result = $conn->query($sql->getInstruction());

	extract($result->fetch(PDO::FETCH_ASSOC));


	if(is_numeric($referido)){

		if($tabela_referente=='ordem_de_servico'){

			$criterio = new TCriteria();
			$criterio->add(new TFilter('id', '=', $referido));

			$sql = new TSqlSelect();
			$sql->setEntity($tabela_referente);
			$sql->addColumn('produto');
			$sql->addColumn('quantidade');
			$sql->addColumn('desconto_em_reais');
			$sql->setCriteria($criterio);
			$result = $conn->query($sql->getInstruction());

			if($result->rowCount()){
				extract($result->fetch(PDO::FETCH_ASSOC));
				$referido_recibo = $produto.' ('.$quantidade.')';
				$desconto = $desconto_em_reais;
			}else{
				$referido_recibo = $referido;
				$desconto = 0;
			}

		}elseif($tabela_referente=='venda'){
			
			$criterio = new TCriteria();
			$criterio->add(new TFilter('venda_id', '=', $referido));

			$sql = new TSqlSelect();
			$sql->setEntity('venda_itens');
			$sql->addColumn('item');
			$sql->addColumn('quantidade');
			$sql->addColumn('preco');
			$sql->addColumn('total as total_item');
			$sql->setCriteria($criterio);
			$result = $conn->query($sql->getInstruction());

			if($result->rowCount()){
				$referido_recibo = '';
				$desconto = 0;
				for($i=0; $i<$result->rowCount(); $i++){
					extract($result->fetch(PDO::FETCH_ASSOC));
					if($i>0)
						$referido_recibo .= ', ';
					$referido_recibo .= $item.' ('.$quantidade.')';
					$desconto += $total_item - $preco;
				}
			}else{
				$referido_recibo = $referido;
				$desconto = 0;
			}

		}


	}else{
		$referido_recibo = $referido;
		$desconto = 0;
	}
	
}else{
	echo 'Recibo não encontrado';
	die;
}

$data = explode('-', $data_pagamento);

switch ($data[1]) {
	case "01" :
		$data[1] = "Janeiro";
		break;
	case "02" :
		$data[1] = "Fevereiro";
		break;
	case "03" :
		$data[1] = "Março";
		break;
	case "04" :
		$data[1] = "Abril";
		break;
	case "05" :
		$data[1] = "Maio";
		break;
	case "06" :
		$data[1] = "Junho";
		break;
	case "07" :
		$data[1] = "Julho";
		break;
	case "08" :
		$data[1] = "Agosto";
		break;
	case "09" :
		$data[1] = "Setembro";
		break;
	case "10" :
		$data[1] = "Outubro";
		break;
	case "11" :
		$data[1] = "Novembro";
		break;
	case "12" :
		$data[1] = "Dezembro";
		break;
}

$status = $entrada_saida;

switch ($status) {
	case '1' :
		$status = "Recebemos do(a)";
		$ass = registro($usuario_id, "usuario", "nome");
    	$ass_img = "<br><br>__________________________________________";
		
		break;
	default:
		$status = "Pagamos ao";
	    $ass = $recebemos_pagamos;
	    $ass_img = "";
}

if(isset($_COOKIE['email'])){
	$vezes = 2;
}else{
	$vezes = 1;
}

$endereco = registro($filial_id, 'filiais', 'endereco').', '.registro($filial_id, 'filiais', 'numero').' '.registro($filial_id, 'filiais', 'bairro').' - '.registro(registro($filial_id, 'filiais', 'cidade'), 'localidade_cidades', 'nome', 'cod_cidades').' '.registro(registro($filial_id, 'filiais', 'estado'), 'localidade_estados', 'sigla', 'cod_estados');
$telefone_1 = registro($filial_id, 'filiais', 'telefone_1');
$telefone_2 = registro($filial_id, 'filiais', 'telefone_2');
$filial = registro($filial_id, 'filiais', 'nome');

?>

<!DOCTYPE html>
<html>
<head>
	<title>Recibo <?php echo $idContaSub; ?></title>
	<!-- style pessoal editado -->
	<link rel="stylesheet" href="../css/cssImp.css">
</head>
<!--<body onload="javascript:self.print()">-->
<body>

	<?php for($j=0; $j<$vezes; $j++): ?>
		<?php if($j>0): ?>
			<hr style='border-top: 1px dashed #f00; border-bottom: none; color: #fff; background-color: #fff; height: 4px;'>
		<?php endif; ?>
	<div style='margin-left: auto; margin-right: auto; margin-bottom:30px; margin-top:30px; width:700px; border: 2px solid #999; border-radius: 20px;'>
		<table>
			<tr>
				<td colspan='5' class='tdNone' align='center' width='806' height='131'>
					<?php include 't_up.php'; ?>
				</td>
			</tr>
			<tr>
				<td align='center' class='tdNone'>
					<table align='center'>
						<tr>
							<td align='center' class='tdNone' style='vertical-align:middle; white-space: nowrap;'>Nº da Conta</td>
							<td align='left' class='tdNone' style='vertical-align:middle;'><?php echo $idConta; ?></td>
							<td align='center' class='tdNone' style='vertical-align:middle; white-space: nowrap;'>Nº do Recibo</td>
							<td align='left' class='tdNone' style='vertical-align:middle;'><?php echo $idContaSub; ?></td>

							<?php if(is_numeric($referido) and $tabela_referente=='venda'): ?>
								
								<td align='center' class='tdNone' style='vertical-align:middle; white-space: nowrap;'>Nº Orçamento</td>

							<?php elseif(is_numeric($referido) and $tabela_referente=='ordem_de_servico'): ?>

								<td align='center' class='tdNone' style='vertical-align:middle; white-space: nowrap;'>Nº Ordem de Serviço</td>

							<?php else: ?>
								<td align='center' colspan='2' class='tdNone'><br></td>
							<?php endif; ?>

							<?php if(is_numeric($referido)): ?>
								<td align='left' class='tdNone' style='vertical-align:middle;'><?php echo $referido; ?></td>	
							<?php endif; ?>

							<td align='center' style='border:none; font-size:25px; font-weight: bold; width:500px;'>R$ <?php echo real($valor, 'real'); ?>

							<?php if($desconto): ?>
								<span style='display: inline-block;  background-color: white;  border: none;  font-size: 10px;  padding-left: 20px;'>VOCÊ ECONOMIZOU<br>
									R$ <?php echo real($desconto, 'real'); ?></span>
							<?php endif; ?>
                            </td>
						</tr>

						<tr>
							<td colspan='7' align='left'><span class='spanRecibo'><?php echo $status; ?> Sr.(s)</span><center style='font-style:italic; font-weight:bold; font-variant:small-caps; font-size:17px;'><?php echo $recebemos_pagamos; ?></center></td>
						</tr>
						<tr>
							<td colspan='7' align='left'><span class='spanRecibo'>A quantida de</span><center style='font-style:italic; font-weight:bold; font-variant:small-caps;  font-size:17px;'><?php echo extenso($valor, false); ?></td>
						</tr>

						<tr>
							<td colspan='7' align='left'><span class='spanRecibo'>Referente a</span><center style='font-style:italic; font-weight:bold; font-variant:small-caps;  font-size:17px;'>
							<?php if($valor<$total): ?>
								Parte do pagamento de 
							<?php endif; ?>

							<?php echo $referido_recibo; ?>

							<?php if(($total-$ja_pago)>0): ?>
							<br>Falta R$ <?php echo real($total-$ja_pago, 'real'); ?>
							<?php endif; ?>

							<?php if($desconto): ?>
							 com desconto de R$ <?php echo real($desconto, 'real'); ?>
							 <?php endif; ?>
                            </td>
						</tr>
						</tr>

						<tr>
	                        <td colspan='7' style='border:none; text-align:center;'>Sobral (CE) <?php echo $data[2]." de ".$data[1]." de ".$data[0]; ?> <br>
									<?php echo $ass_img; ?><br>
									<?php echo $ass; ?><br>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
	<span style="text-align: center; display: block; top: -26px; position: relative; font-size: 10px; color: red;">
		Sistema desenvolvido por www.rocketsolution.com.br
	</span>
	<?php endfor; ?>
</body>
</html>
