<?php


class template{

	public $title_pre 			= NOME_EMPRESA.' | ';
	public $title 				= 'Escritório Virtual';
	private $html;
	public $footer_script		= null;   

	private function get_top(){
		if(!isset($_COOKIE['email']) or !isset($_COOKIE['filial'])){
			$cod = redirecionar('index.php?e='.request_uri(),0);
			exit('Você não está logado.');
		}

		if(!isset($_COOKIE['sidebar'])){
			$_COOKIE['sidebar'] = null;
		}

		$cod = '
			<!DOCTYPE html>
			<html>
			<head>
			  <meta charset="utf-8">
			  <meta http-equiv="X-UA-Compatible" content="IE=edge">
			  <title>'.$this->title_pre.$this->title.'</title>
			  <link rel="shortcut icon" href="../images/favicon.png">
			  <!-- Tell the browser to be responsive to screen width -->
			  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
			  <!-- Bootstrap 3.3.5 -->
			  <link rel="stylesheet" href="plugins/AdminLTE-master/bootstrap/css/bootstrap.min.css">
			  <!-- Font Awesome -->
			  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
			  <!-- Ionicons -->
			  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
			  <!-- Theme style -->
			  <link rel="stylesheet" href="plugins/AdminLTE-master/dist/css/AdminLTE.min.css">
			  <link rel="stylesheet" href="plugins/AdminLTE-master/plugins/select2/select2.min.css">
			  <!-- Date Picker -->
  		 	  <link rel="stylesheet" href="plugins/AdminLTE-master/plugins/datepicker/datepicker3.css">
  		 	  <!-- bootstrap wysihtml5 - text editor -->
  			  <link rel="stylesheet" href="plugins/AdminLTE-master/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
			  
			  <!-- style pessoal editado -->
			  <link rel="stylesheet" href="css/style.css">
			  <!-- AdminLTE Skins. Choose a skin from the css/skins
			  folder instead of downloading all of them to reduce the load. -->
			  <link rel="stylesheet" href="plugins/AdminLTE-master/dist/css/skins/_all-skins.min.css">


			  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
			  <!-- WARNING: Respond.js doesn\'t work if you view the page via file:// -->
			  <!--[if lt IE 9]>
			  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
			  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
			  <![endif]-->

			  <!-- jQuery 2.2.0 -->
	          <script src="plugins/AdminLTE-master/plugins/jQuery/jQuery-2.2.0.min.js"></script>
	          <!-- Bootstrap 3.3.5 -->
	          <script src="plugins/AdminLTE-master/bootstrap/js/bootstrap.min.js"></script>
	          <!-- ChartJS 1.0.1 -->
	    	  <script src="plugins/AdminLTE-master/plugins/chartjs/Chart.min.js"></script>

	    	  <!-- mascara -->
	    	  <script src="js/mascara.js"></script>

			  
			</head>
			<body class="hold-transition skin-blue sidebar-mini '.$_COOKIE['sidebar'].'">
			<!-- Site wrapper -->
  			<div class="wrapper">
		';


		return $cod;
	}

	private function get_footer(){
		$cod = '
			</section>
			<!-- /.content -->
			</div>
			<!-- /.content-wrapper -->

			<footer class="main-footer">
			  <div class="pull-right hidden-xs">
			    <b>Versão</b> '.$this->get_version().'
			  </div>
			  <strong>Copyright &copy; 2011-'.date('Y').' <a href="http://www.rocketsolution.com.br">Rocket Solution</a></strong> 
			</footer>
			
	        
	        <!-- DataTables -->
	        <script src="plugins/AdminLTE-master/plugins/datatables/jquery.dataTables.min.js"></script>
			<script src="plugins/AdminLTE-master/plugins/datatables/dataTables.bootstrap.min.js"></script>
	        
	        <!-- FastClick -->
	        <script src="plugins/AdminLTE-master/plugins/fastclick/fastclick.js"></script>
	        <!-- AdminLTE App -->
	        <script src="plugins/AdminLTE-master/dist/js/app.min.js"></script>
	        <!-- AdminLTE for demo purposes -->
	        <script src="plugins/AdminLTE-master/dist/js/demo.js"></script>

	        <!-- Autocomplite -->
	        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
			<link rel="stylesheet" href="https://jqueryui.com/resources/demos/style.css">
			<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

			<!-- Bootstrap WYSIHTML5 -->
			<script src="plugins/AdminLTE-master/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
	        <!-- Select2 -->
			<script src="plugins/AdminLTE-master/plugins/select2/select2.full.min.js"></script>
			<!-- InputMask -->
			<script src="plugins/AdminLTE-master/plugins/input-mask/jquery.inputmask.js"></script>
			<script src="plugins/AdminLTE-master/plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
			<script src="plugins/AdminLTE-master/plugins/input-mask/jquery.inputmask.extensions.js"></script>
			<!-- datepicker -->
			<script src="plugins/AdminLTE-master/plugins/datepicker/bootstrap-datepicker.js"></script>
			<!-- bootstrap color picker -->
			<script src="plugins/AdminLTE-master/plugins/colorpicker/bootstrap-colorpicker.min.js"></script>
			<!-- bootstrap time picker -->
			<script src="plugins/AdminLTE-master/plugins/timepicker/bootstrap-timepicker.min.js"></script>
			<!-- SlimScroll 1.3.0 -->
			<script src="plugins/AdminLTE-master/plugins/slimScroll/jquery.slimscroll.min.js"></script>
			<!-- iCheck 1.0.1 -->
			<script src="plugins/AdminLTE-master/plugins/iCheck/icheck.min.js"></script>
			<!-- Page script -->			
			<script>
			  $(function () {
			    //Initialize Select2 Elements
			    $(".select2").select2();

			    //Datemask dd/mm/yyyy
			    //$("#datemask").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/aaaa"});
			    //Datemask2 mm/dd/yyyy
			    //$("#datemask2").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/aaaa"});
			    //Money Euro
			    $("[data-mask]").inputmask();

			    //iCheck for checkbox and radio inputs
			    $(\'input[type="checkbox"].minimal, input[type="radio"].minimal\').iCheck({
			      checkboxClass: \'icheckbox_minimal-blue\',
			      radioClass: \'iradio_minimal-blue\'
			    });
			    //Red color scheme for iCheck
			    $(\'input[type="checkbox"].minimal-red, input[type="radio"].minimal-red\').iCheck({
			      checkboxClass: \'icheckbox_minimal-red\',
			      radioClass: \'iradio_minimal-red\'
			    });
			    //Flat red color scheme for iCheck
			    $(\'input[type="checkbox"].flat-red, input[type="radio"].flat-red\').iCheck({
			      checkboxClass: \'icheckbox_flat-green\',
			      radioClass: \'iradio_flat-green\'
			    });

			    //Colorpicker
			    $(".my-colorpicker1").colorpicker();
			    //color picker with addon
			    $(".my-colorpicker2").colorpicker();

			    //Timepicker
			    $(".timepicker").timepicker({
			      showInputs: false
			    });

			    //bootstrap WYSIHTML5 - text editor
    			$(".textarea").wysihtml5();
			  });

			  function sidebar(){
			  	if(getCookie("sidebar")==""){
			  		setCookie("sidebar", "sidebar-collapse", 7);
			  	}else{
			  		setCookie("sidebar", "", 7);
			  	}
			  }

			  function getCookie(cname) {
			    var name = cname + "=";
			    var decodedCookie = decodeURIComponent(document.cookie);
			    var ca = decodedCookie.split(\';\');
			    for(var i = 0; i <ca.length; i++) {
			        var c = ca[i];
			        while (c.charAt(0) == \' \') {
			            c = c.substring(1);
			        }
			        if (c.indexOf(name) == 0) {
			            return c.substring(name.length, c.length);
			        }
			    }
			    return "";
			   }
			   function setCookie(cname, cvalue, exdays) {
			    var d = new Date();
			    d.setTime(d.getTime() + (exdays*24*60*60*1000));
			    var expires = "expires="+ d.toUTCString();
			    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
			   }
			</script>


	        
	        '.$this->footer_script.'
			</body>
			</html>
		';
		return $cod;
	}


	private function get_header(){

		$id 			= base64_encode($this->template_usuario($_COOKIE['email'], 'id'));
		$nome 			= $this->template_usuario($_COOKIE['email'], 'nome');
		$data 		 	= 'Membro desde '.formataData($this->template_usuario($_COOKIE['email'], 'data_cadastro'));
		$funcao 		= registro($this->template_usuario($_COOKIE['email'], 'id_funcao'), 'usuario_funcao', 'nome');
		$inicio 		= 'index-usuario.php';
		$link 			= 'cadastrar-usuario.php';
		if($this->template_usuario($_COOKIE['email'], 'foto') != ''){
			$img_user 		= $this->template_usuario($_COOKIE['email'], 'foto');
		}else{
			$img_user 		= 'plugins/AdminLTE-master/dist/img/user2-160x160-b.jpg';
		}
		


		$cod = '
			<header class="main-header">
		      <!-- Logo -->
		      <a href="'.$inicio.'" class="logo">
		        <!-- mini logo for sidebar mini 50x50 pixels -->
		        <span class="logo-mini">'.NOME_EMPRESA_SIGLA.'</span>
		        <!-- logo for regular state and mobile devices -->
		        <span class="logo-lg"><b>'.NOME_EMPRESA_BLACK.'</b> '.NOME_EMPRESA_LIGHT.'</span>
		        <!--<span class="logo-lg"><img src="img/logo-white.png" style="width:96px;"></span>-->
		      </a>
		      <!-- Header Navbar: style can be found in header.less -->
		      <nav class="navbar navbar-static-top" role="navigation">
		        <!-- Sidebar toggle button-->
		        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button" onclick="sidebar();">
		          <span class="sr-only">Toggle navigation</span>
		          <span class="icon-bar"></span>
		          <span class="icon-bar"></span>
		          <span class="icon-bar"></span>
		        </a>

		        <div class="navbar-custom-menu">
		          <ul class="nav navbar-nav">
		          	';
		          	

		$conn = TConnection::open(DB);
		$criterio1 = new TCriteria();
        $criterio1->add(new TFilter('id_usuario_send', '=', base64_decode($id)));
        $criterio1->add(new TFilter('id_usuario_receive', '=', base64_decode($id)), TExpression::OR_OPERATOR);
        $criterio1->add(new TFilter('id_usuario_receive', '=', 0), TExpression::OR_OPERATOR);

        $criterio2 = new TCriteria();
        $criterio2->add($criterio1);
        $criterio2->add(new TFilter('data_inicio', '>=', date('Y-m-d 00:00:00')));
        $criterio2->add(new TFilter('data_inicio', '<=', date('Y-m-d 23:59:59')));

        $sql = new TSqlSelect();
        $sql->setEntity('agenda');
        $sql->addColumn('texto');
        $sql->addColumn('data_inicio');
        $sql->setCriteria($criterio2);
        
        $result = $conn->query($sql->getInstruction());
        $agenda_cod = '';
        $agenda_total_itens = $result->rowCount();
        for($i=0; $i<$result->rowCount();$i++){
        	extract($result->fetch(PDO::FETCH_ASSOC));
        	$agenda_cod .= '<li class="header">
			                    <a href="#">
			                    	<i class="fa fa-hourglass-half"></i>'.formataData($data_inicio, true).' '.$texto.'
			                	</a>
			                </li>';
        }

		
		
		$cod.='<li class="dropdown notifications-menu" id="calend" style="height: 50px;">
		            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" style="height: 50px;">
		              <i class="fa fa-calendar"></i>
		              <span class="label label-danger">'.$agenda_total_itens.'</span>
		            </a>
		            <ul class="dropdown-menu">
			          '.$agenda_cod.'
		              <li class="footer"><a href="agenda.php">Clique aqui para visualizar o calendário</a></li>
		            </ul>
		         </li>';

		$filial = registro($_COOKIE['filial'], 'filiais', 'nome');
     	$cod.='<li class="dropdown user user-menu">
	              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
	                <span class="hidden-xs">'.$filial.'</span>
	              </a>
	            </li>';


		$cod.='
		            <!-- User Account: style can be found in dropdown.less -->
		            <li class="dropdown user user-menu">
		              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
		                <img src="'.$img_user.'" class="user-image" alt="User Image">
		                <span class="hidden-xs">'.$nome.'</span>
		              </a>
		              <ul class="dropdown-menu">
		                <!-- User image -->
		                <li class="user-header">
		                  <img src="'.$img_user.'" class="img-circle" alt="User Image">

		                  <p>
		                    '.$nome.' - '.$funcao.'
		                    <small>'.$data.'</small>
		                  </p>
		                </li>
		                
		                <!-- Menu Footer-->
		                <li class="user-footer">
		                  <div class="pull-left">
		                    <a href="'.$link.'?op=visualizar&id='.$id.'" class="btn btn-default btn-flat">Perfil</a>
		                  </div>
		                  <div class="pull-right">
		                    <a href="logout.php" class="btn btn-default btn-flat">Sair</a>
		                  </div>
		                </li>
		              </ul>
		            </li>
		            
		          </ul>
		        </div>
		      </nav>
		    </header>
		';
		return $cod;
	}

	private function get_menu(){

	
		$nome 			= $this->template_usuario($_COOKIE['email'], 'nome');
		$id_usuario		= $this->template_usuario($_COOKIE['email'], 'id');
		if($this->template_usuario($_COOKIE['email'], 'foto') != ''){
			$img_user 		= $this->template_usuario($_COOKIE['email'], 'foto');
		}else{
			$img_user 		= 'plugins/AdminLTE-master/dist/img/user2-160x160-b.jpg';
		}


	
		$conn = TConnection::open(DB);

		$cod = '
			<!-- Left side column. contains the sidebar -->
		    <aside class="main-sidebar">
		      <!-- sidebar: style can be found in sidebar.less -->
		      <section class="sidebar">
		        <!-- Sidebar user panel -->
		        <div class="user-panel">
		          <div class="pull-left image">
		            <img src="'.$img_user.'" class="img-circle" alt="User Image">
		          </div>
		          <div class="pull-left info">
		            <p>'.$nome.'</p>
		            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
		          </div>
		        </div>
		        <!-- sidebar menu: : style can be found in sidebar.less -->
		        <ul class="sidebar-menu">
		          <li class="header">Menu</li>';

		$array_ferramentas 			= array('Comercial', 'Gestão', 'Finanças', 'Relatório', 'Sistema');
		$array_ferramentas_icones 	= array('fa-cog', 'fa-line-chart', 'fa-dollar', 'fa-bar-chart', 'fa-wrench');

	
		for($x = 0; $x < count($array_ferramentas); $x++){
				
			$variavel = explode('/', $_SERVER['PHP_SELF']);
			$variavel = end($variavel);

			$criterio = new TCriteria;
			$criterio->add(new TFilter('link', '=', $variavel));
	        $criterio->add(new TFilter('hierarquia', '=', strtolower($array_ferramentas[$x]), unserialize(CHAR_MAP)));

			$sql = new TSqlSelect;
			$sql->setEntity('sistema_ferramentas');
			$sql->addColumn('id');
			$sql->setCriteria($criterio);
			$result = $conn->query($sql->getInstruction());

			if($result->rowCount()){
				$menu = 'active';
			}else{
				$menu = null;
			}

			$cod.='
			<li class="treeview '.$menu.'">
		        <a href="#">
		          <i class="fa '.$array_ferramentas_icones[$x].'"></i> <span>'.$array_ferramentas[$x].'</span>
		          <i class="fa fa-angle-left pull-right"></i>
		        </a>
		        <ul class="treeview-menu">
		        ';

	        $criterio = new TCriteria;
	        $criterio->add(new TFilter('hierarquia', '=', strtolower($array_ferramentas[$x]), unserialize(CHAR_MAP)));
			$criterio->setProperty('order', 'sequencia');
			$criterio->setProperty('group', 'agrupamento');

			$sql_sistema_ferramenta = new TSqlSelect;
			$sql_sistema_ferramenta->setEntity('sistema_ferramentas');
			$sql_sistema_ferramenta->addColumn('id as id_sistema_ferramentas, agrupamento, nome, link');
			$sql_sistema_ferramenta->setCriteria($criterio);

			$result_sistema_ferramenta = $conn->query($sql_sistema_ferramenta->getInstruction());

			for($i = 0; $i < $result_sistema_ferramenta->rowCount(); $i++){

				extract($result_sistema_ferramenta->fetch(PDO::FETCH_ASSOC));

				$criterio = new TCriteria;
				$criterio->add(new TFilter('link', '=', $variavel));
		        $criterio->add(new TFilter('agrupamento', '=', $agrupamento));

				$sql = new TSqlSelect;
				$sql->setEntity('sistema_ferramentas');
				$sql->addColumn('id');
				$sql->setCriteria($criterio);
				$result = $conn->query($sql->getInstruction());

				if($result->rowCount()){
					$menu = 'class="active"';
				}else{
					$menu = null;
				}

				if($nome){

					$cod.='<li '.$menu.'><a href="#"><i class="fa fa-circle-o"></i> '.$agrupamento.'<i class="fa fa-angle-left pull-right"></i></a>';
					$cod.='<ul class="treeview-menu">';

					$criterio = new TCriteria;
					$criterio->add(new TFilter('agrupamento', '=', $agrupamento));

					$sql = new TSqlSelect;
					$sql->setEntity('sistema_ferramentas');
					$sql->addColumn('id as id_sistema_ferramentas');
					$sql->setCriteria($criterio);
					$result_agrupamento = $conn->query($sql->getInstruction());

					for($j = 0; $j < $result_agrupamento->rowCount(); $j++){


						extract($result_agrupamento->fetch(PDO::FETCH_ASSOC));

						$criterio = new TCriteria;
						$criterio->add(new TFilter('id_usuario', '=', $id_usuario));
						$criterio->add(new TFilter('id_sistema_ferramentas', '=', $id_sistema_ferramentas));

						$sql = new TSqlSelect;
						$sql->setEntity('usuario_credenciais');
						$sql->addColumn('id_sistema_ferramentas as ferramenta');
						$sql->setCriteria($criterio);

						$result = $conn->query($sql->getInstruction());
						if($result->rowCount()){
							extract($result->fetch(PDO::FETCH_ASSOC));
							$link = registro($ferramenta, 'sistema_ferramentas', 'link');
							$nome = registro($ferramenta, 'sistema_ferramentas', 'nome');
							$cod.='<li><a href="'.$link.'"><i class="fa fa-circle-o"></i>'.$nome.'</a></li>';
						}

					}

					$cod.='</ul></li>';

				}else{
					$cod.='<li '.$menu.'><a href="'.$link.'"><i class="fa fa-circle-o"></i> '.$agrupamento.'<i class="fa fa-angle-left pull-right"></i></a>';
				}



				

				
			}


			$cod.='
			</ul>
			</li>
			';
		}


		$cod.='
		         

		        </ul>
		      </section>
		      <!-- /.sidebar -->
		    </aside>
		';
		
		return $cod;
		
	}

	

	public function set_html($cod){
		$this->html = $cod;
	}

	public function get_html($pag, $descricao){

		$link = request_uri();



		$cod  = $this->get_top();
		$cod .= $this->get_header();
		$cod .= $this->get_menu();
		$cod .= '
			<!-- Content Wrapper. Contains page content -->
		    <div class="content-wrapper">
		      <!-- Content Header (Page header) -->
		      <section class="content-header">
		        <h1>
		          <a href="'.$link.'">'.$pag.'</a>
		          <small>> '.$descricao.'</small>
		        </h1>
		      </section>



		      <!-- Main content -->
		      <section class="content">
		';
		$cod .= $this->html;
		$cod .= $this->get_footer();
		echo $cod;
	}

	private function get_version(){
		return '1.0';
	}

	public function set_box($titulo, $body, $footer = null){
		$cod = '
			<!-- Default box -->
      		<div class="box">
	        	<div class="box-header with-border">
	          		<h3 class="box-title">'.$titulo.'</h3>
			          <div class="box-tools pull-right">
			            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
			              <i class="fa fa-minus"></i></button>
			            <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
			              <i class="fa fa-times"></i></button>
			          </div>
	        	</div>
		        <div class="box-body">
		          '.$body.'
		        </div>
		        <!-- /.box-body -->
		        <div class="box-footer">
		          '.$footer.'
		        </div>
	        	<!-- /.box-footer-->
	        </div>
	        <!-- /.box -->
      	
		';
		return $cod;
	}

	private function template_usuario($email, $column){
		$conn = TConnection::open(DB);

		$criterio = new TCriteria;
		$criterio->add(new TFilter('email', '=', $email));

		$sql = new TSqlSelect;
		$sql->setEntity('usuario');
		$sql->addColumn($column);
		$sql->setCriteria($criterio);
		$result = $conn->query($sql->getInstruction());

		if($result->rowCount()){
			extract($result->fetch(PDO::FETCH_ASSOC));
			return $$column;
		}
	}
	
	private function template_cliente($cpf, $column){
		$conn = TConnection::open(DB);

		$criterio = new TCriteria;
		$criterio->add(new TFilter('cpf', '=', $cpf));

		$sql = new TSqlSelect;
		$sql->setEntity('cliente');
		$sql->addColumn($column);
		$sql->setCriteria($criterio);
		$result = $conn->query($sql->getInstruction());

		if($result->rowCount()){
			extract($result->fetch(PDO::FETCH_ASSOC));
			return $$column;
		}

	}

	
}













?>