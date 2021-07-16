<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta charset="UTF-8">
	<title>Visualizar frequ&ecirc;ncia de colaborador</title>
	<style type="text/css">
		body {
			font-family: "Adobe Pi Std", "Calibri", sans-serif;
			font-size: 10px;
		}
		.tdFrequenciaImp {
			border: 1px solid #000;
			border-radius: 5px;
		}

		
		.tooltip_templates{
			display: none;
		}
		a{
			text-decoration: none;
		}

	</style>
	
	<script type='text/javascript' src="https://code.jquery.com/jquery-3.4.1.js"></script>
	
	
	<link rel="stylesheet" type="text/css" href="tooltipster/dist/css/tooltipster.bundle.min.css" />

    <script type="text/javascript" src="http://code.jquery.com/jquery-1.10.0.min.js"></script>
    <script type="text/javascript" src="tooltipster/dist/js/tooltipster.bundle.min.js"></script>

	

	<script type="text/javascript">
		$(function(){
			$('.tooltip').tooltipster({
				contentCloning: true
			});
			
		});
	</script>
</head>
	<body>
		<?php
		function subMin($hora1, $hora2) {
			if (empty($hora1)) {
				$hora1 = "00:00";
			}
			if (empty($hora2)) {
				$hora2 = "00:00";
			}
			$separar[1] = explode(':', $hora1);
			$separar[2] = explode(':', $hora2);

			$total_minutos_trasncorridos[1] = ($separar[1][0] * 60) + $separar[1][1];
			$total_minutos_trasncorridos[2] = ($separar[2][0] * 60) + $separar[2][1];
			$total_minutos_trasncorridos = $total_minutos_trasncorridos[1] - $total_minutos_trasncorridos[2];

			return ($total_minutos_trasncorridos);
		}

		function subHor($hora1, $hora2) {
			if (empty($hora1)) {
				$hora1 = "00:00";
			}
			if (empty($hora2)) {
				$hora2 = "00:00";
			}
			$separar[1] = explode(':', $hora1);
			$separar[2] = explode(':', $hora2);

			$total_horas_trasncorridos[1] = $separar[1][0] + ($separar[1][1] / 60);
			$total_horas_trasncorridos[2] = $separar[2][0] + ($separar[2][1] / 60);
			$total_horas_trasncorridos = $total_horas_trasncorridos[1] - $total_horas_trasncorridos[2];

			return ($total_horas_trasncorridos);
		}

		function horaPlus($hora) {
			if ($hora) {
				$hora = explode(":", $hora);
				$hora[0]++;
				$hora = $hora[0] . ":" . $hora[1];
			}
			return $hora;
		}

		function horaMinus($hora) {
			if ($hora) {
				$hora = explode(":", $hora);
				$hora[0]--;
				$hora = $hora[0] . ":" . $hora[1];
			}
			return $hora;
		}
		
		//echo $_COOKIE["tipo"]." ".$_COOKIE["login"]." ".$_COOKIE["senha"];

		
		if (!(empty($_COOKIE["tipo"]))) {
			$login = $_COOKIE["login"];
			include "conecta.php";

			//declarando variaveis
			$falta = 0;
			$atraso = 0;
			$atrasoSTR = 0;
			$extraSTR = 0;
			extract($_POST);
			$idUsuario = $id;
			
			//colaborador mes ano
			switch ($mes) {
				case 1 :
					$Mes = "Janeiro";
					break;
				case 2 :
					$Mes = "Fevereiro";
					break;
				case 3 :
					$Mes = "Mar&ccedil;o";
					break;
				case 4 :
					$Mes = "Abril";
					break;
				case 5 :
					$Mes = "Maio";
					break;
				case 6 :
					$Mes = "Junho";
					break;
				case 7 :
					$Mes = "Julho";
					break;
				case 8 :
					$Mes = "Agosto";
					break;
				case 9 :
					$Mes = "Setembro";
					break;
				case 10 :
					$Mes = "Outubro";
					break;
				case 11 :
					$Mes = "Novembro";
					break;
				case 12 :
					$Mes = "Dezembro";
					break;
			}
			$instrucao = "select * from usuario where id='$idUsuario'";
			$sql = mysqli_query($conexao, $instrucao);
			$reg = mysqli_fetch_assoc($sql);
			extract($reg);
			//id login senha nome data_nascimento funcao funcao2 funcao3 email fone1 fone2 endereco complemento
			//bairro municipio rg cpf carteira salario_base filhos data_adminissao data_demissao status

			echo "<center>";

			echo "FOLHA DE CONTROLE DE FREQU&Ecirc;NCIA";
			echo "<table align='center'>";
			echo "<tr>";
			echo "<td class='tdFrequenciaImp' align='center' width='100' colspan='2'><b>Empregador:</td>";
			echo "<td class='tdFrequenciaImp' align='center' width='250' colspan='4'>FRANCISCO ROBÉRIO LIMA ROCHA - ME</td>";
			echo "<td class='tdFrequenciaImp' align='center' width='60'><b>CNPJ/CEI:</td>";
			echo "<td class='tdFrequenciaImp' align='center' width='250' colspan='2'>23.581.283/0001-08</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='tdFrequenciaImp' align='center' colspan='2'><b>Endere&ccedil;o:</td>";
			echo "<td class='tdFrequenciaImp' align='center' colspan='4'>AV. MONS. ALOÍSIO PINTO, 650 DOM EXPEDITO SOBRAL CE</td>";
			echo "<td class='tdFrequenciaImp' align='center'><b>Per&iacute;odo:</td>";
			echo "<td class='tdFrequenciaImp' align='center' colspan='2'>$Mes / $ano</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='tdFrequenciaImp' align='center'colspan='2'><b>Atividade:</td>";
			echo "<td class='tdFrequenciaImp' align='center'colspan='4'></td>";
			echo "<td class='tdFrequenciaImp' align='center'><b>Lota&ccedil;&atilde;o:</td>";
			echo "<td class='tdFrequenciaImp' align='center'colspan='2'></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='tdFrequenciaImp' align='center' colspan='2'><b>Empregado (a):</td>";
			echo "<td class='tdFrequenciaImp' align='center' colspan='4'>$nome</td>";
			echo "<td class='tdFrequenciaImp' align='center'><b>CPF:</td>";
			echo "<td class='tdFrequenciaImp' align='center'colspan='2'>$cpf</td>";
			echo "</tr>";
			;

			//selecionando horario e salario do usuario
			$instrucao = "select * from relogio_turnos where id_usuario='$idUsuario' and data_validade=(select max(data_validade) from relogio_turnos where id_usuario='$idUsuario')";

			$sql = mysqli_query($conexao, $instrucao);
			if (!mysqli_query($conexao, $instrucao)){
				echo("Error description: " . mysqli_error($conexao));
			}
			$infoSalario = mysqli_fetch_assoc($sql);
			extract($infoSalario);
			//id log_colaborador dava_validade data_vencimento dom_t1_e dom_t1_s dom_t2_e dom_t2_s
			//seg_t1_e seg_t1_s seg_t2_e seg_t2_s ter_t1_e ter_t1_s ter_t2_e ter_t2_s qua_t1_e qua_t1_s qua_t2_e qua_t2_s
			//qui_t1_e qui_t1_s qui_t2_e qui_t2_s sex_t1_e sex_t1_s sex_t2_e sex_t2_s sab_t1_e sab_t1_s sab_t2_e sab_t2_s
			echo "<tr>";
			echo "<td colspan='9' align='center' width='660'>";
			echo "<table>";
			echo "<tr>";
			echo "<td></td>";
			echo "<td colspan='14' align='center'>Hor&aacute;rio de trabalho</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td></td>";
			echo "<td colspan='2' align='center' class='tdFrequenciaImp'>DOM</td>";
			echo "<td colspan='2' align='center' class='tdFrequenciaImp'>SEG</td>";
			echo "<td colspan='2' align='center' class='tdFrequenciaImp'>TER</td>";
			echo "<td colspan='2' align='center' class='tdFrequenciaImp'>QUA</td>";
			echo "<td colspan='2' align='center' class='tdFrequenciaImp'>QUI</td>";
			echo "<td colspan='2' align='center' class='tdFrequenciaImp'>SEX</td>";
			echo "<td colspan='2' align='center' class='tdFrequenciaImp'>SAB</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td>Turno 1</td>";
			echo "<td align='center' class='tdFrequenciaImp' width='30'>$dom_t1_e</td>";
			echo "<td align='center' class='tdFrequenciaImp' width='30'>$dom_t1_s</td>";
			echo "<td align='center' class='tdFrequenciaImp' width='30'>$seg_t1_e</td>";
			echo "<td align='center' class='tdFrequenciaImp' width='30'>$seg_t1_s</td>";
			echo "<td align='center' class='tdFrequenciaImp' width='30'>$ter_t1_e</td>";
			echo "<td align='center' class='tdFrequenciaImp' width='30'>$ter_t1_s</td>";
			echo "<td align='center' class='tdFrequenciaImp' width='30'>$qua_t1_e</td>";
			echo "<td align='center' class='tdFrequenciaImp' width='30'>$qua_t1_s</td>";
			echo "<td align='center' class='tdFrequenciaImp' width='30'>$qui_t1_e</td>";
			echo "<td align='center' class='tdFrequenciaImp' width='30'>$qui_t1_s</td>";
			echo "<td align='center' class='tdFrequenciaImp' width='30'>$sex_t1_e</td>";
			echo "<td align='center' class='tdFrequenciaImp' width='30'>$sex_t1_s</td>";
			echo "<td align='center' class='tdFrequenciaImp' width='30'>$sab_t1_e</td>";
			echo "<td align='center' class='tdFrequenciaImp' width='30'>$sab_t1_s</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td>Turno 2</td>";
			echo "<td align='center' class='tdFrequenciaImp' width='30'>$dom_t2_e</td>";
			echo "<td align='center' class='tdFrequenciaImp' width='30'>$dom_t2_s</td>";
			echo "<td align='center' class='tdFrequenciaImp' width='30'>$seg_t2_e</td>";
			echo "<td align='center' class='tdFrequenciaImp' width='30'>$seg_t2_s</td>";
			echo "<td align='center' class='tdFrequenciaImp' width='30'>$ter_t2_e</td>";
			echo "<td align='center' class='tdFrequenciaImp' width='30'>$ter_t2_s</td>";
			echo "<td align='center' class='tdFrequenciaImp' width='30'>$qua_t2_e</td>";
			echo "<td align='center' class='tdFrequenciaImp' width='30'>$qua_t2_s</td>";
			echo "<td align='center' class='tdFrequenciaImp' width='30'>$qui_t2_e</td>";
			echo "<td align='center' class='tdFrequenciaImp' width='30'>$qui_t2_s</td>";
			echo "<td align='center' class='tdFrequenciaImp' width='30'>$sex_t2_e</td>";
			echo "<td align='center' class='tdFrequenciaImp' width='30'>$sex_t2_s</td>";
			echo "<td align='center' class='tdFrequenciaImp' width='30'>$sab_t2_e</td>";
			echo "<td align='center' class='tdFrequenciaImp' width='30'>$sab_t2_s</td>";
			echo "</tr>";
			echo "</table>";

			//segundo cabeçalho
			echo "<tr>";
			echo "<td colspan='2'></td>";
			echo "<td class='tdFrequenciaImp' colspan='2'><b><center>INTERVALO ALMO&Ccedil;O</td>";
			echo "<td></td>";
			echo "<td class='tdFrequenciaImp' colspan='2'><b><center>PRORROGA&Ccedil;&Atilde;O H.EXTRA</td>";
			echo "<td colspan='2'></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='tdFrequenciaImp' ><b><center>DIA</td>";
			echo "<td class='tdFrequenciaImp' ><b><center>ENTRADA</td>";
			echo "<td class='tdFrequenciaImp' ><b><center>INICIO</td>";
			echo "<td class='tdFrequenciaImp' ><b><center>FIM</td>";
			echo "<td class='tdFrequenciaImp' ><b><center>SA&Iacute;DA</td>";
			echo "<td class='tdFrequenciaImp' ><b><center>ENTRADA</td>";
			echo "<td class='tdFrequenciaImp' ><b><center>SA&Iacute;DA</td>";
			echo "<td class='tdFrequenciaImp' colspan='2'><b><center>ASSINATURA / JUSTIFICATIVA</td>";
			echo "</tr>";

			//aqui que a magica começa
			$width = 80;
			$linhaDias = date('t', mktime(0, 0, 0, $mes, 1, $mes));
			$horaCompletar = 0;
			$horaPreenchidas = 0;
			for ($i = 1; $i <= $linhaDias; $i++) {
				$hora1 = NULL;
				$hora2 = NULL;
				$hora3 = NULL;
				$hora4 = NULL;

				echo "<tr>";
				$diaSemana = date('w', mktime(0, 0, 0, $mes, $i, $ano));
				if ($diaSemana == 0) {
					$diaSemana = "Dom";
					$horaEntradaT2 = $dom_t2_e;
					$horaSaidaT1 = $dom_t1_s;

					$horaCheck1e = $dom_t1_e;
					$horaCheck1s = $dom_t1_s;
					$horaCheck2e = $dom_t2_e;
					$horaCheck2s = $dom_t2_s;
				} elseif ($diaSemana == 1) {
					$diaSemana = "Seg";
					$horaEntradaT2 = $seg_t2_e;
					$horaSaidaT1 = $seg_t1_s;

					$horaCheck1e = $seg_t1_e;
					$horaCheck1s = $seg_t1_s;
					$horaCheck2e = $seg_t2_e;
					$horaCheck2s = $seg_t2_s;
				} elseif ($diaSemana == 2) {
					$diaSemana = "Ter";
					$horaEntradaT2 = $ter_t2_e;
					$horaSaidaT1 = $ter_t1_s;

					$horaCheck1e = $ter_t1_e;
					$horaCheck1s = $ter_t1_s;
					$horaCheck2e = $ter_t2_e;
					$horaCheck2s = $ter_t2_s;
				} elseif ($diaSemana == 3) {
					$diaSemana = "Qua";
					$horaEntradaT2 = $qua_t2_e;
					$horaSaidaT1 = $qua_t1_s;

					$horaCheck1e = $qua_t1_e;
					$horaCheck1s = $qua_t1_s;
					$horaCheck2e = $qua_t2_e;
					$horaCheck2s = $qua_t2_s;
				} elseif ($diaSemana == 4) {
					$diaSemana = "Qui";
					$horaEntradaT2 = $qui_t2_e;
					$horaSaidaT1 = $qui_t1_s;

					$horaCheck1e = $qui_t1_e;
					$horaCheck1s = $qui_t1_s;
					$horaCheck2e = $qui_t2_e;
					$horaCheck2s = $qui_t2_s;
				} elseif ($diaSemana == 5) {
					$diaSemana = "Sex";
					$horaEntradaT2 = $sex_t2_e;
					$horaSaidaT1 = $sex_t1_s;

					$horaCheck1e = $sex_t1_e;
					$horaCheck1s = $sex_t1_s;
					$horaCheck2e = $sex_t2_e;
					$horaCheck2s = $sex_t2_s;
				} elseif ($diaSemana == 6) {
					$diaSemana = "Sab";
					$horaEntradaT2 = $sab_t2_e;
					$horaSaidaT1 = $sab_t1_s;

					$horaCheck1e = $sab_t1_e;
					$horaCheck1s = $sab_t1_s;
					$horaCheck2e = $sab_t2_e;
					$horaCheck2s = $sab_t2_s;
				}
				if (empty($horaCheck1e)) {
					$bgcolor = "#c2c2c2";
				} else {
					$bgcolor = "#e7e7e7";
				}

				echo "<td class='tdFrequenciaImp' align='center' valign='middle' bgcolor='$bgcolor'><b>$i $diaSemana</td>";

				//ENTRADA TURNO 1
				$dataCheck = $ano . "-" . $mes . "-" . $i;
				if ($horaSaidaT1){
					$instrucao = "select hora, end_foto, justificativa, descricao from relogio_ponto where data='$dataCheck' and acao='Entrar' and hora<='$horaSaidaT1' and id_usuario='$idUsuario'";
				}else{
					$instrucao = "select hora, end_foto, justificativa, descricao from relogio_ponto where data='$dataCheck' and acao='Entrar' and id_usuario='$idUsuario'";
				}
				$sql = mysqli_query($conexao, $instrucao);
				if (!mysqli_query($conexao, $instrucao)){
					echo("Error description: " . mysqli_error($conexao));
				}
				$linha = mysqli_num_rows($sql);

				//EXCESSAO SEM ENTRADA
				if (empty($horaCheck1e)) {
					echo "<td class='tdFrequenciaImp' colspan='2' align='center' valign='middle' bgcolor='$bgcolor'>X</td>";
					$descricao1 = $descricao2 = NULL;
				} else {
					if ($linha <> 0) {
						$reg = mysqli_fetch_assoc($sql);
						extract($reg);
						// hora end_foto justificativa descricao
						$descricao1 = $descricao;
					} else {
						$hora = "";
						$end_foto = NULL;
						$justificativa = NULL;
						$descricao1 = NULL;
					}

					//VERIFICANDO ATRASO E SOMANDO SE EXISTIR
					if (strtotime($hora) > strtotime($horaCheck1e) and $justificativa == NULL and $hora <> NULL) {
						$font = 'red';
					} else {
						$font = 'black';
					}
					if ($hora) {
						$hora1 = $hora;
					}
					//EXCESSAO FOTO com justificativa
					if ($end_foto == NULL and $justificativa == NULL) {
						echo "<td class='tdFrequenciaImp' align='center' valign='middle' bgcolor='$bgcolor'><font color='red'>*</font>";
						//adicionar falta
						$falta += 0.5;
					} else {
						echo "<td class='tdFrequenciaImp' align='center' valign='middle' bgcolor='$bgcolor'>";
					}
					//filtro justificativa
					if ($justificativa <> NULL and $hora <> NULL) {
						echo "<font color='$font'>X</font></td>";
					} else {
						if($end_foto){
							$end_foto = str_replace('../', '', $end_foto);
							$idFoto = explode('/', $end_foto);
							$idFoto = str_replace('.jpg', '', $idFoto[(count($idFoto)-1)]);
							echo "<a href='#' class='tooltip' data-tooltip-content='#$idFoto'>";
						}
						echo "<font color='$font'>$hora</font>";
						
						if($end_foto){
							echo "</a>";
						}

						echo "</td>";

						if($end_foto){
							echo '<div class="tooltip_templates">
							    <span id="'.$idFoto.'">
							        <img src="'.$end_foto.'" />
							    </span>
							</div>';
						}
					}

					//SAIDA PRIMEIRO TURNO
					if ($horaEntradaT2){
						$instrucao = "select hora, end_foto, justificativa, descricao from relogio_ponto where data='$dataCheck' and acao='Sair' and hora<='$horaEntradaT2' and id_usuario='$idUsuario'";
					}else{
						$instrucao = "select hora, end_foto, justificativa, descricao from relogio_ponto where data='$dataCheck' and acao='Sair' and id_usuario='$idUsuario'";
					}
					$sql = mysqli_query($conexao, $instrucao);
					$linha = mysqli_num_rows($sql);
					//VERIFICANDO SAIDA ANTES DO HORARIO E SOMANDO SE EXISTIR
					if ($linha <> 0) {
						$reg = mysqli_fetch_assoc($sql);
						extract($reg);
						// hora end_foto justificativa descricao
						$descricao2 = $descricao;
					} else {
						$hora = "";
						$end_foto = NULL;
						$justificativa = NULL;
						$descricao2 = NULL;
					}

					//VERIFICANDO ATRASO E SOMANDO SE EXISTIR
					if (strtotime($hora) < strtotime($horaCheck1s) and $justificativa == NULL and $hora <> NULL) {
						$font = 'red';
					} else {
						$font = 'black';
					}
					if ($hora) {
						$hora2 = $hora;
					}
					//EXCESSAO FOTO com justificativa
					if ($end_foto == NULL and $justificativa == NULL) {
						echo "<td class='tdFrequenciaImp' align='center' valign='middle' bgcolor='$bgcolor'><font color='red'>*</font>";
						//adicionar falta
						$falta += 0.5;
					} else {
						echo "<td class='tdFrequenciaImp' align='center' valign='middle' bgcolor='$bgcolor'>";
					}
					//filtro justificativa
					if ($justificativa <> NULL and $hora <> NULL) {
						echo "<font color='$font'>X</td>";
					} else {
						if($end_foto){
							$end_foto = str_replace('../', '', $end_foto);
							$idFoto = explode('/', $end_foto);
							$idFoto = str_replace('.jpg', '', $idFoto[(count($idFoto)-1)]);
							echo "<a href='#' class='tooltip' data-tooltip-content='#$idFoto'>";
						}
						echo "<font color='$font'>$hora</font>";
						
						if($end_foto){
							echo "</a>";
						}

						echo "</td>";

						if($end_foto){
							echo '<div class="tooltip_templates">
							    <span id="'.$idFoto.'">
							        <img src="'.$end_foto.'" />
							    </span>
							</div>';
						}
					}

				}
				//ENTRADA TURNO 2
				$hora = $end_foto = $justificativa = NULL;
				$dataCheck = $ano . "-" . $mes . "-" . $i;
				$instrucao = "select hora, end_foto, justificativa, descricao from relogio_ponto where data='$dataCheck' and acao='Entrar' and hora>='$horaSaidaT1' and id_usuario='$idUsuario'";

				$sql = mysqli_query($conexao, $instrucao);
				$linha = mysqli_num_rows($sql);

				if (empty($horaCheck2e)) {
					$bgcolor = "#c2c2c2";
				} else {
					$bgcolor = "#e7e7e7";
				}

				//EXCESSAO SEM ENTRADA
				if (empty($horaCheck2e)) {
					echo "<td class='tdFrequenciaImp' colspan='2' align='center' valign='middle' bgcolor='$bgcolor'>X</td>";
					echo "<td class='tdFrequenciaImp' colspan='2' align='center' valign='middle' bgcolor='$bgcolor'>X</td>";
					echo "<td class='tdFrequenciaImp' colspan='2' align='center' valign='middle' bgcolor='$bgcolor'>X</td>";
					$descricao3 = $descricao4 = NULL;
				} else {
					//EXCESSAO SABADO

					if ($linha <> 0) {
						$reg = mysqli_fetch_assoc($sql);
						extract($reg);
						// hora end_foto justificativa descricao
						$descricao3 = $descricao;
					} else {
						$hora = "";
						$end_foto = NULL;
						$justificativa = NULL;
						$descricao3 = NULL;
					}

					//VERIFICANDO ATRASO E SOMANDO SE EXISTIR
					if (strtotime($hora) > strtotime($horaCheck2e) and $justificativa == NULL and $hora <> NULL) {
						$font = 'red';
					} else {
						$font = 'black';
					}
					if ($hora) {
						$hora3 = $hora;
					}
					//EXCESSAO FOTO com justificativa
					if ($end_foto == NULL and $justificativa == NULL) {
						echo "<td class='tdFrequenciaImp' align='center' valign='middle' bgcolor='$bgcolor'><font color='red'>*</font>";
						//adicionar falta
						$falta += 0.5;
					} else {
						echo "<td class='tdFrequenciaImp' align='center' valign='middle' bgcolor='$bgcolor'>";
					}
					//filtro justificativa
					if ($justificativa <> NULL and $hora <> NULL) {
						echo "<font color='$font'>X</td>";
					} else {
						if($end_foto){
							$end_foto = str_replace('../', '', $end_foto);
							$idFoto = explode('/', $end_foto);
							$idFoto = str_replace('.jpg', '', $idFoto[(count($idFoto)-1)]);
							echo "<a href='#' class='tooltip' data-tooltip-content='#$idFoto'>";
						}
						echo "<font color='$font'>$hora</font>";
						
						if($end_foto){
							echo "</a>";
						}

						echo "</td>";

						if($end_foto){
							echo '<div class="tooltip_templates">
							    <span id="'.$idFoto.'">
							        <img src="'.$end_foto.'" />
							    </span>
							</div>';
						}
					}

					//SAIDA MANHA
					$instrucao = "select hora, end_foto, justificativa, descricao from relogio_ponto where data='$dataCheck' and acao='Sair' and hora>'$hora2' and id_usuario='$idUsuario'";
					$sql = mysqli_query($conexao, $instrucao);
					$linha = mysqli_num_rows($sql);
					//VERIFICANDO SAIDA ANTES DO HORARIO E SOMANDO SE EXISTIR
					if ($linha == 1) {
						$reg = mysqli_fetch_assoc($sql);
						extract($reg);
						// hora end_foto justificativa descricao
						$descricao4 = $descricao;
					} else {
						$hora = "";
						$end_foto = NULL;
						$justificativa = NULL;
						$descricao4 = NULL;
					}

					//VERIFICANDO ATRASO E SOMANDO SE EXISTIR
					if (strtotime($hora) < strtotime($horaCheck2s) and $justificativa == NULL and $hora <> NULL) {
						$font = 'red';
					} else {
						$font = 'black';
					}
					if ($hora) {
						$hora4 = $hora;
					}
					//EXCESSAO FOTO com justificativa
					if ($end_foto == NULL and $justificativa == NULL) {
						echo "<td class='tdFrequenciaImp' align='center' valign='middle' bgcolor='$bgcolor'><font color='red'>*</font>";
						//adicionar falta
						$falta += 0.5;
					} else {
						echo "<td class='tdFrequenciaImp' align='center' valign='middle' bgcolor='$bgcolor'>";
					}
					//filtro justificativa
					if ($justificativa <> NULL and $hora <> NULL) {
						echo "<font color='$font'>X</td>";
					} else {
						if($end_foto){
							$end_foto = str_replace('../', '', $end_foto);
							$idFoto = explode('/', $end_foto);
							$idFoto = str_replace('.jpg', '', $idFoto[(count($idFoto)-1)]);
							echo "<a href='#' class='tooltip' data-tooltip-content='#$idFoto'>";
						}
						echo "<font color='$font'>$hora</font>";
						
						if($end_foto){
							echo "</a>";
						}

						echo "</td>";

						if($end_foto){
							echo '<div class="tooltip_templates">
							    <span id="'.$idFoto.'">
							        <img src="'.$end_foto.'" />
							    </span>
							</div>';
						}
					}

					echo "<td class='tdFrequenciaImp' bgcolor='$bgcolor'><center>:</td>";
					echo "<td class='tdFrequenciaImp' bgcolor='$bgcolor'><center>:</td>";
					//filtro variavel justificativa
					if ($descricao1 or $descricao2 or $descricao3 or $descricao4) {
						$script = "<td class='tdFrequenciaImp' bgcolor='$bgcolor'>";
						if ($descricao1==$descricao2 and $descricao3==$descricao4){
							$script .= "$descricao1";
						}
						if($descricao1<>$descricao2){
							$script .= "$descricao1 $descricao2";
						}
						if($descricao3<>$descricao4){
							$script .= "$descricao3 $descricao4";
						}
						$script .= "</td>";
						echo $script;
					} else {
						echo "<td class='tdFrequenciaImp' bgcolor='$bgcolor'></td>";
					}
					echo "</tr>";

				}//fecha else da excecao domingo

				//CALCULANDO HORAS A COMPLETAR
				if ($horaCheck1e) {
					$horaCompletar += subHor($horaCheck1s, $horaCheck1e);
					if (!empty($hora1) and !empty($hora2)) {
						#echo "<tr>";
						#	echo "<td colspan='8'>$hora2 - $hora1</td>";
						#echo "</tr>";
						$horaPreenchidas += subHor($hora2, $hora1);
					}
				}
				if ($horaCheck2e) {
					$horaCompletar += subHor($horaCheck2s, $horaCheck2e);
					if (!empty($hora3) and !empty($hora4)) {
						#echo "<tr>";
						#	echo "<td colspan='8'>$hora4 - $hora3</td>";
						#echo "</tr>";
						$horaPreenchidas += subHor($hora4, $hora3);
					}
				}

			}//fecha for
			
			$sql= mysqli_query($conexao, "select salario_base from usuario where id='$idUsuario'");
			extract(mysqli_fetch_assoc($sql));
			echo "</table>";
			echo "<table>";
			echo "<tr>";
			echo "<td align='center' valign='middle' colspan='2' rowspan='2'>";
				echo "<table>";
					echo "<tr>";
						echo "<td colspan='3' width='300'>Banco de Horas</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td></td>";
						echo "<td>Horas</td>";
						echo "<td>R$</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td>A completar</td>";
						echo "<td>".round($horaCompletar,2)."</td>";
						echo "<td>R$ $salario_base</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td>Preenchidas</td>";
						echo "<td>".round($horaPreenchidas,2)."</td>";
						$Salario = $horaPreenchidas * $salario_base / $horaCompletar;
						echo "<td>R$ ".round($Salario,2)."</td>";
					echo "</tr>";
					if ($salario_base<$Salario){
						echo "<tr>";
							echo "<td>Super&aacute;vit</td>";
							$super= $horaPreenchidas * 100 /  $horaCompletar - 100;
							echo "<td>+ ".round($super,2)."%</td>";
							$super= $Salario-$salario_base;
							echo "<td>R$ + ".round($super,2)."</td>";
						echo "</tr>";
					}else{
						echo "<tr>";
							echo "<td>Deficit</td>";
							$def= 100 - $horaPreenchidas * 100 / $horaCompletar;
							echo "<td>- ".round($def,2)."%</td>";
							$def= $Salario-$salario_base;
							echo "<td>R$ ".round($def,2)."</td>";
						echo "</tr>";
					}
					
				echo "</table>";
			echo "</td>";
			echo "<td align='center' valign='middle'></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td align='center' valign='middle' width='300'><hr>$nome<br>CPF $cpf</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td colspan='9'><center><small><small>* REGISTRO SEM FOTO.</td>";
			echo "</tr>";
			echo "</table>";
			echo "<br><br><br>";

			mysqli_close($conexao);
		}
		?>
	</body>
</html>