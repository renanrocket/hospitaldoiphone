<!-- Morris charts -->
  <link rel="stylesheet" href="plugins/AdminLTE-master/plugins/morris/morris.css">
<?php

include 'inc/functions.inc.php';

is_logged_usuario();


/*
	$data = '2018-06-20';
	$id = 1;

	$line = null;
	$line[0][0] = '<a class="btn btn-primary" href="cliente-naoquitados.php?op=visualizar&id='.base64_encode($id).'">'.$id.'</a>';
	$line[0][1] = '<a class="btn btn-primary" href="cadastrar-cliente.php?op=visualizar&id='.base64_encode($id).'">Renan Rocha</a>';
	$line[0][2] = formataData($data).'<br>(atrasado '.subtrairDatas($data, date('Y-m-d')).' dias)';
	$line[0][3] = real(49.90, 'real');
	$line[0][4] = 'Pendente Pagamento';


	$column = null;
	$column = array('ID', 'Cliente', 'Data Vencimento', 'Valor (R$)', 'Status');
	$table = new table('Usuários devedores', $column, $line);
	$table->focus = 'Pendente Pagamento';
	$table->focus_color = 'red';
	$table->get_script();


	$cod = $table->set_table();
*/






$tdl = new todolist();
$cod= $tdl->get_todolist();


//inicio de uma sessão (1)
$cod.= '<div class="row">';


//produtos mais acessados da ultima semana
$criterio = new TCriteria();
$criterio->add(new TFilter('data', '>=', date('Y-m-d H:i:s', strtotime('-1 week'))));
$criterio->add(new TFilter('operacao', '=', 'produto'));
$criterio->setProperty('limit', '500');
$criterio->setProperty('order', 'data DESC');

$sql = new TSqlSelect();
$sql->setEntity('acessos_site');
$sql->addColumn('*');
$sql->setCriteria($criterio);

$result = $conn->query($sql->getInstruction());
$listaProdutos = array();

for($i=0; $i<$result->rowCount(); $i++){
	extract($result->fetch(PDO::FETCH_ASSOC));

	if(!array_key_exists($valor, $listaProdutos)){
		$listaProdutos[$valor]=1;
	}else{
		$listaProdutos[$valor]++;
	}
	
}
arsort($listaProdutos);

$cod.= '<div class="col-xs-6">
			<div class="box">
				<div class="box box-primary">
					<div class="box-header">
						<i class="fa fa-mouse-pointer"></i>
						<h3 class="box-title">10 produtos mais acessados da semana</h3>
					</div>
					<!-- /.box-header -->
					<div class="box-body">';
$cod.= '<ul class="todo-list">';
$i=0;
foreach ($listaProdutos as $id => $acesso) {
	if ($i++ == 10) break;
	if(is_numeric($id)){
		$produto = registro($id, 'produto', 'nome');
		$link_e = '<a href="cadastrar-produto.php?op=visualizar&id='.base64_encode($id).'" target="_black"><i class="fa fa-share"></i></a>';
		$link_v = '<a href="../single-product.php?p='.base64_encode($id).'" target="_black"><i class="fa fa-edit"></i></a>';
	}elseif($id=='Camisa'){
		$produto = $id;
		$link_e = null;
		$link_v = '<a href="../orc-camisa.php" target="_black"><i class="fa fa-edit"></i></a>';
	}
	
	$obs = '<b>'.$produto.'</b> -> '.$acesso.' vezes';

	$cod.= '

	<li id="li_'.$id.'">
		<!-- drag handle -->
		<span class="handle">
			<i class="fa fa-ellipsis-v"></i>
			<i class="fa fa-ellipsis-v"></i>
		</span>
		<!-- todo text -->
		<span class="text">'.$obs.'</span>
		<!-- General tools such as edit or delete-->
		<div class="tools">
			'.$link_e.'
			'.$link_v.'
		</div>
	</li>';
}
$cod.= '</ul></div></div></div></div>';

//mais procurados
/*
 *
 *
 *
 */
$criterio = new TCriteria();
$criterio->add(new TFilter('operacao', '=', 'pesquisa'));
$criterio->setProperty('limit', '500');
$criterio->setProperty('order', 'data DESC');

$sql = new TSqlSelect();
$sql->setEntity('acessos_site');
$sql->addColumn('*');
$sql->setCriteria($criterio);

$result = $conn->query($sql->getInstruction());
$listaPesquisa = array();

for($i=0; $i<$result->rowCount(); $i++){
	extract($result->fetch(PDO::FETCH_ASSOC));

	if(!array_key_exists($valor, $listaPesquisa)){
		$listaPesquisa[$valor]=1;
	}else{
		$listaPesquisa[$valor]++;
	}
	
}
arsort($listaPesquisa);

$cod.= '
			<div class="col-xs-6">
				<div class="box">
					<div class="box box-primary">
						<div class="box-header">
							<i class="fa fa-search"></i>
							<h3 class="box-title">Top 10 pesquisas no site</h3>
						</div>
						<!-- /.box-header -->
						<div class="box-body">';
$cod.= '<ul class="todo-list">';
$i=0;
foreach ($listaPesquisa as $pesquisa => $acesso) {
	if ($i++ == 10) break;
	
	$obs = '<b>'.$pesquisa.'</b> -> '.$acesso.' vezes';

	$cod.= '

	<li id="li_'.$pesquisa.'">
		<!-- drag handle -->
		<span class="handle">
			<i class="fa fa-ellipsis-v"></i>
			<i class="fa fa-ellipsis-v"></i>
		</span>
		<!-- todo text -->
		<span class="text">'.$obs.'</span>
	</li>';
}
$cod.= '</ul></div></div></div></div>';


//fim de uma sessão (1)
$cod.= '</div>';

//inicio de uma sessao(2)
$cod.= '<div class="row">';


//visitas
/*
 *
 *
 *
 */
$criterio = new TCriteria();
$criterio->add(new TFilter('data', '>=', date('Y-m-d H:i:s', strtotime('-1 week'))));
$criterio->setProperty('limit', '500');
$criterio->setProperty('order', 'data DESC');

$sql = new TSqlSelect();
$sql->setEntity('acessos_site');
$sql->addColumn('*');
$sql->setCriteria($criterio);

$result = $conn->query($sql->getInstruction());
$listaLocalizacao = array();
$listaSessao = array();
$listaPag = array();
$acessos = 0;



for($i=0; $i<$result->rowCount(); $i++){
	extract($result->fetch(PDO::FETCH_ASSOC));

	if(!$latitude){

		$criterio = new TCriteria();
		$criterio->add(new TFilter('ip', '=', $ip));
		$criterio->add(new TFilter('latitude', '=', ''));
		$criterio->add(new TFilter('longitude', '=', ''));

		$sql = new TSqlSelect();
		$sql->setEntity('acessos_site');
		$sql->addColumn('cidade');
		$sql->addColumn('pais');
		$sql->addColumn('continente');
		$sql->addColumn('latitude');
		$sql->addColumn('longitude');
		$sql->addColumn('organizacao');
		$sql->setCriteria($criterio);

		$resultUPDATE = $conn->query($sql->getInstruction());

		if($resultUPDATE->rowCount()){
		//if(false){

			//olha primeiro no banco de dados se tem o ip ja registrado
			extract($resultUPDATE->fetch(PDO::FETCH_ASSOC));

		}else{

			//procura no banco de dados do ipfind (local mais preciso)
			$ip_address = $ip;
			$request_uri = 'https://ipfind.co';
			$auth = '63a8d102-58be-4594-9dbf-6a8e1508ef92';
			$url = $request_uri . "?ip=" . $ip_address . "&auth=" . $auth . "&lang=pt-BR";
			$document = my_file_get_contents($url);
			$resultIP = json_decode($document);
			
			if(!isset($resultIP->error)){

				$cidade = $resultIP->city;
				$pais = $resultIP->country;
				$continente = $resultIP->continent;
				$latitude = $resultIP->latitude;
				$longitude = $resultIP->longitude;
				$organizacao = $resultIP->owner;

			}else{

				//procura no ipapi local menos preciso
				$request_uri = 'http://api.ipapi.com/';
				$auth = '6c8723986076343bb458d9515d14e9fb';
				$url = $request_uri . $ip_address . "?access_key=" . $auth. "&language=pt-br&hostname=1";

				$document = my_file_get_contents($url);
				$resultIP = json_decode($document);

				$cidade = $resultIP->city;
				$pais = $resultIP->country_name;
				$continente = $resultIP->continent_name;
				$latitude = $resultIP->latitude;
				$longitude = $resultIP->longitude;
				$organizacao = $resultIP->hostname;



			}

		}





		$criterio = new TCriteria();
		$criterio->add(new TFilter('id', '=', $id));

		$sql = new TSqlUpdate();
		$sql->setEntity('acessos_site');
		$sql->setRowData('cidade', $cidade);
		$sql->setRowData('pais', $pais);
		$sql->setRowData('continente', $continente);
		$sql->setRowData('latitude', $latitude);
		$sql->setRowData('longitude', $longitude);
		$sql->setRowData('organizacao', $organizacao);
		$sql->setCriteria($criterio);
		$resultUPDATE = $conn->query($sql->getInstruction());
	}

	if($i==0){
		$ultimoAcesso = formataData($data);
		if(is_numeric($valor)){
			$ultimoAcessoPag = registro($valor,'produto', 'nome');
		}else{
			$ultimoAcessoPag = $valor;
		}
	}

	if($latitude){

		$cidade == '' ? $chave = $pais : $chave = $cidade;

		$chave.= ','.$latitude.','.$longitude;

		if(!array_key_exists($chave, $listaLocalizacao)){
			$listaLocalizacao[$chave]=1;
		}else{
			$listaLocalizacao[$chave]++;
		}

	}

	if(!array_key_exists($session, $listaSessao)){
		$listaSessao[$session]=1;
	}else{
		$listaSessao[$session]++;
	}

	if($operacao=='acesso' or $operacao=='produto'){

		if(!array_key_exists($valor, $listaPag)){
			$listaPag[$valor]=1;
		}else{
			$listaPag[$valor]++;
		}
	}

	$acessos++;

	
}

if(!$result->rowCount()){

	$ultimoAcessoPag = $ultimoAcesso = null;
}
arsort($listaPag);
arsort($listaLocalizacao);
$listaPagChave = array_keys($listaPag);
$cod.= "
	<!-- jvectormap -->
  	<link rel=\"stylesheet\" href=\"plugins/AdminLTE-master/plugins/jvectormap/jquery-jvectormap-1.2.2.css\">
	<script type=\"text/javascript\">
	$(function(){
		/* jVector Maps
	   * ------------
	   * Create a world map with markers
	   */
	  $('#world-map-markers').vectorMap({
	    map: 'world_mill_en',
	    normalizeFunction: 'polynomial',
	    hoverOpacity: 0.7,
	    hoverColor: false,
	    backgroundColor: 'transparent',
	    regionStyle: {
	      initial: {
	        fill: 'rgba(210, 214, 222, 1)',
	        \"fill-opacity\": 1,
	        stroke: 'none',
	        \"stroke-width\": 0,
	        \"stroke-opacity\": 1
	      },
	      hover: {
	        \"fill-opacity\": 0.7,
	        cursor: 'pointer'
	      },
	      selected: {
	        fill: 'yellow'
	      },
	      selectedHover: {}
	    },
	    markerStyle: {
	      initial: {
	        fill: '#00a65a',
	        stroke: '#111'
	      }
	    },
	    markers: [";
$localizacaoChave = array_keys($listaLocalizacao);
for($i=0; $i<count($listaLocalizacao); $i++){
	if($i>0)
		$cod.=',';
	$localizacao = explode(',' , $localizacaoChave[$i]);
	$cod.= "{latLng: [".$localizacao[1].", ".$localizacao[2]."], name: '".$localizacao[0]." ".$listaLocalizacao[$localizacaoChave[$i]]." visitas'}";
}


	    $cod.="]
	  });
	});


</script>
<!-- jvectormap -->
 <script src=\"plugins/AdminLTE-master/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js\"></script>
 <script src=\"plugins/AdminLTE-master/plugins/jvectormap/jquery-jvectormap-world-mill-en.js\"></script>
 <!-- Sparkline -->
 <script src=\"plugins/AdminLTE-master/plugins/sparkline/jquery.sparkline.min.js\"></script>";



$cod.='<div class="col-md-12">
	   <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title">Visitantes da ultima semana</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body no-padding">
          <div class="row">
            <div class="col-md-9 col-sm-8">
              <div class="pad">
                <!-- Map will be created here -->
                <div id="world-map-markers" style="height: 427px;"></div>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-md-3 col-sm-4">
              <div class="pad box-pane-right bg-green" style="min-height: 280px">
                <div class="description-block margin-bottom">
                  <div class="fa fa-users" data-color="#fff" style="font-size: 49px;"></div>
                  <h5 class="description-header">'.count($listaSessao).'</h5>
                  <span class="description-text">Visitantes Unicos</span>
                </div>
                <!-- /.description-block -->
                <div class="description-block margin-bottom">
                  <div class="fa fa-bar-chart" data-color="#fff" style="font-size: 49px;"></div>
                  <h5 class="description-header">'.$acessos.'</h5>
                  <span class="description-text">Pag acessadas</span>
                </div>
                <!-- /.description-block -->
                <div class="description-block">
                  <div class="fa fa-file-o" data-color="#fff" style="font-size: 49px;"></div>
                  <h5 class="description-header">'.$ultimoAcessoPag.'</h5>
                  <span class="description-text">Ultima pag acessada</span>
                </div>
                <!-- /.description-block -->
                <div class="description-block">
                  <div class="fa fa-clock-o" data-color="#fff" style="font-size: 49px;"></div>
                  <h5 class="description-header">'.$ultimoAcesso.'</h5>
                  <span class="description-text">Último acesso</span>
                </div>
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.box-body -->
      </div>
      </div>';



$html = new template;
$html->set_html($cod);

$cod = '';
$html->footer_script = $cod;
$html->get_html('Inicio', 'Resumo');




?>

<script type="text/javascript">
	$(function(){




	  //-----------------
	  //- SPARKLINE BAR -
	  //-----------------
	  $('.sparkbar').each(function () {
	    var $this = $(this);
	    $this.sparkline('html', {
	      type: 'bar',
	      height: $this.data('height') ? $this.data('height') : '30',
	      barColor: $this.data('color')
	    });
	  });
	  //-----------------
	  //- SPARKLINE PIE -
	  //-----------------
	  $('.sparkpie').each(function () {
	    var $this = $(this);
	    $this.sparkline('html', {
	      type: 'pie',
	      height: $this.data('height') ? $this.data('height') : '90',
	      sliceColors: $this.data('color')
	    });
	  });

	  //------------------
	  //- SPARKLINE LINE -
	  //------------------
	  $('.sparkline').each(function () {
	    var $this = $(this);
	    $this.sparkline('html', {
	      type: 'line',
	      height: $this.data('height') ? $this.data('height') : '90',
	      width: '100%',
	      lineColor: $this.data('linecolor'),
	      fillColor: $this.data('fillcolor'),
	      spotColor: $this.data('spotcolor')
	    });
	  });
	});
</script>