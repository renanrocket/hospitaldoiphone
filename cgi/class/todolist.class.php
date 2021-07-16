<?php

class todolist{

	public $id_usuario;

	function __construct(){
		
		$usu = new usuario;
		$this->id_usuario = $usu->get_usuario('id');


	}

	function get_todolist(){

		$cod = '
		<div class="row">
			<div class="col-xs-12">
				<div class="box">
					<!-- TO DO List -->
					<div class="box box-primary">
						<div class="box-header">
							<i class="ion ion-clipboard"></i>

							<h3 class="box-title">Lista de Tarefas</h3>
							
							<div class="box-tools pull-right">
								<ul class="pagination pagination-sm inline">
									<!--<li><a href="#">&laquo;</a></li>-->
									<li><a href="#" onclick="tarefa_show(\'ativo\')" style="background-color:#f39c12; color:white;"><span class="fa fa-circle-o"></span></a></li>
									<li><a href="#" onclick="tarefa_show(\'inativo\')" style="background-color:#00a65a; color:white;"><span class="fa fa-check"></span></a></li>
									<!--<li><a href="#">&raquo;</a></li>-->
								</ul>
							</div>
						</div>
						<!-- /.box-header -->
						<div class="box-body">';
		
		

		$conn = TConnection::open(DB);



		###ATIVO###

		$cod.= '<ul class="todo-list" id="tarefa_ativo">';

		if(registro($this->id_usuario, 'usuario', 'id_funcao') == 1){

			$criterio = new TCriteria;
			$criterio->add(new TFilter('status', '=', 1)); //tarefa no status de aberta
			$criterio->add(new TFilter('status', '=', 2), TExpression::OR_OPERATOR); //tarefa no status de em produção
			$criterio->setProperty('order', 'data_fazer');

			$sql = new TSqlSelect;
			$sql->setEntity('tarefas');
			$sql->addColumn('*');
			$sql->setCriteria($criterio);
			$result = $conn->query($sql->getInstruction());
			
			for($i=0; $i<$result->rowCount(); $i++){
				extract($result->fetch(PDO::FETCH_ASSOC));

				$pop = new popup(null, 'amarelo');
			    $pop->display = 'none';
			    $texto = 'Você tem certeza que deseja deletar esta tarefa?<br><br>';
			    $texto.= '<a href="#" onclick="tarefa_deletar('.$id.');" class="btn btn-danger">Sim</a>';
			    $texto.= '<a href="#" onclick="'.$pop->get_action_hide().'" class="btn btn-success pull-right">Não</a>';
			    $pop->texto = $texto;

			    $cod.= $pop->get_pop();

				$tempo = subtrairDatas(date('Y-m-d H:i:s'), $data_fazer);
				if($tempo>5){
					$tempo.= ' dias';
					$label = 'label-success';
				}elseif($tempo>0){
					$tempo.= ' dias';
					$label = 'label-warning';
				}elseif($tempo==0){
					$tempo = 'Hoje';
					$label = 'label-warning';
				}else{
					$tempo = 'Atrasado '.$tempo.' dias';
					$label = 'label-danger';
				}

				if($riscado == 1){
					$riscado = 'class="done"';
					$checked = 'checked="yes"';
				}else{
					$riscado = null;
					$checked = null;
				}
				

				$nome_usuario_criacao = explode(' ', registro($id_usuario_criacao, 'usuario', 'Nome'));
				$nome_usuario_fazer = explode(' ', registro($id_usuario_fazer, 'usuario', 'Nome'));
				$color_criacao = registro($id_usuario_criacao, 'usuario', 'telefone');
				$color_criacao != null ?  $color_criacao = $color_criacao[12].$color_criacao[13].$color_criacao[14] : $color_criacao = 000;
				$color_fazer = registro($id_usuario_fazer, 'usuario', 'telefone');
				$color_fazer != null ?  $color_fazer = $color_fazer[12].$color_fazer[13].$color_fazer[14] : $color_fazer = 000;


				$obs = '<b style="color: #'.$color_criacao.';">'.$nome_usuario_criacao[0].'</b> -> <b style="color: #'.$color_fazer.';">'.$nome_usuario_fazer[0].'</b> - ';

				$cod.= '
				
				<li id="li_'.$id.'" '.$riscado.'>
					<!-- drag handle -->
					<span class="handle">
						<i class="fa fa-ellipsis-v"></i>
						<i class="fa fa-ellipsis-v"></i>
					</span>
					<!-- checkbox -->
					<input type="checkbox" id="tarefa_'.$id.'" onclick="tarefa_riscado(this.id);" '.$checked.'>
					<!-- todo text -->
					<span class="text">'.$obs.' '.$tarefa.'</span>
					<!-- Emphasis label -->
					<small class="label '.$label.'"><i class="fa fa-clock-o"></i> '.$tempo.'</small>
					<!-- General tools such as edit or delete-->
					<div class="tools">
						<a href="tarefa.php?op=visualizar&id='.base64_encode($id).'"><i class="fa fa-edit"></i></a>
						<a href="#" onclick="'.$pop->get_action_show().'"><i class="fa fa-trash-o"></i></a>
					</div>
				</li>';
				

				
				


			}

		}else{

			$criterio = new TCriteria;
			$criterio->add(new TFilter('id_usuario_fazer', '=', $this->id_usuario));
			$criterio2 = new TCriteria;
			$criterio2->add(new TFilter('status', '=', 1)); //tarefa no status de aberta
			$criterio2->add(new TFilter('status', '=', 2), TExpression::OR_OPERATOR); //tarefa no status de em produção
			$criterio->add($criterio2);
			$criterio->setProperty('order', 'data_fazer');

			$sql = new TSqlSelect;
			$sql->setEntity('tarefas');
			$sql->addColumn('*');
			$sql->setCriteria($criterio);
			$result = $conn->query($sql->getInstruction());
			
			for($i=0; $i<$result->rowCount(); $i++){
				extract($result->fetch(PDO::FETCH_ASSOC));

				$pop = new popup(null, 'amarelo');
			    $pop->display = 'none';
			    $texto = 'Você tem certeza que deseja deletar esta tarefa?<br><br>';
			    $texto.= '<a href="#" onclick="tarefa_deletar('.$id.');" class="btn btn-danger">Sim</a>';
			    $texto.= '<a href="#" onclick="'.$pop->get_action_hide().'" class="btn btn-success pull-right">Não</a>';
			    $pop->texto = $texto;

			    $cod.= $pop->get_pop();

				$tempo = subtrairDatas(date('Y-m-d H:i:s'), $data_fazer);
				if($tempo>5){
					$tempo.= ' dias';
					$label = 'label-success';
				}elseif($tempo>0){
					$tempo.= ' dias';
					$label = 'label-warning';
				}elseif($tempo==0){
					$tempo = 'Hoje';
					$label = 'label-warning';
				}else{
					$tempo = 'Atrasado '.$tempo.' dias';
					$label = 'label-danger';
				}

				if($riscado == 1){
					$riscado = 'class="done"';
					$checked = 'checked="yes"';
				}else{
					$riscado = null;
					$checked = null;
				}

				if($id_usuario_criacao != $id_usuario_fazer){
					$nome_usuario = explode(' ', registro($id_usuario_criacao, 'usuario', 'Nome'));

					$obs = '(Criado por '.$nome_usuario[0].')';
				}else{
					$obs = null;
				}

				$cod.= '
				
				<li id="li_'.$id.'" '.$riscado.'>
					<!-- drag handle -->
					<span class="handle">
						<i class="fa fa-ellipsis-v"></i>
						<i class="fa fa-ellipsis-v"></i>
					</span>
					<!-- checkbox -->
					<input type="checkbox" id="tarefa_'.$id.'" onclick="tarefa_riscado(this.id);" '.$checked.'>
					<!-- todo text -->
					<span class="text">'.$tarefa.' '.$obs.'</span>
					<!-- Emphasis label -->
					<small class="label '.$label.'"><i class="fa fa-clock-o"></i> '.$tempo.'</small>
					<!-- General tools such as edit or delete-->
					<div class="tools">
						<a href="tarefa.php?op=visualizar&id='.base64_encode($id).'"><i class="fa fa-edit"></i></a>
						<a href="#" onclick="'.$pop->get_action_show().'"><i class="fa fa-trash-o"></i></a>
					</div>
				</li>';
				

				
				


			}



			//ADMINISTRADOR

			$criterio = new TCriteria;
			$criterio->add(new TFilter('id_usuario_criacao', '=', $this->id_usuario));
			$criterio->add(new TFilter('id_usuario_fazer', '<>', $this->id_usuario));
			$criterio2 = new TCriteria;
			$criterio2->add(new TFilter('status', '=', 1)); //tarefa no status de aberta
			$criterio2->add(new TFilter('status', '=', 2), TExpression::OR_OPERATOR); //tarefa no status de em produção
			$criterio->add($criterio2);
			$criterio->setProperty('order', 'data_fazer');

			$sql = new TSqlSelect;
			$sql->setEntity('tarefas');
			$sql->addColumn('*');
			$sql->setCriteria($criterio);
			$result = $conn->query($sql->getInstruction());
			
			for($i=0; $i<$result->rowCount(); $i++){
				extract($result->fetch(PDO::FETCH_ASSOC));

				$pop = new popup(null, 'amarelo');
			    $pop->display = 'none';
			    $texto = 'Você tem certeza que deseja deletar esta tarefa?<br><br>';
			    $texto.= '<a href="#" onclick="tarefa_deletar('.$id.');" class="btn btn-danger">Sim</a>';
			    $texto.= '<a href="#" onclick="'.$pop->get_action_hide().'" class="btn btn-success pull-right">Não</a>';
			    $pop->texto = $texto;

			    $cod.= $pop->get_pop();

				$tempo = subtrairDatas(date('Y-m-d H:i:s'), $data_fazer);
				if($tempo>5){
					$tempo.= ' dias';
					$label = 'label-success';
				}elseif($tempo>0){
					$tempo.= ' dias';
					$label = 'label-warning';
				}elseif($tempo==0){
					$tempo = 'Hoje';
					$label = 'label-warning';
				}else{
					$tempo = 'Atrasado '.$tempo.' dias';
					$label = 'label-danger';
				}

				if($riscado == 1){
					$riscado = 'class="done"';
					$checked = 'checked="yes"';
				}else{
					$riscado = null;
					$checked = null;
				}

				$nome_usuario = explode(' ', registro($id_usuario_fazer, 'usuario', 'Nome'));
				$obs = '<b>'.$nome_usuario[0].'</b> - ';
				

				$cod.= '
				
				<li id="li_'.$id.'" '.$riscado.'>
					<!-- drag handle -->
					<span class="handle">
						<i class="fa fa-ellipsis-v"></i>
						<i class="fa fa-ellipsis-v"></i>
					</span>
					<!-- checkbox -->
					<input type="checkbox" id="tarefa_'.$id.'" onclick="tarefa_riscado(this.id);" '.$checked.'>
					<!-- todo text -->
					<span class="text">'.$obs.' '.$tarefa.'</span>
					<!-- Emphasis label -->
					<small class="label '.$label.'"><i class="fa fa-clock-o"></i> '.$tempo.'</small>
					<!-- General tools such as edit or delete-->
					<div class="tools">
						<a href="tarefa.php?op=visualizar&id='.base64_encode($id).'"><i class="fa fa-edit"></i></a>
						<a href="#" onclick="'.$pop->get_action_show().'"><i class="fa fa-trash-o"></i></a>
					</div>
				</li>';
				

				
				


			}

		}


		$cod.= '</ul>';




		###INATIVO###


		$cod.= '<ul class="todo-list" id="tarefa_inativo" style="display:none;">';
		

		$conn = TConnection::open(DB);


		if(registro($this->id_usuario, 'usuario', 'id_funcao') == 1){

			$criterio = new TCriteria;
			$criterio->add(new TFilter('status', '!=', 1)); //tarefa no status de aberta
			$criterio->add(new TFilter('status', '!=', 2)); //tarefa no status de em produção
			$criterio->setProperty('order', 'data_fazer desc');
			$criterio->setProperty('limit', '20');

			$sql = new TSqlSelect;
			$sql->setEntity('tarefas');
			$sql->addColumn('*');
			$sql->setCriteria($criterio);
			$result = $conn->query($sql->getInstruction());
			
			for($i=0; $i<$result->rowCount(); $i++){
				extract($result->fetch(PDO::FETCH_ASSOC));

				$pop = new popup(null, 'amarelo');
			    $pop->display = 'none';
			    $texto = 'Você tem certeza que deseja deletar esta tarefa?<br><br>';
			    $texto.= '<a href="#" onclick="tarefa_deletar('.$id.');" class="btn btn-danger">Sim</a>';
			    $texto.= '<a href="#" onclick="'.$pop->get_action_hide().'" class="btn btn-success pull-right">Não</a>';
			    $pop->texto = $texto;

			    $cod.= $pop->get_pop();

				$tempo = subtrairDatas(date('Y-m-d H:i:s'), $data_fazer);
				if($tempo>5){
					$tempo.= ' dias';
					$label = 'label-success';
				}elseif($tempo>0){
					$tempo.= ' dias';
					$label = 'label-warning';
				}elseif($tempo==0){
					$tempo = 'Hoje';
					$label = 'label-warning';
				}else{
					$tempo = 'Atrasado '.$tempo.' dias';
					$label = 'label-danger';
				}

				if($riscado == 1){
					$riscado = 'class="done"';
					$checked = 'checked="yes"';
				}else{
					$riscado = null;
					$checked = null;
				}

				$nome_usuario_criacao = explode(' ', registro($id_usuario_criacao, 'usuario', 'Nome'));
				$nome_usuario_fazer = explode(' ', registro($id_usuario_fazer, 'usuario', 'Nome'));
				$color_criacao = registro($id_usuario_criacao, 'usuario', 'telefone');
				$color_criacao != null ?  $color_criacao = $color_criacao[12].$color_criacao[13].$color_criacao[14] : $color_criacao = 000;
				$color_fazer = registro($id_usuario_fazer, 'usuario', 'telefone');
				$color_fazer != null ?  $color_fazer = $color_fazer[12].$color_fazer[13].$color_fazer[14] : $color_fazer = 000;


				$obs = '<b style="color: #'.$color_criacao.';">'.$nome_usuario_criacao[0].'</b> -> <b style="color: #'.$color_fazer.';">'.$nome_usuario_fazer[0].'</b> - ';

				$cod.= '
				
				<li id="li_'.$id.'" '.$riscado.'>
					<!-- drag handle -->
					<span class="handle">
						<i class="fa fa-ellipsis-v"></i>
						<i class="fa fa-ellipsis-v"></i>
					</span>
					<!-- checkbox -->
					<input type="checkbox" id="tarefa_'.$id.'" onclick="tarefa_riscado(this.id);" '.$checked.'>
					<!-- todo text -->
					<span class="text">'.$obs.' '.$tarefa.'</span>
					<!-- Emphasis label -->
					<small class="label '.$label.'"><i class="fa fa-clock-o"></i> '.$tempo.'</small>
					<!-- General tools such as edit or delete-->
					<div class="tools">
						<a href="tarefa.php?op=visualizar&id='.base64_encode($id).'"><i class="fa fa-edit"></i></a>
						<a href="#" onclick="'.$pop->get_action_show().'"><i class="fa fa-trash-o"></i></a>
					</div>
				</li>';
				

				
				


			}

		}else{


			$criterio = new TCriteria;
			$criterio->add(new TFilter('id_usuario_fazer', '=', $this->id_usuario));
			$criterio->add(new TFilter('status', '!=', 1)); //tarefa no status de aberta
			$criterio->add(new TFilter('status', '!=', 2)); //tarefa no status de em produção
			$criterio->setProperty('order', 'data_fazer');
			$criterio->setProperty('limit', '10');

			$sql = new TSqlSelect;
			$sql->setEntity('tarefas');
			$sql->addColumn('*');
			$sql->setCriteria($criterio);
			
			$result = $conn->query($sql->getInstruction());
			
			for($i=0; $i<$result->rowCount(); $i++){
				extract($result->fetch(PDO::FETCH_ASSOC));

				$pop = new popup(null, 'amarelo');
			    $pop->display = 'none';
			    $texto = 'Você tem certeza que deseja deletar esta tarefa?<br><br>';
			    $texto.= '<a href="#" onclick="tarefa_deletar('.$id.');" class="btn btn-danger">Sim</a>';
			    $texto.= '<a href="#" onclick="'.$pop->get_action_hide().'" class="btn btn-success pull-right">Não</a>';
			    $pop->texto = $texto;

			    $cod.= $pop->get_pop();

				$tempo = subtrairDatas(date('Y-m-d H:i:s'), $data_fazer);
				if($tempo>5){
					$tempo.= ' dias';
					$label = 'label-success';
				}elseif($tempo>0){
					$tempo.= ' dias';
					$label = 'label-warning';
				}elseif($tempo==0){
					$tempo = 'Hoje';
					$label = 'label-warning';
				}else{
					$tempo = 'Atrasado '.$tempo.' dias';
					$label = 'label-danger';
				}

				if($riscado == 1){
					$riscado = 'class="done"';
					$checked = 'checked="yes"';
				}else{
					$riscado = null;
					$checked = null;
				}

				if($id_usuario_criacao != $id_usuario_fazer){
					$nome_usuario = explode(' ', registro($id_usuario_criacao, 'usuario', 'Nome'));

					$obs = '(Criado por '.$nome_usuario[0].')';
				}else{
					$obs = null;
				}
				
				$cod.= '
				
				<li id="li_'.$id.'" '.$riscado.'>
					<!-- drag handle -->
					<span class="handle">
						<i class="fa fa-ellipsis-v"></i>
						<i class="fa fa-ellipsis-v"></i>
					</span>
					<!-- checkbox -->
					<input type="checkbox" id="tarefa_'.$id.'" onclick="tarefa_riscado(this.id);" '.$checked.'>
					<!-- todo text -->
					<span class="text">'.$tarefa.' '.$obs.'</span>
					<!-- Emphasis label -->
					<small class="label '.$label.'"><i class="fa fa-clock-o"></i> '.$tempo.'</small>
					<!-- General tools such as edit or delete-->
					<div class="tools">
						<a href="tarefa.php?op=visualizar&id='.base64_encode($id).'"><i class="fa fa-edit"></i></a>
						<a href="#" onclick="'.$pop->get_action_show().'"><i class="fa fa-trash-o"></i></a>
					</div>
				</li>';
				

				
				


			}


			$conn = TConnection::open(DB);

			$criterio = new TCriteria;
			$criterio->add(new TFilter('id_usuario_criacao', '=', $this->id_usuario));
			$criterio->add(new TFilter('id_usuario_fazer', '<>', $this->id_usuario));
			$criterio->add(new TFilter('status', '!=', 1)); //tarefa no status de aberta
			$criterio->add(new TFilter('status', '!=', 2)); //tarefa no status de em produção
			$criterio->setProperty('order', 'data_fazer');
			$criterio->setProperty('limit', '10');

			$sql = new TSqlSelect;
			$sql->setEntity('tarefas');
			$sql->addColumn('*');
			$sql->setCriteria($criterio);
			
			$result = $conn->query($sql->getInstruction());
			
			for($i=0; $i<$result->rowCount(); $i++){
				extract($result->fetch(PDO::FETCH_ASSOC));

				$pop = new popup(null, 'amarelo');
			    $pop->display = 'none';
			    $texto = 'Você tem certeza que deseja deletar esta tarefa?<br><br>';
			    $texto.= '<a href="#" onclick="tarefa_deletar('.$id.');" class="btn btn-danger">Sim</a>';
			    $texto.= '<a href="#" onclick="'.$pop->get_action_hide().'" class="btn btn-success pull-right">Não</a>';
			    $pop->texto = $texto;

			    $cod.= $pop->get_pop();

				$tempo = subtrairDatas(date('Y-m-d H:i:s'), $data_fazer);
				if($tempo>5){
					$tempo.= ' dias';
					$label = 'label-success';
				}elseif($tempo>0){
					$tempo.= ' dias';
					$label = 'label-warning';
				}elseif($tempo==0){
					$tempo = 'Hoje';
					$label = 'label-warning';
				}else{
					$tempo = 'Atrasado '.$tempo.' dias';
					$label = 'label-danger';
				}

				if($riscado == 1){
					$riscado = 'class="done"';
					$checked = 'checked="yes"';
				}else{
					$riscado = null;
					$checked = null;
				}

				$nome_usuario = explode(' ', registro($id_usuario_fazer, 'usuario', 'Nome'));

				$obs = '<b>'.$nome_usuario[0].'</b> - ';
				
				
				$cod.= '
				
				<li id="li_'.$id.'" '.$riscado.'>
					<!-- drag handle -->
					<span class="handle">
						<i class="fa fa-ellipsis-v"></i>
						<i class="fa fa-ellipsis-v"></i>
					</span>
					<!-- checkbox -->
					<input type="checkbox" id="tarefa_'.$id.'" onclick="tarefa_riscado(this.id);" '.$checked.'>
					<!-- todo text -->
					<span class="text">'.$obs.' '.$tarefa.'</span>
					<!-- Emphasis label -->
					<small class="label '.$label.'"><i class="fa fa-clock-o"></i> '.$tempo.'</small>
					<!-- General tools such as edit or delete-->
					<div class="tools">
						<a href="tarefa.php?op=visualizar&id='.base64_encode($id).'"><i class="fa fa-edit"></i></a>
						<a href="#" onclick="'.$pop->get_action_show().'"><i class="fa fa-trash-o"></i></a>
					</div>
				</li>';
				

				
				


			}
		}

		$cod.= '</ul>';

		
				
		$cod .= '
							
						</div>
						<!-- /.box-body -->
						<div class="box-footer clearfix no-border">
							<a href="tarefa.php" type="button" class="btn btn-default pull-right"><i class="fa fa-plus"></i> Adicionar Tarefa</a>
						</div>
					</div>
					<!-- /.box -->
				</div>
			</div>
		</div>';



		$cod.='
		<script type="text/javascript">
			function tarefa_riscado(tarefa){
				var id = tarefa.split("_");
				id = id[1];
				if($("#tarefa_"+id).is(":checked")){

					var class_tarefa 	= "done";
					var check 			= "true";

				}else{

					var class_tarefa 	= "";
					var check 			= "false";

				}

				$.post("inc/tarefa.inc.php", {
					id_tarefa : id,
					checked : check,
					op : "riscado"
				}, function(data) {
					if (data.length > 0) {
						$("#li_" + id).attr("class", class_tarefa);
					}else{
						alert("Algo deu errado :(");
					}
				});
				
			}

			function tarefa_deletar(tarefa_id){
				
				$.post("inc/tarefa.inc.php", {
					id_tarefa : tarefa_id,
					op : "deletar",
				}, function(data) {
					if (data.length > 0) {
						$("#li_" + tarefa_id).attr("style", "display:none;");
					}else{
						alert("Algo deu errado :(");
					}
				});
				
			}

			function tarefa_show(tarefa_display){

				if(tarefa_display=="ativo"){
					$("#tarefa_ativo").show();
					$("#tarefa_inativo").hide();
				}else{
					$("#tarefa_ativo").hide();
					$("#tarefa_inativo").show();
				}

			}
		</script>
		';

		return $cod;
	}
}




?>