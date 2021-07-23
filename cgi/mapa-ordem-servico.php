<?php

include 'inc/functions.inc.php';

is_logged_usuario();

extract($_POST);
extract($_GET);

if(isset($id))
    if(!is_numeric($id))
        $id = base64_decode($id);


$conn = TConnection::open(DB);


$hoje = date('Y-m-d');

$criterio = new TCriteria();
$criterio->add(new TFilter('id', '<>', 6)); //finalizada
$criterio->add(new TFilter('id', '<>', 7)); //cancelada
$criterio->add(new TFilter('status', '=', 1));

$sql = new TSqlSelect();
$sql->setEntity('ordem_de_servico_status');
$sql->addColumn('id as statusID');
$sql->addColumn('nome as statusNome');
$sql->setCriteria($criterio);

$result = $conn->query($sql->getInstruction());

$column = array('ID','Nome do cliente', 'Data de abertura', 'Imei', 'Fotos do aparelho');
$cod_pag = $script = null;

for($i = $totalOS = $totalOSAmarelo = $totalOSVerde = $totalOSVermelho = 0; $i<$result->rowCount(); $i++){

    extract($result->fetch(PDO::FETCH_ASSOC));

    $entries = null;

    $criterio = new TCriteria();
    $criterio->add(new TFilter('status', '=', $statusID));
    $criterio->setProperty('order', 'data_de_abertura');

    $sql = new TSqlSelect();
    $sql->setEntity('ordem_de_servico');
    $sql->addColumn('*');
    $sql->setCriteria($criterio);
    $resultCards = $conn->query($sql->getInstruction());

    if($resultCards->rowCount()){

        for($j=0; $j<$resultCards->rowCount(); $j++){

            extract($resultCards->fetch(PDO::FETCH_ASSOC));
            $idOS = $id;
            $entries[$j][0] = '<a class="btn btn-primary" href="cadastrar-card.php?op=visualizar&id='.base64_encode($id).'">'.$id.'</a>';
            
            $entries[$j][1] = $nome.'<br>';
            
            if($wpp){
                $wpp = str_replace('_', '', $wpp);
                $telefone = $wpp;
                $wpp = str_replace('(', '', $wpp);
                $wpp = str_replace(')', '', $wpp);
                $wpp = str_replace(' ', '', $wpp);
                $wpp = str_replace('.', '', $wpp);

                $wpp = 'https://api.whatsapp.com/send?phone=55'.$wpp;
                $entries[$j][1] .= ' <a class="btnImp btn btn-danger" target="blank_" href="'.$wpp.'">'.$telefone.'</a>';
                
            }            
            
            
            $entries[$j][2] = formataData($data_de_abertura);
            $entries[$j][3] = $imei_ou_n_de_serie;
            
            $criterio_img = new TCriteria();
            $criterio_img->add(new TFilter('ordem_de_servico_id', '=', $idOS));
            $criterio_img->add(new TFilter('tipo', '=', 'fotos_do_celular'));

            $sql_img = new TSqlSelect();
            $sql_img->setEntity('ordem_de_servico_atributos');
            $sql_img->addColumn('valor as img');
            $sql_img->setCriteria($criterio_img);

            $result_img = $conn->query($sql_img->getInstruction());

            $entries[$j][4] = null;
            $tem_arte = 0;
            for($h=0; $h<$result_img->rowCount(); $h++){
                extract($result_img->fetch(PDO::FETCH_ASSOC));
                
                $entries[$j][4] .= '<span class="fa fa-file-image-o" style="font-size: 3em; margin: 10px;" onclick="window.open(\''.$img.'\',\'GoogleWindow\', \'width=400, height=400\');"></span>';
                $tem_arte++;
            }

            
            
        }
        $fontes[] = $statusNome;
        $dados[] = $resultCards->rowCount();
        $totalOS += intval($resultCards->rowCount());
        
        $table = new table($statusNome.' ('.$resultCards->rowCount().')', $column, $entries, 'table_'.$i);
        $table->focus = null;
        $script .= $table->get_script();
        $cod_pag .= $table->set_table();
    }
    

}

	
	



//$fontes = array('Total Bruto', 'Total já recebido', 'Falta receber');
//$dados = array($total, $total_pago, ($total-$total_pago));
if(isset($fontes)){
    $grafico = new chart('Gráfico das Cards em produção', $fontes, $dados, 'bar');



    if($totalOSVermelho){
        $fora_do_prazo = ((($totalOSVermelho * 100)/($totalOSVerde + $totalOSAmarelo + $totalOSVermelho)));
    }else{
        $fora_do_prazo = 0;
    }


    $cod_grafico = '
        <section class="content">
            <div class="row">
                <div class="col-md-12">'.$grafico->get_chart().'</div>
            <!--
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-green"><i class="fa fa-clock-o"></i></span>

                        <div class="info-box-content">
                        <span class="info-box-text">Total Cards Verdes</span>
                        <span class="info-box-number">'. $totalOSVerde .'</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="fa fa-clock-o"></i></span>

                        <div class="info-box-content">
                        <span class="info-box-text">Total Cards Amarelos</span>
                        <span class="info-box-number">'. $totalOSAmarelo .'</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-red"><i class="fa fa-clock-o"></i></span>

                        <div class="info-box-content">
                        <span class="info-box-text">Total Cards Vermelhos</span>
                        <span class="info-box-number">'. $totalOSVermelho .'</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-blue"><i class="fa fa-pie-chart"></i></span>

                        <div class="info-box-content">
                        <span class="info-box-text">Índice Cards fora do prazo</span>
                        <span class="info-box-number">'. $fora_do_prazo .'%</span>
                        </div>
                    </div>
                </div>
                -->
            </div>
        </section>
        ';
}else{
    $cod_grafico = null;
}

$html = new template;
if(!isset($buscar_por)){
	$cod = $html->set_box('Mapa de produção', $cod_pag);
}else{
	$cod = $cod_pag;
}
$html->set_html($cod.$cod_grafico);
$html->get_html('Gerêncie a produção de ordem de serviço', 'Visualize todas as ordens de serviço em produção');


?>

<?php 
	if(isset($script))
		echo $script; 
?>
<script type="text/javascript">
	$(function(){
		var valor = $('select[name="buscar_por"]').val();
		busca(valor);
		
	});

	function busca(valor){
		$('#id').hide();
		$('#nome_do_cliente').hide();
		$('#servico').hide();
		$('#data_1').hide();
		$('#data_2').hide();

		if(valor == 'data'){
			$('#data_1').show();
			$('#data_2').show();
		}else{
			$('#'+valor).show();
		}
	}

</script>
